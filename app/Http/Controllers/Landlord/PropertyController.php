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
        $validated = $this->validateProperty($request);

        $validated['landlord_id'] = Auth::id();

        $property = Property::create($validated);

        $this->savePhotos($property, $request->file('photos', []), $request);

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

        $validated = $this->validateProperty($request);

        $property->update($validated);

        $this->savePhotos($property, $request->file('photos', []), $request);

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

    /* ==================== Helpers ==================== */

    private function validateProperty(Request $request): array
    {
        return $request->validate([
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
    }

    private function savePhotos(Property $property, array $files, Request $request): void
    {
        if (empty($files)) {
            return;
        }

        $coverIdx = (int) $request->input('cover_index', 0);
        $maxSort  = (int) $property->photos()->max('sort_order');
        $hasCover = $property->photos()->where('is_cover', true)->exists();

        foreach ($files as $i => $file) {
            $path = $file->store('properties/' . $property->id, 'public');

            PropertyPhoto::create([
                'property_id' => $property->id,
                'file_path'   => $path,
                'is_cover'    => !$hasCover && $i === $coverIdx,
                'sort_order'  => $maxSort + $i + 1,
            ]);

            if (!$hasCover && $i === $coverIdx) {
                $hasCover = true;
            }
        }

        if (!$property->photos()->where('is_cover', true)->exists()) {
            $first = $property->photos()->orderBy('sort_order')->first();
            if ($first) {
                $first->update(['is_cover' => true]);
            }
        }
    }

    private function checkOwnership(Property $property): void
    {
        if ($property->landlord_id !== Auth::id()) {
            abort(403);
        }
    }
}