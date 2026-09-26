<?php

namespace App\Http\Controllers\Landlord;

use App\Http\Controllers\Controller;
use App\Models\MaintenanceRequest;
use App\Models\Notification;
use App\Models\Payment;
use App\Models\Property;
use App\Models\Tenancy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $landlordId = Auth::id();
        $period     = $request->get('period', 'month'); // day | month | year

        // ---------- Properties ----------
        $properties = Property::where('landlord_id', $landlordId)->get();
        $totalProps = $properties->count();
        $available  = $properties->where('status', 'available')->count();
        $occupied   = $properties->where('status', 'occupied')->count();

        // ---------- Active tenancies ----------
        $activeTenancies = Tenancy::where('landlord_id', $landlordId)
            ->where('status', 'active')
            ->get();

        $activeTenantCount = $activeTenancies->count();
        $expectedMonthly   = $activeTenancies->sum('rent_amount');
        $tenantIds         = $activeTenancies->pluck('user_id')->unique();

        // ---------- Payments (this month) ----------
        $thisMonthCollected = Payment::whereIn('user_id', $tenantIds)
            ->whereMonth('paid_on', now()->month)
            ->whereYear('paid_on', now()->year)
            ->sum('amount');

        $collectionRate = $expectedMonthly > 0
            ? min(100, round(($thisMonthCollected / $expectedMonthly) * 100))
            : 0;

        // ---------- Requests ----------
        $requestsQuery = MaintenanceRequest::whereIn('user_id', $tenantIds);

        $totalRequests    = (clone $requestsQuery)->count();
        $pendingRequests  = (clone $requestsQuery)->where('status', 'pending')->count();
        $resolvedRequests = (clone $requestsQuery)->where('status', 'resolved')->count();

        $resolvedRate = $totalRequests > 0
            ? round(($resolvedRequests / $totalRequests) * 100)
            : 0;

        $occupancyRate = $totalProps > 0
            ? round(($occupied / $totalProps) * 100)
            : 0;

        // ---------- Chart: date range ----------
        [$startDate, $endDate, $days] = $this->getDateRange($period);

        $labels         = [];
        $incomeSeries   = [];
        $requestsSeries = [];

        for ($i = 0; $i < $days; $i++) {
            $date = $startDate->copy()->addDays($i);
            $labels[] = $date->format('M d');

            $income = Payment::whereIn('user_id', $tenantIds)
                ->whereDate('paid_on', $date)
                ->sum('amount');

            $reqs = MaintenanceRequest::whereIn('user_id', $tenantIds)
                ->whereDate('created_at', $date)
                ->count();

            $incomeSeries[]   = (float) $income;
            $requestsSeries[] = $reqs;
        }

        // ---------- Chart: 12 months revenue ----------
        $monthlyRevenue = [];
        $monthLabels    = [];

        for ($i = 11; $i >= 0; $i--) {
            $month = now()->copy()->subMonths($i);
            $monthLabels[] = $month->format('M');

            $total = Payment::whereIn('user_id', $tenantIds)
                ->whereMonth('paid_on', $month->month)
                ->whereYear('paid_on', $month->year)
                ->sum('amount');

            $monthlyRevenue[] = (float) $total;
        }

        // ---------- Activity ----------
        $recentActivity = Notification::where('user_id', $landlordId)
            ->latest()
            ->take(7)
            ->get();

        return view('landlord.dashboard', compact(
            'totalProps', 'available', 'occupied',
            'activeTenantCount', 'expectedMonthly', 'thisMonthCollected', 'collectionRate',
            'pendingRequests', 'totalRequests', 'resolvedRequests', 'resolvedRate',
            'occupancyRate',
            'labels', 'incomeSeries', 'requestsSeries',
            'monthLabels', 'monthlyRevenue',
            'recentActivity',
            'period'
        ));
    }

    private function getDateRange(string $period): array
    {
        return match ($period) {
            'day'   => [now()->copy(), now()->copy()->addDay(), 24],
            'year'  => [now()->copy()->subDays(364), now()->copy(), 365],
            default => [now()->copy()->subDays(29), now()->copy(), 30],
        };
    }
}