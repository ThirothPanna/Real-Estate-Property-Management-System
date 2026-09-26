@extends('layouts.app')

@section('title', 'Edit ' . $property->name . ' – NEKJOUL IMANAGE')

@section('content')

    <div style="max-width:820px; margin:0 auto;">

        <div style="margin-bottom:24px;">
            <a href="{{ route('landlord.properties.index') }}" style="font-size:13px; color:#6b7280; text-decoration:none;">← Back to Properties</a>
            <h1 style="font-size:24px; font-weight:700; margin-top:8px; margin-bottom:4px;">Edit Property</h1>
            <p style="color:#6b7280; font-size:14px;">Update {{ $property->name }}.</p>
        </div>

        @if (session('status'))
            <div style="background:#dcfce7; color:#16a34a; padding:12px 16px; border-radius:10px; margin-bottom:16px; font-size:14px;">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('landlord.properties.update', $property) }}" enctype="multipart/form-data">
            @csrf
            @method('PATCH')

            {{-- Basic Info --}}
            <div class="panel">
                <h3>Basic Information</h3>
                <div>
                    <label style="display:block; font-size:12px; font-weight:600; color:#6b7280; text-transform:uppercase; letter-spacing:.4px; margin-bottom:6px;">Property Name *</label>
                    <input type="text" name="name" required value="{{ old('name', $property->name) }}"
                           style="width:100%; padding:12px 14px; border:1px solid #e5e7eb; border-radius:10px; font-size:14px; outline:none;">
                </div>
            </div>

            {{-- Address --}}
            <div class="panel">
                <h3>Address</h3>
                <div style="display:grid; grid-template-columns:1fr; gap:18px;">
                    <div>
                        <label style="display:block; font-size:12px; font-weight:600; color:#6b7280; text-transform:uppercase; letter-spacing:.4px; margin-bottom:6px;">Street Address *</label>
                        <input type="text" name="address" required value="{{ old('address', $property->address) }}"
                               style="width:100%; padding:12px 14px; border:1px solid #e5e7eb; border-radius:10px; font-size:14px; outline:none;">
                    </div>
                    <div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:14px;">
                        <div>
                            <label style="display:block; font-size:12px; font-weight:600; color:#6b7280; text-transform:uppercase; letter-spacing:.4px; margin-bottom:6px;">City</label>
                            <input type="text" name="city" value="{{ old('city', $property->city) }}"
                                   style="width:100%; padding:12px 14px; border:1px solid #e5e7eb; border-radius:10px; font-size:14px;">
                        </div>
                        <div>
                            <label style="display:block; font-size:12px; font-weight:600; color:#6b7280; text-transform:uppercase; letter-spacing:.4px; margin-bottom:6px;">State</label>
                            <input type="text" name="state" value="{{ old('state', $property->state) }}"
                                   style="width:100%; padding:12px 14px; border:1px solid #e5e7eb; border-radius:10px; font-size:14px;">
                        </div>
                        <div>
                            <label style="display:block; font-size:12px; font-weight:600; color:#6b7280; text-transform:uppercase; letter-spacing:.4px; margin-bottom:6px;">Zip</label>
                            <input type="text" name="zip" value="{{ old('zip', $property->zip) }}"
                                   style="width:100%; padding:12px 14px; border:1px solid #e5e7eb; border-radius:10px; font-size:14px;">
                        </div>
                    </div>
                    <div>
                        <label style="display:block; font-size:12px; font-weight:600; color:#6b7280; text-transform:uppercase; letter-spacing:.4px; margin-bottom:6px;">Country</label>
                        <input type="text" name="country" value="{{ old('country', $property->country) }}"
                               style="width:100%; padding:12px 14px; border:1px solid #e5e7eb; border-radius:10px; font-size:14px;">
                    </div>
                </div>
            </div>

            {{-- Details --}}
            <div class="panel">
                <h3>Property Details</h3>
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:18px;">
                    <div>
                        <label style="display:block; font-size:12px; font-weight:600; color:#6b7280; text-transform:uppercase; letter-spacing:.4px; margin-bottom:6px;">Type *</label>
                        <select name="type" required style="width:100%; padding:12px 14px; border:1px solid #e5e7eb; border-radius:10px; font-size:14px; background:#fff;">
                            @foreach (['apartment' => 'Apartment', 'house' => 'House', 'condo' => 'Condo', 'townhouse' => 'Townhouse', 'studio' => 'Studio', 'other' => 'Other'] as $k => $v)
                                <option value="{{ $k }}" {{ old('type', $property->type) === $k ? 'selected' : '' }}>{{ $v }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label style="display:block; font-size:12px; font-weight:600; color:#6b7280; text-transform:uppercase; letter-spacing:.4px; margin-bottom:6px;">Status *</label>
                        <select name="status" required style="width:100%; padding:12px 14px; border:1px solid #e5e7eb; border-radius:10px; font-size:14px; background:#fff;">
                            <option value="available"   {{ old('status', $property->status) === 'available'   ? 'selected' : '' }}>Available</option>
                            <option value="occupied"    {{ old('status', $property->status) === 'occupied'    ? 'selected' : '' }}>Occupied</option>
                            <option value="maintenance" {{ old('status', $property->status) === 'maintenance' ? 'selected' : '' }}>Under Maintenance</option>
                        </select>
                    </div>
                    <div>
                        <label style="display:block; font-size:12px; font-weight:600; color:#6b7280; text-transform:uppercase; letter-spacing:.4px; margin-bottom:6px;">Bedrooms *</label>
                        <input type="number" name="bedrooms" min="0" max="20" required value="{{ old('bedrooms', $property->bedrooms) }}"
                               style="width:100%; padding:12px 14px; border:1px solid #e5e7eb; border-radius:10px; font-size:14px;">
                    </div>
                    <div>
                        <label style="display:block; font-size:12px; font-weight:600; color:#6b7280; text-transform:uppercase; letter-spacing:.4px; margin-bottom:6px;">Bathrooms *</label>
                        <input type="number" name="bathrooms" min="0" max="20" required value="{{ old('bathrooms', $property->bathrooms) }}"
                               style="width:100%; padding:12px 14px; border:1px solid #e5e7eb; border-radius:10px; font-size:14px;">
                    </div>
                    <div>
                        <label style="display:block; font-size:12px; font-weight:600; color:#6b7280; text-transform:uppercase; letter-spacing:.4px; margin-bottom:6px;">Square Feet</label>
                        <input type="number" name="square_feet" min="0" value="{{ old('square_feet', $property->square_feet) }}"
                               style="width:100%; padding:12px 14px; border:1px solid #e5e7eb; border-radius:10px; font-size:14px;">
                    </div>
                    <div>
                        <label style="display:block; font-size:12px; font-weight:600; color:#6b7280; text-transform:uppercase; letter-spacing:.4px; margin-bottom:6px;">Monthly Rent (USD) *</label>
                        <input type="number" name="rent_amount" step="0.01" min="0" required value="{{ old('rent_amount', $property->rent_amount) }}"
                               style="width:100%; padding:12px 14px; border:1px solid #e5e7eb; border-radius:10px; font-size:14px;">
                    </div>
                </div>
                <div style="margin-top:18px;">
                    <label style="display:block; font-size:12px; font-weight:600; color:#6b7280; text-transform:uppercase; letter-spacing:.4px; margin-bottom:6px;">Description</label>
                    <textarea name="description" rows="4"
                              style="width:100%; padding:12px 14px; border:1px solid #e5e7eb; border-radius:10px; font-size:14px; resize:vertical; font-family:inherit;">{{ old('description', $property->description) }}</textarea>
                </div>
            </div>

            {{-- Existing Photos --}}
            <div class="panel">
                <h3>Current Photos ({{ $property->photos->count() }} / 10)</h3>
                <p style="color:#6b7280; font-size:13px; margin-bottom:14px;">
                    Click ★ to set a photo as cover. Click ✕ to delete.
                </p>

                @if ($property->photos->isEmpty())
                    <div style="color:#9ca3af; font-size:13px; padding:20px; text-align:center; border:2px dashed #e5e7eb; border-radius:10px;">
                        No photos yet. Add some below.
                    </div>
                @else
                    <div style="display:flex; gap:12px; flex-wrap:wrap;">
                        @foreach ($property->photos as $photo)
                            <div style="width:120px; height:120px; border-radius:12px; overflow:hidden; position:relative; border:2px solid {{ $photo->is_cover ? '#22c55e' : '#e5e7eb' }};">
                                <img src="{{ $photo->url }}" style="width:100%;height:100%;object-fit:cover;display:block;">

                                {{-- Set cover --}}
                                @if (!$photo->is_cover)
                                    <form method="POST" action="{{ route('landlord.properties.photos.cover', $photo) }}" style="margin:0;">
                                        @csrf @method('PATCH')
                                        <button type="submit" title="Set as cover"
                                                style="position:absolute;top:6px;left:6px;background:#fff;border:1px solid #e5e7eb;width:22px;height:22px;border-radius:50%;cursor:pointer;font-size:12px;line-height:1;color:#9ca3af;">☆</button>
                                    </form>
                                @else
                                    <div style="position:absolute;top:6px;left:6px;background:#22c55e;color:#fff;padding:2px 8px;border-radius:10px;font-size:10px;font-weight:700;">★ Cover</div>
                                @endif

                                {{-- Delete --}}
                                <form method="POST" action="{{ route('landlord.properties.photos.destroy', $photo) }}"
                                      onsubmit="return confirm('Delete this photo?');" style="margin:0;">
                                    @csrf @method('DELETE')
                                    <button type="submit" title="Delete"
                                            style="position:absolute;top:6px;right:6px;background:#ef4444;color:#fff;border:none;width:22px;height:22px;border-radius:50%;cursor:pointer;font-size:12px;line-height:1;">✕</button>
                                </form>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Add New Photos --}}
            <div class="panel">
                <h3>Add New Photos</h3>
                <p style="color:#6b7280; font-size:13px; margin-bottom:14px;">
                    JPG, PNG, WEBP — max 4 MB each. Up to 10 total per property.
                </p>

                <input type="file" name="photos[]" id="photoInput" accept="image/*" multiple style="display:none;">

                <div id="photoPreviewGrid" style="display:flex; gap:12px; flex-wrap:wrap;"></div>

                <div onclick="document.getElementById('photoInput').click()"
                     style="width:120px; height:120px; border-radius:12px; border:2px dashed #d1d5db; display:inline-flex; align-items:center; justify-content:center; flex-direction:column; gap:4px; cursor:pointer; color:#9ca3af; background:#f9fafb; margin-top:12px;">
                    <span style="font-size:24px;">📎</span>
                    <span style="font-size:11px;">Add photos</span>
                </div>
            </div>

            {{-- Location --}}
            <div class="panel">
                <h3>Location</h3>
                <p style="color:#6b7280; font-size:13px; margin-bottom:14px;">
                    The map updates live as you change the address above.
                </p>

                <div style="border-radius:14px; overflow:hidden; border:1px solid #e5e7eb; margin-bottom:14px;">
                    <iframe id="mapPreview"
                            src="{{ $property->map_embed_url }}"
                            width="100%" height="320"
                            style="border:0; display:block;"
                            allowfullscreen=""
                            loading="lazy"
                            referrerpolicy="no-referrer-when-downgrade"></iframe>
                </div>

                <div style="background:#f9fafb; padding:14px; border-radius:10px; border:1px solid #e5e7eb;">
                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:14px;">
                        <div>
                            <label style="display:block; font-size:12px; font-weight:600; color:#6b7280; text-transform:uppercase; letter-spacing:.4px; margin-bottom:6px;">Latitude</label>
                            <input type="text" name="latitude" id="latField" value="{{ old('latitude', $property->latitude) }}"
                                   style="width:100%; padding:10px 12px; border:1px solid #e5e7eb; border-radius:8px; font-size:13px; outline:none;">
                        </div>
                        <div>
                            <label style="display:block; font-size:12px; font-weight:600; color:#6b7280; text-transform:uppercase; letter-spacing:.4px; margin-bottom:6px;">Longitude</label>
                            <input type="text" name="longitude" id="lngField" value="{{ old('longitude', $property->longitude) }}"
                                   style="width:100%; padding:10px 12px; border:1px solid #e5e7eb; border-radius:8px; font-size:13px; outline:none;">
                        </div>
                    </div>
                </div>
            </div>

            <div style="display:flex; justify-content:flex-end; gap:12px; margin-bottom:40px;">
                <a href="{{ route('landlord.properties.index') }}" class="btn btn-outline">Cancel</a>
                <button type="submit" class="btn btn-primary">Save Changes</button>
            </div>
        </form>
    </div>

    <script>
        var selectedFiles = [];

        document.getElementById('photoInput').addEventListener('change', function () {
            Array.from(this.files).forEach(f => selectedFiles.push(f));
            renderPhotoGrid();
            this.value = '';
        });

        function renderPhotoGrid() {
            var grid = document.getElementById('photoPreviewGrid');
            grid.innerHTML = '';

            selectedFiles.forEach(function (file, i) {
                var url = URL.createObjectURL(file);

                var tile = document.createElement('div');
                tile.style.cssText = 'width:120px;height:120px;border-radius:12px;overflow:hidden;position:relative;border:2px solid #e5e7eb;background:#fff;';

                var img = document.createElement('img');
                img.src = url;
                img.style.cssText = 'width:100%;height:100%;object-fit:cover;display:block;';
                tile.appendChild(img);

                var remove = document.createElement('button');
                remove.type = 'button';
                remove.textContent = '✕';
                remove.style.cssText = 'position:absolute;top:6px;right:6px;background:#ef4444;color:#fff;border:none;width:22px;height:22px;border-radius:50%;cursor:pointer;font-size:12px;line-height:1;';
                remove.onclick = function (e) { e.stopPropagation(); selectedFiles.splice(i, 1); renderPhotoGrid(); };
                tile.appendChild(remove);

                grid.appendChild(tile);
            });
        }

        // Live map
        function buildAddress() {
            var parts = [
                document.querySelector('input[name="address"]')?.value || '',
                document.querySelector('input[name="city"]')?.value || '',
                document.querySelector('input[name="state"]')?.value || '',
                document.querySelector('input[name="zip"]')?.value || '',
                document.querySelector('input[name="country"]')?.value || '',
            ].filter(p => p.trim() !== '');
            return parts.join(', ');
        }

        function updateMap() {
            var address = buildAddress();
            if (!address) return;

            var lat = document.getElementById('latField').value.trim();
            var lng = document.getElementById('lngField').value.trim();

            var query = (lat && lng) ? (lat + ',' + lng) : address;
            document.getElementById('mapPreview').src = 'https://www.google.com/maps?q=' + encodeURIComponent(query) + '&output=embed';
        }

        ['address', 'city', 'state', 'zip', 'country'].forEach(function (field) {
            var input = document.querySelector('input[name="' + field + '"]');
            if (input) input.addEventListener('input', updateMap);
        });
        document.getElementById('latField').addEventListener('input', updateMap);
        document.getElementById('lngField').addEventListener('input', updateMap);
    </script>

@endsection