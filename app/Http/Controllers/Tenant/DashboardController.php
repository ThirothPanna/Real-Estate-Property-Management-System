<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\LeaseDocument;
use App\Models\MaintenanceRequest;
use App\Models\Payment;
use Illuminate\Http\Request;
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

        $documents = LeaseDocument::where('user_id', Auth::id())
            ->latest()
            ->get();

        return view('tenant.dashboard', compact('requests', 'payments', 'documents'));
    }

    public function rent(Request $request)
    {
        $userId = Auth::id();

        $year = (int) $request->get('year', date('Y'));

        $allPayments = Payment::where('user_id', $userId)
            ->latest('paid_on')
            ->get();

        $payments = Payment::where('user_id', $userId)
            ->whereYear('paid_on', $year)
            ->latest('paid_on')
            ->paginate(15)
            ->withQueryString();

        $totalPaid = $allPayments->sum('amount');

        $thisYearTotal = $allPayments
            ->where('paid_on', '>=', now()->startOfYear())
            ->sum('amount');

        $lastPayment = $allPayments->first();

        $nextRentDue = $lastPayment
            ? $lastPayment->paid_on->copy()->addMonth()
            : null;

        $years = $allPayments
            ->pluck('paid_on')
            ->map(fn ($d) => $d->year)
            ->unique()
            ->sortDesc()
            ->values();

        return view('tenant.rent', compact(
            'payments',
            'totalPaid',
            'thisYearTotal',
            'lastPayment',
            'nextRentDue',
            'years',
            'year'
        ));
    }

    public function requests()
    {
        $requests = MaintenanceRequest::where('user_id', Auth::id())
            ->latest()
            ->paginate(15);

        return view('tenant.requests', compact('requests'));
    }

    public function utilities()
    {
        return view('tenant.utilities');
    }

    public function applications()
    {
        return view('tenant.applications');
    }

    public function files()
    {
        return view('tenant.files');
    }

    public function downloads()
    {
        return view('tenant.downloads');
    }
}