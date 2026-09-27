<?php

namespace App\Http\Controllers\Landlord;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Property;
use App\Models\Tenancy;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $landlordId = Auth::id();

        // Get all tenant IDs under this landlord (active + ended so history is complete)
        $tenancies = Tenancy::with(['tenant', 'property'])
            ->where('landlord_id', $landlordId)
            ->get();

        $tenantIds = $tenancies->pluck('user_id')->unique();

        // Base query
        $query = Payment::with('user')
            ->whereIn('user_id', $tenantIds)
            ->latest('paid_on');

        // Tabs
        $tab = $request->get('tab', 'all');
        if (in_array($tab, ['completed', 'pending', 'failed'])) {
            $query->where('status', $tab);
        }

        // Period filter
        $period = $request->get('period', 'year');
        if ($period === 'day') {
            $query->whereDate('paid_on', today());
        } elseif ($period === 'month') {
            $query->whereMonth('paid_on', now()->month)
                  ->whereYear('paid_on', now()->year);
        } elseif ($period === 'year') {
            $query->whereYear('paid_on', now()->year);
        }

        // Property filter
        $propertyId = $request->get('property_id');
        if ($propertyId) {
            $propertyTenantIds = $tenancies->where('property_id', $propertyId)->pluck('user_id')->unique();
            $query->whereIn('user_id', $propertyTenantIds);
        }

        // Search
        $search = trim((string) $request->get('search', ''));
        if ($search !== '') {
            $query->whereHas('user', function ($u) use ($search) {
                $u->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $payments = $query->paginate(20)->withQueryString();

        // Counts for tabs (unfiltered by tab, but filtered by period)
        $baseCountQuery = Payment::whereIn('user_id', $tenantIds);
        if ($period === 'day') {
            $baseCountQuery->whereDate('paid_on', today());
        } elseif ($period === 'month') {
            $baseCountQuery->whereMonth('paid_on', now()->month)->whereYear('paid_on', now()->year);
        } elseif ($period === 'year') {
            $baseCountQuery->whereYear('paid_on', now()->year);
        }

        $counts = [
            'all'       => (clone $baseCountQuery)->count(),
            'completed' => (clone $baseCountQuery)->where('status', 'completed')->count(),
            'pending'   => (clone $baseCountQuery)->where('status', 'pending')->count(),
            'failed'    => (clone $baseCountQuery)->where('status', 'failed')->count(),
        ];

        $totals = [
            'all'       => (clone $baseCountQuery)->sum('amount'),
            'completed' => (clone $baseCountQuery)->where('status', 'completed')->sum('amount'),
            'pending'   => (clone $baseCountQuery)->where('status', 'pending')->sum('amount'),
        ];

        // Properties for the dropdown
        $properties = Property::where('landlord_id', $landlordId)->orderBy('name')->get();

        // Map tenant → property for easy display
        $tenantPropertyMap = $tenancies->keyBy('user_id');

        return view('landlord.payments.index', compact(
            'payments', 'counts', 'totals', 'tab', 'period', 'search',
            'properties', 'propertyId', 'tenantPropertyMap'
        ));
    }
}