<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\RentReportingInterest;
use Illuminate\Support\Facades\Auth;

class RentReportingController extends Controller
{
    public function store()
    {
        RentReportingInterest::firstOrCreate(['user_id' => Auth::id()]);

        return redirect()
            ->route('tenant.dashboard')
            ->with('status', 'Your interest in rent reporting has been recorded. No payment data has been sent to a credit bureau.');
    }
}