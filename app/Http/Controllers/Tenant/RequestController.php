<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\MaintenanceRequest;
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

        MaintenanceRequest::create($validated);

        return back()->with('status', 'Request submitted successfully.');
    }
}