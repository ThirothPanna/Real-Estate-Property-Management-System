<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Property;
use Illuminate\Http\Request;

class AvailablePropertyController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->query('search', ''));

        $query = Property::with('photos')->where('status', 'available');

        if ($search !== '') {
            $query->where(function ($builder) use ($search) {
                $builder->where('name', 'like', "%{$search}%")
                    ->orWhere('address', 'like', "%{$search}%")
                    ->orWhere('city', 'like', "%{$search}%")
                    ->orWhere('state', 'like', "%{$search}%");
            });
        }

        $properties = $query->orderBy('name')->paginate(9)->withQueryString();

        return view('tenant.properties.index', compact('properties', 'search'));
    }

    public function show(Property $property)
    {
        $availableProperty = Property::with('photos')
            ->where('status', 'available')
            ->findOrFail($property->id);

        return view('tenant.properties.show', ['property' => $availableProperty]);
    }
}