<?php

namespace App\Http\Controllers\Landlord;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\Property;
use App\Models\PropertyPhoto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PropertyController extends Controller
{
    public function index()
    {
        $properties = Property::with('photos')
            ->where('landlord_id', Auth::id())
            ->latest()
            ->paginate(12);

        return view('landlord.properties.index', compact('properties'));
    }

    public function create()
    {
        return view('landlord.properties.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'         => ['required', 'string', 'max:255'],
            'address'      => ['required', 'string', 'max:255'],
            'city'         => ['nullable', 'string', 'max:100'],
            'state'        => ['nullable', 'string', 'max:100'],
            'zip'          => ['nullable', 'string', 'max:20'],
            'country'      => ['nullable', 'string', 'max:100'],
            'type'         => ['required', 'in:apartment,house,condo,townhouse,studio,other'],
            'bedrooms'     => ['required', 'integer', 'min:0', 'max:20'],
            'bathrooms'    => ['required', 'integer', 'min:0', 'max:20'],
            'square_feet'  => ['nullable', 'integer', 'min:0', 'max:100000'],
            'rent_amount'  => ['required', 'numeric', 'min:0', 'max:1000000'],
            'description'  => ['nullable', 'string', 'max:2000'],
            'status'       => ['required', 'in:available,occupied,maintenance'],
            'latitude'     => ['nullable', 'numeric', 'between:-90,90'],
            'longitude'    => ['nullable', 'numeric', 'between:-180,180'],
            'photos'       => ['nullable', 'array', 'max:10'],
            'photos.*'     => ['image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'cover_index'  => ['nullable', 'integer', 'min:0'],
        ]);

        $validated['landlord_id'] = Auth::id();

        $property = Property::create($validated);

        $this->storePhotos(
            $property,
            $request->file('photos', []),
            (int) $request->input('cover_index', 0)
        );

        Notification::create([
            'user_id' => Auth::id(),
            'title'   => 'Property added',
            'body'    => $property->name . ' was added to your portfolio.',
            'icon'    => 'lease',
        ]);

        return redirect()
            ->route('landlord.properties.index')
            ->with('status', 'Property added successfully.');
    }

    public function show(Property $property)
    {
        $this->checkOwnership($property);
        $property->load('photos');

        return view('landlord.properties.show', compact('property'));
    }

    public function edit(Property $property)
    {
        $this->checkOwnership($property);
        $property->load('photos');

        return view('landlord.properties.edit', compact('property'));
    }

    public function update(Request $request, Property $property)
    {
        $this->checkOwnership($property);

        $validated = $request->validate([
            'name'         => ['required', 'string', 'max:255'],
            'address'      => ['required', 'string', 'max:255'],
            'city'         => ['nullable', 'string', 'max:100'],
            'state'        => ['nullable', 'string', 'max:100'],
            'zip'          => ['nullable', 'string', 'max:20'],
            'country'      => ['nullable', 'string', 'max:100'],
            'type'         => ['required', 'in:apartment,house,condo,townhouse,studio,other'],
            'bedrooms'     => ['required', 'integer', 'min:0', 'max:20'],
            'bathrooms'    => ['required', 'integer', 'min:0', 'max:20'],
            'square_feet'  => ['nullable', 'integer', 'min:0', 'max:100000'],
            'rent_amount'  => ['required', 'numeric', 'min:0', 'max:1000000'],
            'description'  => ['nullable', 'string', 'max:2000'],
            'status'       => ['required', 'in:available,occupied,maintenance'],
            'latitude'     => ['nullable', 'numeric', 'between:-90,90'],
            'longitude'    => ['nullable', 'numeric', 'between:-180,180'],
            'photos'       => ['nullable', 'array', 'max:10'],
            'photos.*'     => ['image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'cover_index'  => ['nullable', 'integer', 'min:0'],
        ]);

        $property->update($validated);

        $this->storePhotos(
            $property,
            $request->file('photos', []),
            (int) $request->input('cover_index', 0)
        );

        return redirect()
            ->route('landlord.properties.index')
            ->with('status', 'Property updated successfully.');
    }

    public function destroy(Property $property)
    {
        $this->checkOwnership($property);

        foreach ($property->photos as $photo) {
            if (Storage::disk('public')->exists($photo->file_path)) {
                Storage::disk('public')->delete($photo->file_path);
            }
        }

        $name = $property->name;
        $property->delete();

        return redirect()
            ->route('landlord.properties.index')
            ->with('status', $name . ' was deleted.');
    }

    private function storePhotos(Property $property, array $files, int $coverIndex = 0): void
    {
        if (empty($files)) {
            return;
        }

        $maxSort  = (int) $property->photos()->max('sort_order');
        $hasCover = $property->photos()->where('is_cover', true)->exists();

        $created = [];

        foreach ($files as $i => $file) {
            $path = $file->store('properties/' . $property->id, 'public');

            $created[] = PropertyPhoto::create([
                'property_id' => $property->id,
                'file_path'   => $path,
                'is_cover'    => false,
                'sort_order'  => $maxSort + $i + 1,
            ]);
        }

        if (!$hasCover && !empty($created)) {
            $target = $created[$coverIndex] ?? $created[0];
            $target->update(['is_cover' => true]);
        }
    }

    private function checkOwnership(Property $property): void
    {
        if ($property->landlord_id !== Auth::id()) {
            abort(403);
        }
    }
}