<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\MaintenanceRequest;
use App\Models\Payment;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $requests = MaintenanceRequest::where('user_id', Auth::id())
            ->latest()
            ->get();

        $payments = Payment::where('user_id', Auth::id())
            ->latest('paid_on')
            ->get();

        return view('tenant.dashboard', compact('requests', 'payments'));
    }

    public function notifications()
    {
        return view('tenant.notifications');
    }
}