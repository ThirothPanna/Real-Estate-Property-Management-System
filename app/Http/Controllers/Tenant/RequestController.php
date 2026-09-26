<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\MaintenanceRequest;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RequestController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'       => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:2000'],
            'priority'    => ['required', 'in:low,medium,high,urgent'],
        ]);

        $validated['user_id'] = Auth::id();

        $req = MaintenanceRequest::create($validated);

        Notification::create([
            'user_id' => Auth::id(),
            'title'   => 'Request submitted',
            'body'    => $req->title . ' — Priority: ' . ucfirst($req->priority),
            'icon'    => 'request',
        ]);

        return back()->with('status', 'Request submitted successfully.');
    }
}