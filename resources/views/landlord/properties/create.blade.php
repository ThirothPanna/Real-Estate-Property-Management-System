@extends('layouts.app')

@section('title', 'Add Property – NEKJOUL IMANAGE')

@section('content')

    <div style="max-width:820px; margin:0 auto;">

        <div style="margin-bottom:24px;">
            <a href="{{ route('landlord.properties.index') }}" style="font-size:13px; color:#6b7280; text-decoration:none;">← Back to Properties</a>
            <h1 style="font-size:24px; font-weight:700; margin-top:8px; margin-bottom:4px;">Add Property</h1>
            <p style="color:#6b7280; font-size:14px;">Add a new property to your portfolio.</p>
        </div>

        <form method="POST" action="{{ route('landlord.properties.store') }}" enctype="multipart/form-data" id="propForm">
            @csrf

            {{-- Basic Info --}}
            <div class="panel">
                <h3>Basic Information</h3>
                <div>
                    <label style="display:block; font-size:12px; font-weight:600; color:#6b7280; text-transform:uppercase; letter-spacing:.4px; margin-bottom:6px;">Property Name *</label>
                    <input type="text" name="name" required value="{{ old('name') }}"
                           placeholder="e.g. Sunset Apartments Unit 4B"
                           style="width:100%; padding:12px 14px; border:1px solid #e5e7eb; border-radius:10px; font-size:14px; outline:none;">
                    @error('name') <div style="color:#ef4444; font-size:12px; margin-top:4px;">{{ $message }}</div> @enderror
                </div>
            </div>

            {{-- Address --}}
            <div class="panel">
                <h3>Address</h3>
                <div style="display:grid; grid-template-columns:1fr; gap:18px;">
                    <div>
                        <label style="display:block; font-size:12px; font-weight:600; color:#6b7280; text-transform:uppercase; letter-spacing:.4px; margin-bottom:6px;">Street Address *</label>
                        <input type="text" name="address" required value="{{ old('address') }}"
                               placeholder="123 Main Street"
                               style="width:100%; padding:12px 14px; border:1px solid #e5e7eb; border-radius:10px; font-size:14px; outline:none;">
                    </div>
                    <div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:14px;">
                        <div>
                            <label style="display:block; font-size:12px; font-weight:600; color:#6b7280; text-transform:uppercase; letter-spacing:.4px; margin-bottom:6px;">City</label>
                            <input type="text" name="city" value="{{ old('city') }}"
                                   style="width:100%; padding:12px 14px; border:1px solid #e5e7eb; border-radius:10px; font-size:14px; outline:none;">
                        </div>
                        <div>
                            <label style="display:block; font-size:12px; font-weight:600; color:#6b7280; text-transform:uppercase; letter-spacing:.4px; margin-bottom:6px;">State</label>
                            <input type="text" name="state" value="{{ old('state') }}"
                                   style="width:100%; padding:12px 14px; border:1px solid #e5e7eb; border-radius:10px; font-size:14px; outline:none;">
                        </div>
                        <div>
                            <label style="display:block; font-size:12px; font-weight:600; color:#6b7280; text-transform:uppercase; letter-spacing:.4px; margin-bottom:6px;">Zip</label>
                            <input type="text" name="zip" value="{{ old('zip') }}"
                                   style="width:100%; padding:12px 14px; border:1px solid #e5e7eb; border-radius:10px; font-size:14px; outline:none;">
                        </div>
                    </div>
                    <div>
                        <label style="display:block; font-size:12px; font-weight:600; color:#6b7280; text-transform:uppercase; letter-spacing:.4px; margin-bottom:6px;">Country</label>
                        <input type="text" name="country" value="{{ old('country', 'USA') }}"
                               style="width:100%; padding:12px 14px; border:1px solid #e5e7eb; border-radius:10px; font-size:14px; outline:none;">
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
                                <option value="{{ $k }}" {{ old('type') === $k ? 'selected' : '' }}>{{ $v }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label style="display:block; font-size:12px; font-weight:600; color:#6b7280; text-transform:uppercase; letter-spacing:.4px; margin-bottom:6px;">Status *</label>
                        <select name="status" required style="width:100%; padding:12px 14px; border:1px solid #e5e7eb; border-radius:10px; font-size:14px; background:#fff;">
                            <option value="available"   {{ old('status') === 'available'   ? 'selected' : '' }}>Available</option>
                            <option value="occupied"    {{ old('status') === 'occupied'    ? 'selected' : '' }}>Occupied</option>
                            <option value="maintenance" {{ old('status') === 'maintenance' ? 'selected' : '' }}>Under Maintenance</option>
                        </select>
                    </div>
                    <div>
                        <label style="display:block; font-size:12px; font-weight:600; color:#6b7280; text-transform:uppercase; letter-spacing:.4px; margin-bottom:6px;">Bedrooms *</label>
                        <input type="number" name="bedrooms" min="0" max="20" required value="{{ old('bedrooms', 1) }}"
                               style="width:100%; padding:12px 14px; border:1px solid #e5e7eb; border-radius:10px; font-size:14px;">
                    </div>
                    <div>
                        <label style="display:block; font-size:12px; font-weight:600; color:#6b7280; text-transform:uppercase; letter-spacing:.4px; margin-bottom:6px;">Bathrooms *</label>
                        <input type="number" name="bathrooms" min="0" max="20" required value="{{ old('bathrooms', 1) }}"
                               style="width:100%; padding:12px 14px; border:1px solid #e5e7eb; border-radius:10px; font-size:14px;">
                    </div>
                    <div>
                        <label style="display:block; font-size:12px; font-weight:600; color:#6b7280; text-transform:uppercase; letter-spacing:.4px; margin-bottom:6px;">Square Feet</label>
                        <input type="number" name="square_feet" min="0" value="{{ old('square_feet') }}"
                               style="width:100%; padding:12px 14px; border:1px solid #e5e7eb; border-radius:10px; font-size:14px;">
                    </div>
                    <div>
                        <label style="display:block; font-size:12px; font-weight:600; color:#6b7280; text-transform:uppercase; letter-spacing:.4px; margin-bottom:6px;">Monthly Rent (USD) *</label>
                        <input type="number" name="rent_amount" step="0.01" min="0" required value="{{ old('rent_amount') }}"
                               style="width:100%; padding:12px 14px; border:1px solid #e5e7eb; border-radius:10px; font-size:14px;">
                    </div>
                </div>
                <div style="margin-top:18px;">
                    <label style="display:block; font-size:12px; font-weight:600; color:#6b7280; text-transform:uppercase; letter-spacing:.4px; margin-bottom:6px;">Description</label>
                    <textarea name="description" rows="4"
                              style="width:100%; padding:12px 14px; border:1px solid #e5e7eb; border-radius:10px; font-size:14px; resize:vertical; font-family:inherit;">{{ old('description') }}</textarea>
                </div>
            </div>

            {{-- Photos --}}
            <div class="panel">
                <h3>Property Photos</h3>
                <p style="color:#6b7280; font-size:13px; margin-bottom:14px;">
                    Up to 10 photos. JPG, PNG, WEBP — max 4 MB each. First one is the cover.
                </p>

                <input type="file" name="photos[]" id="photoInput" accept="image/*" multiple style="display:none;">
                <input type="hidden" name="cover_index" id="coverIndex" value="0">

                <div id="photoPreviewGrid" style="display:flex; gap:12px; flex-wrap:wrap;"></div>

                <div id="addPhotoTile"
                     onclick="document.getElementById('photoInput').click()"
                     style="width:120px; height:120px; border-radius:12px; border:2px dashed #d1d5db; display:inline-flex; align-items:center; justify-content:center; flex-direction:column; gap:4px; cursor:pointer; color:#9ca3af; background:#f9fafb; margin-top:12px;">
                    <span style="font-size:24px;">📎</span>
                    <span style="font-size:11px;">Add photos</span>
                </div>

                @error('photos') <div style="color:#ef4444; font-size:12px; margin-top:8px;">{{ $message }}</div> @enderror
                @error('photos.*') <div style="color:#ef4444; font-size:12px; margin-top:8px;">{{ $message }}</div> @enderror
            </div>

            {{-- Location (free Google Map) --}}
            <div class="panel">
                <h3>Location</h3>
                <p style="color:#6b7280; font-size:13px; margin-bottom:14px;">
                    The map updates live as you type the address above.
                </p>

                <div style="border-radius:14px; overflow:hidden; border:1px solid #e5e7eb; margin-bottom:14px;">
                    <iframe id="mapPreview"
                            src="https://www.google.com/maps?q=New+York&output=embed"
                            width="100%" height="320"
                            style="border:0; display:block;"
                            allowfullscreen=""
                            loading="lazy"
                            referrerpolicy="no-referrer-when-downgrade"></iframe>
                </div>

                <div style="background:#f9fafb; padding:14px; border-radius:10px; border:1px solid #e5e7eb;">
                    <p style="font-size:12px; color:#6b7280; margin-bottom:10px;">
                        Optional: for a more precise pin, paste coordinates from
                        <a href="https://www.google.com/maps" target="_blank" style="color:#16a34a; font-weight:600;">Google Maps</a>
                        (right-click a spot → "What's here?" → copy the numbers).
                    </p>
                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:14px;">
                        <div>
                            <label style="display:block; font-size:12px; font-weight:600; color:#6b7280; text-transform:uppercase; letter-spacing:.4px; margin-bottom:6px;">Latitude</label>
                            <input type="text" name="latitude" id="latField" value="{{ old('latitude') }}" placeholder="e.g. 11.5564"
                                   style="width:100%; padding:10px 12px; border:1px solid #e5e7eb; border-radius:8px; font-size:13px; outline:none;">
                        </div>
                        <div>
                            <label style="display:block; font-size:12px; font-weight:600; color:#6b7280; text-transform:uppercase; letter-spacing:.4px; margin-bottom:6px;">Longitude</label>
                            <input type="text" name="longitude" id="lngField" value="{{ old('longitude') }}" placeholder="e.g. 104.9282"
                                   style="width:100%; padding:10px 12px; border:1px solid #e5e7eb; border-radius:8px; font-size:13px; outline:none;">
                        </div>
                    </div>
                </div>
            </div>

            <div style="display:flex; justify-content:flex-end; gap:12px; margin-bottom:40px;">
                <a href="{{ route('landlord.properties.index') }}" class="btn btn-outline">Cancel</a>
                <button type="submit" class="btn btn-primary">Save Property</button>
            </div>
        </form>
    </div>

    <script>
        // ============ Multi-photo preview ============
        var selectedFiles = [];

        document.getElementById('photoInput').addEventListener('change', function () {
            Array.from(this.files).forEach(function (f) {
                selectedFiles.push(f);
            });
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

                // Cover badge
                if (i === 0) {
                    var cover = document.createElement('div');
                    cover.textContent = '★ Cover';
                    cover.style.cssText = 'position:absolute;top:6px;left:6px;background:#22c55e;color:#fff;padding:2px 8px;border-radius:10px;font-size:10px;font-weight:700;';
                    tile.appendChild(cover);
                }

                // Remove button
                var remove = document.createElement('button');
                remove.type = 'button';
                remove.textContent = '✕';
                remove.style.cssText = 'position:absolute;top:6px;right:6px;background:#ef4444;color:#fff;border:none;width:22px;height:22px;border-radius:50%;cursor:pointer;font-size:12px;line-height:1;';
                remove.onclick = function (e) {
                    e.stopPropagation();
                    selectedFiles.splice(i, 1);
                    renderPhotoGrid();
                };
                tile.appendChild(remove);

                grid.appendChild(tile);
            });

            // Update cover index (first photo always)
            document.getElementById('coverIndex').value = 0;
        }

        // ============ Live Google Map preview ============
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
            if (address.length === 0) return;

            var lat = document.getElementById('latField').value.trim();
            var lng = document.getElementById('lngField').value.trim();

            var query = (lat && lng) ? (lat + ',' + lng) : address;
            var url = 'https://www.google.com/maps?q=' + encodeURIComponent(query) + '&output=embed';

            document.getElementById('mapPreview').src = url;
        }

        ['address', 'city', 'state', 'zip', 'country'].forEach(function (field) {
            var input = document.querySelector('input[name="' + field + '"]');
            if (input) input.addEventListener('input', updateMap);
        });

        document.getElementById('latField').addEventListener('input', updateMap);
        document.getElementById('lngField').addEventListener('input', updateMap);
    </script>

@endsection