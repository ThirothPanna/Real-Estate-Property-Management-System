<?php

namespace App\Http\Controllers\Landlord;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Property;
use App\Models\Tenancy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;


class ReportController extends Controller
{
    public function index(Request $request)
    {
        $landlordId = Auth::id();
        $year       = (int) $request->get('year', date('Y'));

        // ---------- Base data ----------
        $properties = Property::where('landlord_id', $landlordId)->get();
        $tenancies  = Tenancy::with(['tenant', 'property'])
            ->where('landlord_id', $landlordId)
            ->get();

        $tenantIds = $tenancies->pluck('user_id')->unique();

        // ---------- KPI cards ----------
        $totalRevenue = Payment::whereIn('user_id', $tenantIds)
            ->where('status', 'completed')
            ->whereYear('paid_on', $year)
            ->sum('amount');

        $expectedYearly = $tenancies->where('status', 'active')->sum('rent_amount') * 12;

        $collectionRate = $expectedYearly > 0
            ? min(100, round(($totalRevenue / $expectedYearly) * 100))
            : 0;

        $occupancyRate = $properties->count() > 0
            ? round(($tenancies->where('status', 'active')->count() / $properties->count()) * 100)
            : 0;

        $avgRent = $properties->count() > 0
            ? round($properties->avg('rent_amount'), 2)
            : 0;

        // ---------- Monthly revenue (12 months of the selected year) ----------
        $monthlyRevenue = [];
        $monthLabels    = [];

        for ($m = 1; $m <= 12; $m++) {
            $monthLabels[] = date('M', mktime(0, 0, 0, $m, 1));

            $total = Payment::whereIn('user_id', $tenantIds)
                ->where('status', 'completed')
                ->whereYear('paid_on', $year)
                ->whereMonth('paid_on', $m)
                ->sum('amount');

            $monthlyRevenue[] = (float) $total;
        }

        // ---------- Occupancy over time (for the year) ----------
        $occupancyByMonth = [];

        for ($m = 1; $m <= 12; $m++) {
            $monthStart = now()->setDate($year, $m, 1)->startOfMonth();
            $monthEnd   = $monthStart->copy()->endOfMonth();

            // Count how many tenancies were active in this month
            $activeCount = $tenancies->filter(function ($t) use ($monthStart, $monthEnd) {
                return $t->lease_start <= $monthEnd && $t->lease_end >= $monthStart;
            })->count();

            $occupancyByMonth[] = $properties->count() > 0
                ? round(($activeCount / $properties->count()) * 100)
                : 0;
        }

        // ---------- Top properties by revenue ----------
        $topProperties = [];

        foreach ($properties as $prop) {
            // Find all tenancies for this property
            $propTenantIds = $tenancies
                ->where('property_id', $prop->id)
                ->pluck('user_id')
                ->unique();

            $revenue = Payment::whereIn('user_id', $propTenantIds)
                ->where('status', 'completed')
                ->whereYear('paid_on', $year)
                ->sum('amount');

            $topProperties[] = [
                'property' => $prop,
                'revenue'  => (float) $revenue,
                'tenants'  => $propTenantIds->count(),
            ];
        }

        // Sort by revenue descending, take top 5
        usort($topProperties, fn ($a, $b) => $b['revenue'] <=> $a['revenue']);
        $topProperties = array_slice($topProperties, 0, 5);

        // ---------- Available years ----------


$years = Payment::whereIn('user_id', $tenantIds)
    ->where('status', 'completed')
    ->whereNotNull('paid_on')
    ->selectRaw('YEAR(paid_on) as y')
    ->distinct()
    ->orderBy('y', 'desc')
    ->pluck('y')
    ->filter()
    ->values();

if ($years->isEmpty()) {
    $years = collect([(int) date('Y')]);
}

        return view('landlord.reports.index', compact(
            'totalRevenue', 'occupancyRate', 'avgRent', 'collectionRate',
            'monthlyRevenue', 'monthLabels',
            'occupancyByMonth',
            'topProperties',
            'year', 'years'
        ));
    }

    public function exportCsv(Request $request)
    {
        $landlordId = Auth::id();
        $year       = (int) $request->get('year', date('Y'));

        $tenancies  = Tenancy::where('landlord_id', $landlordId)->get();
        $tenantIds  = $tenancies->pluck('user_id')->unique();
        $properties = Property::where('landlord_id', $landlordId)->get();

        $filename = "landlord-reports-{$year}-" . date('Y-m-d') . ".csv";

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($year, $tenantIds, $properties, $tenancies) {
            $handle = fopen('php://output', 'w');

            // Section 1: Monthly Revenue
            fputcsv($handle, ['Monthly Revenue — ' . $year]);
            fputcsv($handle, ['Month', 'Revenue (USD)']);

            for ($m = 1; $m <= 12; $m++) {
                $monthName = date('F', mktime(0, 0, 0, $m, 1));
                $total = Payment::whereIn('user_id', $tenantIds)
                    ->where('status', 'completed')
                    ->whereYear('paid_on', $year)
                    ->whereMonth('paid_on', $m)
                    ->sum('amount');
                fputcsv($handle, [$monthName, number_format($total, 2, '.', '')]);
            }

            fputcsv($handle, []);

            // Section 2: Top Properties
            fputcsv($handle, ['Top Properties by Revenue']);
            fputcsv($handle, ['Property', 'Address', 'Tenants', 'Revenue (USD)']);

            foreach ($properties as $prop) {
                $propTenantIds = $tenancies->where('property_id', $prop->id)->pluck('user_id')->unique();
                $revenue = Payment::whereIn('user_id', $propTenantIds)
                    ->where('status', 'completed')
                    ->whereYear('paid_on', $year)
                    ->sum('amount');

                fputcsv($handle, [
                    $prop->name,
                    $prop->full_address,
                    $propTenantIds->count(),
                    number_format($revenue, 2, '.', ''),
                ]);
            }

            fputcsv($handle, []);

            // Section 3: Summary
            fputcsv($handle, ['Summary']);
            fputcsv($handle, ['Metric', 'Value']);

            $totalRevenue = Payment::whereIn('user_id', $tenantIds)
                ->where('status', 'completed')
                ->whereYear('paid_on', $year)
                ->sum('amount');

            fputcsv($handle, ['Total Revenue', number_format($totalRevenue, 2, '.', '')]);
            fputcsv($handle, ['Total Properties', $properties->count()]);
            fputcsv($handle, ['Active Tenancies', $tenancies->where('status', 'active')->count()]);

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }
}