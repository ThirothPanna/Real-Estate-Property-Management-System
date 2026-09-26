<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $userId = Auth::id();

        $tab = $request->get('tab', 'all');
        if (!in_array($tab, ['all', 'unread', 'read'])) {
            $tab = 'all';
        }

        $query = Notification::where('user_id', $userId)->latest();

        if ($tab === 'unread') {
            $query->unread();
        } elseif ($tab === 'read') {
            $query->read();
        }

        $search = trim((string) $request->get('search', ''));
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('body', 'like', "%{$search}%");
            });
        }

        $from = $request->get('from');
        $to   = $request->get('to');

        if ($from) {
            $query->whereDate('created_at', '>=', $from);
        }
        if ($to) {
            $query->whereDate('created_at', '<=', $to);
        }

        $notifications = $query->paginate(15)->withQueryString();

        $counts = [
            'all'    => Notification::where('user_id', $userId)->count(),
            'unread' => Notification::where('user_id', $userId)->unread()->count(),
            'read'   => Notification::where('user_id', $userId)->read()->count(),
        ];

        return view('tenant.notifications', compact(
            'notifications', 'tab', 'counts', 'search', 'from', 'to'
        ));
    }

    public function markRead(Notification $notification)
    {
        $this->checkOwnership($notification);

        if ($notification->isUnread()) {
            $notification->update(['read_at' => now()]);
        }

        return back()->with('status', 'Marked as read.');
    }

    public function markUnread(Notification $notification)
    {
        $this->checkOwnership($notification);

        $notification->update(['read_at' => null]);

        return back()->with('status', 'Marked as unread.');
    }

    public function markAllRead()
    {
        Notification::where('user_id', Auth::id())
            ->unread()
            ->update(['read_at' => now()]);

        return back()->with('status', 'All notifications marked as read.');
    }

    public function markAllUnread()
    {
        Notification::where('user_id', Auth::id())
            ->update(['read_at' => null]);

        return back()->with('status', 'All notifications marked as unread.');
    }

    public function destroy(Notification $notification)
    {
        $this->checkOwnership($notification);

        $notification->delete();

        return back()->with('status', 'Notification deleted.');
    }

    public function destroyAll()
    {
        Notification::where('user_id', Auth::id())->delete();

        return back()->with('status', 'All notifications deleted.');
    }

    private function checkOwnership(Notification $notification): void
    {
        if ($notification->user_id !== Auth::id()) {
            abort(403);
        }
    }
}