<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Lease;
use App\Models\LeaseDocument;
use App\Models\MaintenanceRequest;
use App\Models\Document;
use App\Models\Payment;
use App\Models\RentReportingInterest;
use App\Models\Tenancy;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $userId = Auth::id();

        $requests = MaintenanceRequest::where('user_id', $userId)
            ->latest()
            ->get();

        $payments = Payment::where('user_id', $userId)
            ->latest('paid_on')
            ->get();
        $completedPayments = $payments->where('status', 'completed');

        $documents = LeaseDocument::where('user_id', $userId)
            ->latest()
            ->get();

        $leases = Lease::where('user_id', $userId)
            ->latest()
            ->get();

        $rentReportingRegistered = RentReportingInterest::where('user_id', $userId)->exists();

        return view('tenant.dashboard', compact('requests', 'payments', 'completedPayments', 'documents', 'leases', 'rentReportingRegistered'));
    }

    public function rent()
    {
        $userId = Auth::id();

        $tenancies = Tenancy::with('property.photos')
            ->where('user_id', $userId)
            ->where('status', 'active')
            ->latest('lease_start')
            ->get();

        $allPayments = Payment::where('user_id', $userId)
            ->where('status', 'completed')
            ->latest('paid_on')
            ->get();

        $year = (int) request()->get('year', date('Y'));

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
            'year',
            'tenancies'
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
        $userId = Auth::id();

        $sharedDocuments = Document::where(function ($query) use ($userId) {
                $query->whereHas('property.tenancies', function ($tenancies) use ($userId) {
                    $tenancies->where('user_id', $userId)->where('status', 'active');
                })->orWhere(function ($unassigned) use ($userId) {
                    $unassigned->whereNull('property_id')
                        ->whereHas('landlord.landlordTenancies', function ($tenancies) use ($userId) {
                            $tenancies->where('user_id', $userId)->where('status', 'active');
                        });
                });
            })
            ->latest()
            ->get();

        $documents = LeaseDocument::where('user_id', $userId)
            ->latest()
            ->get();

        $leases = Lease::where('user_id', $userId)
            ->latest()
            ->get();

        $payments = Payment::where('user_id', $userId)
            ->where('status', 'completed')
            ->latest('paid_on')
            ->get();

        return view('tenant.files', compact('documents', 'sharedDocuments', 'leases', 'payments'));
    }

    public function downloads()
    {
        $userId = Auth::id();

        $payments = Payment::where('user_id', $userId)
            ->where('status', 'completed')
            ->latest('paid_on')
            ->get();

        $leases = Lease::where('user_id', $userId)
            ->latest()
            ->get();

        $documents = LeaseDocument::where('user_id', $userId)
            ->latest()
            ->get();

        return view('tenant.downloads', compact('payments', 'leases', 'documents'));
    }

    public function notifications()
    {
        return view('tenant.notifications');
    }
}