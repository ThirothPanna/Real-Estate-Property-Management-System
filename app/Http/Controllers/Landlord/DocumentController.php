<?php

namespace App\Http\Controllers\Landlord;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\Notification;
use App\Models\Property;
use App\Models\Tenancy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Services\StoredDocumentPreview;

class DocumentController extends Controller
{
    public function index()
    {
        $landlordId = Auth::id();

        $documents = Document::with('property')
            ->where('landlord_id', $landlordId)
            ->latest()
            ->paginate(15);

        $properties = Property::where('landlord_id', $landlordId)
            ->orderBy('name')
            ->get();

        return view('landlord.documents.index', compact('documents', 'properties'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'       => ['required', 'string', 'max:255'],
            'property_id' => ['nullable', 'exists:properties,id'],
            'document'    => ['required', 'file', 'max:10240', 'mimes:pdf,jpg,jpeg,png,doc,docx'],
        ]);

        // Ownership check
        if ($validated['property_id']) {
            $property = Property::findOrFail($validated['property_id']);
            if ($property->landlord_id !== Auth::id()) {
                abort(403);
            }
        }

        $file = $request->file('document');
        $path = $file->store('documents/' . Auth::id(), 'public');

        Document::create([
            'landlord_id'   => Auth::id(),
            'property_id'   => $validated['property_id'] ?? null,
            'title'         => $validated['title'],
            'file_path'     => $path,
            'original_name' => $file->getClientOriginalName(),
            'mime_type'     => $file->getClientMimeType(),
            'size'          => $file->getSize(),
        ]);

        // Notify tenants of that property (or all)
        $tenantIds = $validated['property_id']
            ? Tenancy::where('property_id', $validated['property_id'])
                ->where('status', 'active')
                ->pluck('user_id')
            : Tenancy::where('landlord_id', Auth::id())
                ->where('status', 'active')
                ->pluck('user_id');

        foreach ($tenantIds as $userId) {
            Notification::create([
                'user_id' => $userId,
                'title'   => '📄 New document shared',
                'body'    => $validated['title'] . ' is now available in your File Manager.',
                'icon'    => 'lease',
            ]);
        }

        return redirect()
            ->route('landlord.documents.index')
            ->with('status', 'Document uploaded successfully.');
    }

    public function destroy(Document $document)
    {
        if ($document->landlord_id !== Auth::id()) {
            abort(403);
        }

        if (Storage::disk('public')->exists($document->file_path)) {
            Storage::disk('public')->delete($document->file_path);
        }

        $document->delete();

        return back()->with('status', 'Document deleted.');
    }

    public function view(Document $document, StoredDocumentPreview $preview)
    {
        if ($document->landlord_id !== Auth::id()) {
            abort(403);
        }

        return $preview->response(
            $document->file_path,
            $document->original_name,
            $document->mime_type,
            route('landlord.documents.download', $document)
        );
    }

    public function download(Document $document)
    {
        if ($document->landlord_id !== Auth::id()) {
            abort(403);
        }

        $storage = Storage::disk('public');
        if (! $storage->exists($document->file_path)) {
            abort(404);
        }

        return $storage->download($document->file_path, $document->original_name);
    }
}