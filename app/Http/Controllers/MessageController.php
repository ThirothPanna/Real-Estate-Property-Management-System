<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\Property;
use App\Models\Tenancy;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MessageController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $routePrefix = $this->routePrefix();

        $tenancies = Tenancy::with(['tenant', 'landlord', 'property'])
            ->where($user->isTenant() ? 'user_id' : 'landlord_id', $user->id)
            ->withMax('messages', 'created_at')
            ->orderByDesc('messages_max_created_at')
            ->orderByDesc('updated_at')
            ->get();

        $inquiryMessages = Message::with(['sender', 'recipient', 'property'])
            ->whereNull('tenancy_id')
            ->where(function ($query) use ($user) {
                $query->where('sender_id', $user->id)->orWhere('recipient_id', $user->id);
            })
            ->latest()
            ->get();

        $inquiries = $inquiryMessages
            ->groupBy(function (Message $message) {
                $tenantId = $message->sender->isTenant() ? $message->sender_id : $message->recipient_id;
                $landlordId = $message->sender->isLandlord() ? $message->sender_id : $message->recipient_id;

                return $message->property_id . ':' . $tenantId . ':' . $landlordId;
            })
            ->map(function ($thread) {
                $latest = $thread->first();
                $tenant = $latest->sender->isTenant() ? $latest->sender : $latest->recipient;
                $landlord = $latest->sender->isLandlord() ? $latest->sender : $latest->recipient;

                return (object) [
                    'property' => $latest->property,
                    'tenant' => $tenant,
                    'landlord' => $landlord,
                    'latest' => $latest,
                ];
            })
            ->values();

        return view('messages.index', compact('tenancies', 'inquiries', 'routePrefix'));
    }

    public function show(Tenancy $tenancy)
    {
        $this->ensureParticipant($tenancy);
        $tenancy->load(['tenant', 'landlord', 'property']);
        $routePrefix = $this->routePrefix();

        Message::where('tenancy_id', $tenancy->id)
            ->where('sender_id', '!=', Auth::id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        $messages = $tenancy->messages()->with('sender')->orderBy('created_at')->get();

        return view('messages.show', compact('tenancy', 'messages', 'routePrefix'));
    }

    public function profile(Tenancy $tenancy)
    {
        $this->ensureParticipant($tenancy);
        $tenancy->load(['tenant', 'landlord', 'property']);

        $person = Auth::user()->isTenant() ? $tenancy->landlord : $tenancy->tenant;
        $routePrefix = $this->routePrefix();

        return view('messages.profile', compact('tenancy', 'person', 'routePrefix'));
    }

    public function showPropertyInquiry(Property $property)
    {
        abort_unless(Auth::user()->isTenant(), 403);
        $property->load('landlord');

        $messages = $this->propertyInquiryMessages($property, Auth::user(), $property->landlord);
        abort_unless($property->status === 'available' || (clone $messages)->exists(), 404);

        return $this->renderPropertyInquiry($property, $property->landlord, $messages);
    }

    public function storePropertyInquiry(Request $request, Property $property)
    {
        abort_unless(Auth::user()->isTenant(), 403);
        $property->load('landlord');
        $query = $this->propertyInquiryMessages($property, Auth::user(), $property->landlord);
        abort_unless($property->status === 'available' || (clone $query)->exists(), 404);

        return $this->savePropertyInquiry($request, $property, Auth::user(), $property->landlord);
    }

    public function showLandlordInquiry(Property $property, User $tenant)
    {
        $this->ensureLandlordInquiryAccess($property, $tenant);
        $query = $this->propertyInquiryMessages($property, $tenant, Auth::user());
        abort_unless((clone $query)->exists(), 404);

        return $this->renderPropertyInquiry($property, $tenant, $query);
    }

    public function storeLandlordInquiry(Request $request, Property $property, User $tenant)
    {
        $this->ensureLandlordInquiryAccess($property, $tenant);
        abort_unless($this->propertyInquiryMessages($property, $tenant, Auth::user())->exists(), 404);

        return $this->savePropertyInquiry($request, $property, $tenant, Auth::user());
    }

    public function store(Request $request, Tenancy $tenancy)
    {
        $this->ensureParticipant($tenancy);

        $messageData = $this->validatedMessageData($request);
        Message::create($messageData + [
            'tenancy_id' => $tenancy->id,
            'sender_id' => Auth::id(),
        ]);

        return redirect()
            ->route($this->routePrefix() . '.messages.show', $tenancy)
            ->withFragment('latest-message');
    }

    private function ensureParticipant(Tenancy $tenancy): void
    {
        $user = Auth::user();
        $participantId = $user->isTenant() ? $tenancy->user_id : $tenancy->landlord_id;

        abort_unless($participantId === $user->id, 403);
    }

    private function routePrefix(): string
    {
        return Auth::user()->isTenant() ? 'tenant' : 'landlord';
    }

    private function ensureLandlordInquiryAccess(Property $property, User $tenant): void
    {
        abort_unless(Auth::user()->isLandlord() && $property->landlord_id === Auth::id(), 403);
        abort_unless($tenant->isTenant(), 404);
    }

    private function propertyInquiryMessages(Property $property, User $tenant, User $landlord)
    {
        return Message::with('sender')
            ->whereNull('tenancy_id')
            ->where('property_id', $property->id)
            ->where(function ($query) use ($tenant, $landlord) {
                $query->where(function ($pair) use ($tenant, $landlord) {
                    $pair->where('sender_id', $tenant->id)->where('recipient_id', $landlord->id);
                })->orWhere(function ($pair) use ($tenant, $landlord) {
                    $pair->where('sender_id', $landlord->id)->where('recipient_id', $tenant->id);
                });
            })
            ->orderBy('created_at');
    }

    private function renderPropertyInquiry(Property $property, User $otherPerson, $query)
    {
        $query->where('sender_id', '!=', Auth::id())->whereNull('read_at')->update(['read_at' => now()]);
        $messages = $this->propertyInquiryMessages(
            $property,
            Auth::user()->isTenant() ? Auth::user() : $otherPerson,
            Auth::user()->isLandlord() ? Auth::user() : $otherPerson
        )->get();

        $routePrefix = $this->routePrefix();
        $sendUrl = Auth::user()->isTenant()
            ? route('tenant.inquiries.store', $property)
            : route('landlord.inquiries.store', [$property, $otherPerson]);

        return view('messages.inquiry', compact('property', 'otherPerson', 'messages', 'routePrefix', 'sendUrl'));
    }

    private function savePropertyInquiry(Request $request, Property $property, User $tenant, User $landlord)
    {
        $messageData = $this->validatedMessageData($request);

        Message::create($messageData + [
            'property_id' => $property->id,
            'sender_id' => Auth::id(),
            'recipient_id' => Auth::id() === $tenant->id ? $landlord->id : $tenant->id,
        ]);

        $destination = Auth::user()->isTenant()
            ? route('tenant.inquiries.show', $property)
            : route('landlord.inquiries.show', [$property, $tenant]);

        return redirect($destination . '#latest-message');
    }

    public function attachment(Message $message)
    {
        $user = Auth::user();
        abort_unless($user->isTenant() || $user->isLandlord(), 403);

        if ($message->tenancy_id) {
            $tenancy = $message->tenancy;
            $participantId = $user->isTenant() ? $tenancy->user_id : $tenancy->landlord_id;
            abort_unless($participantId === $user->id, 403);
        } else {
            abort_unless($message->sender_id === $user->id || $message->recipient_id === $user->id, 403);
        }

        abort_unless($message->attachment_path && Storage::disk('local')->exists($message->attachment_path), 404);

        return response()->file(Storage::disk('local')->path($message->attachment_path), [
            'Content-Type' => $message->attachment_mime ?: 'application/octet-stream',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    private function validatedMessageData(Request $request): array
    {
        $validated = $request->validate([
            'body' => ['nullable', 'required_without:attachment', 'string', 'max:5000'],
            'attachment' => [
                'nullable',
                'file',
                'max:20480',
                'mimetypes:image/jpeg,image/png,image/webp,image/gif,image/avif,image/heic,image/heif,audio/mpeg,audio/mp4,audio/ogg,audio/wav,audio/x-wav,audio/webm,audio/aac,audio/3gpp,video/webm',
            ],
        ]);

        $data = ['body' => $validated['body'] ?? ''];
        $file = $request->file('attachment');

        if ($file) {
            $mime = $file->getMimeType();
            $clientMime = $file->getClientMimeType();

            // PHP may identify an audio-only MediaRecorder WebM as video/webm.
            if ($mime === 'video/webm' && str_starts_with($clientMime, 'audio/')) {
                $mime = 'audio/webm';
            }

            $data['attachment_path'] = $file->store('messages', 'local');
            $data['attachment_mime'] = $mime;
            $data['attachment_name'] = mb_substr($file->getClientOriginalName(), 0, 255);
        }

        return $data;
    }
}
