@extends('layouts.app')

@section('title', $property->name . ' – NEKJOUL IMANAGE')

@section('content')
    <style>
        .property-detail { max-width:1000px; margin:0 auto; }
        .property-back { display:inline-block; margin-bottom:16px; color:#4b5563; font-size:14px; font-weight:600; text-decoration:none; }
        .property-photo { display:block; width:100%; height:340px; border-radius:8px; object-fit:cover; background:#f3f4f6; cursor:zoom-in; }
        .photo-fullscreen { width:100vw; max-width:none; height:100vh; max-height:none; margin:0; padding:56px 24px 24px; border:0; background:rgba(0,0,0,.96); color:#fff; }
        .photo-fullscreen::backdrop { background:rgba(0,0,0,.96); }
        .photo-fullscreen img { display:block; width:100%; height:100%; object-fit:contain; }
        .photo-fullscreen-close { position:absolute; top:14px; right:18px; padding:8px 14px; border:1px solid rgba(255,255,255,.5); border-radius:8px; background:#222; color:#fff; font:inherit; cursor:pointer; }
        @media (max-width:600px) { .photo-fullscreen { padding:56px 10px 12px; } }
        .property-gallery { margin-top:12px; }
        .property-gallery-label { margin:0 0 8px; color:#6b7280; font-size:13px; }
        .property-gallery-track { display:flex; gap:10px; overflow-x:auto; padding:2px 2px 10px; scroll-snap-type:x mandatory; -webkit-overflow-scrolling:touch; }
        .property-gallery-item { flex:0 0 150px; padding:0; border:2px solid transparent; border-radius:8px; background:#f3f4f6; cursor:pointer; scroll-snap-align:start; }
        .property-gallery-item[aria-pressed="true"] { border-color:#15803d; }
        .property-gallery-photo { display:block; width:100%; height:96px; border-radius:6px; object-fit:cover; }
        .property-detail-panel { margin-top:18px; padding:22px; border:1px solid #e5e7eb; border-radius:8px; background:#fff; }
        .property-detail-heading { display:flex; justify-content:space-between; align-items:flex-start; gap:18px; flex-wrap:wrap; }
        .property-detail-name { margin:0; color:#111827; font-size:24px; font-weight:700; }
        .property-detail-address { margin:6px 0 0; color:#6b7280; font-size:14px; }
        .property-detail-price { color:#15803d; font-size:24px; font-weight:800; white-space:nowrap; }
        .property-detail-price span { color:#6b7280; font-size:13px; font-weight:500; }
        .property-status { display:inline-block; margin-bottom:8px; padding:4px 10px; border-radius:999px; background:#dcfce7; color:#15803d; font-size:12px; font-weight:700; }
        .property-contact-landlord { display:inline-block; margin-top:18px; padding:11px 16px; border-radius:8px; background:#15803d; color:#fff; font-size:14px; font-weight:700; text-decoration:none; }
        .property-contact-landlord:hover { background:#166534; }
        .property-detail-specs { display:flex; flex-wrap:wrap; gap:22px; margin-top:20px; padding-top:18px; border-top:1px solid #f3f4f6; color:#374151; font-size:14px; }
        .property-description { margin:20px 0 0; color:#4b5563; font-size:14px; line-height:1.65; white-space:pre-line; }
        .property-location { margin-top:18px; padding:22px; border:1px solid #e5e7eb; border-radius:8px; background:#fff; }
        .property-location-header { display:flex; justify-content:space-between; align-items:center; gap:14px; flex-wrap:wrap; margin-bottom:14px; }
        .property-location h2 { margin:0; color:#111827; font-size:17px; font-weight:700; }
        .property-location-link { color:#15803d; font-size:13px; font-weight:600; text-decoration:none; }
        .property-map { display:block; width:100%; height:340px; border:0; border-radius:7px; }
        @media (max-width:600px) { .property-photo { height:220px; } .property-detail-price { font-size:21px; } .property-map { height:260px; } }
    </style>

    <main class="property-detail">
        <a class="property-back" href="{{ route('tenant.properties.index') }}">← Back to available homes</a>
        <img class="property-photo" id="property-main-photo" src="{{ $property->image_url }}" alt="{{ $property->name }}" role="button" tabindex="0" aria-label="View {{ $property->name }} photo in full screen" title="Click to view full screen">
        @if ($property->photos->count() > 1)
            <section class="property-gallery" aria-label="Photo gallery for {{ $property->name }}">
                <p class="property-gallery-label">Scroll to see all {{ $property->photos->count() }} photos. Select a photo to view it larger.</p>
                <div class="property-gallery-track" role="group" aria-label="Property photos">
                @foreach ($property->photos as $photo)
                    <button class="property-gallery-item" type="button"
                            data-photo-url="{{ $photo->url }}"
                            data-photo-alt="{{ $property->name }} photo {{ $loop->iteration }}"
                            aria-label="View photo {{ $loop->iteration }} of {{ $property->photos->count() }}"
                            aria-pressed="{{ $photo->url === $property->image_url ? 'true' : 'false' }}">
                        <img class="property-gallery-photo" src="{{ $photo->url }}" alt="" loading="lazy">
                    </button>
                @endforeach
                </div>
            </section>
        @endif

        <section class="property-detail-panel">
            <div class="property-detail-heading">
                <div>
                    <span class="property-status">Available</span>
                    <h1 class="property-detail-name">{{ $property->name }}</h1>
                    <p class="property-detail-address">{{ $property->full_address }}</p>
                </div>
                <div class="property-detail-price">
                    ${{ number_format((float) $property->rent_amount, 2) }}
                    <span>per month</span>
                </div>
            </div>
            <div class="property-detail-specs">
                <span>{{ $property->bedrooms }} bedrooms</span>
                <span>{{ $property->bathrooms }} bathrooms</span>
                @if ($property->square_feet)
                    <span>{{ number_format($property->square_feet) }} sq ft</span>
                @endif
                <span>{{ ucfirst($property->type) }}</span>
            </div>
            @if ($property->description)
                <p class="property-description">{{ $property->description }}</p>
            @endif
            <a class="property-contact-landlord" href="{{ route('tenant.inquiries.show', $property) }}">Message Landlord</a>
        </section>

        <section class="property-location">
            <div class="property-location-header">
                <h2>Location</h2>
                <a class="property-location-link" href="{{ $property->map_link }}" target="_blank" rel="noopener noreferrer">Open in Google Maps</a>
            </div>
            <iframe class="property-map" src="{{ $property->map_embed_url }}"
                    title="Map showing {{ $property->name }}" allowfullscreen loading="lazy"
                    referrerpolicy="no-referrer-when-downgrade"></iframe>
        </section>
    </main>
    <dialog class="photo-fullscreen" id="property-photo-dialog" aria-label="Full screen property photo">
        <button class="photo-fullscreen-close" id="property-photo-close" type="button">Close <span aria-hidden="true">×</span></button>
        <img id="property-fullscreen-photo" src="{{ $property->image_url }}" alt="{{ $property->name }}">
    </dialog>
    <script>
        (function () {
            var mainPhoto = document.getElementById('property-main-photo');
            var photoDialog = document.getElementById('property-photo-dialog');
            var fullscreenPhoto = document.getElementById('property-fullscreen-photo');

            function openFullscreen() {
                fullscreenPhoto.src = mainPhoto.src;
                fullscreenPhoto.alt = mainPhoto.alt;
                photoDialog.showModal();
            }

            mainPhoto.addEventListener('click', openFullscreen);
            mainPhoto.addEventListener('keydown', function (event) {
                if (event.key === 'Enter' || event.key === ' ') {
                    event.preventDefault();
                    openFullscreen();
                }
            });
            document.getElementById('property-photo-close').addEventListener('click', function () {
                photoDialog.close();
                mainPhoto.focus();
            });
            photoDialog.addEventListener('click', function (event) {
                if (event.target === photoDialog) photoDialog.close();
            });
        })();
    </script>
    @if ($property->photos->count() > 1)
        <script>
            document.querySelectorAll('.property-gallery-item').forEach(function (button) {
                button.addEventListener('click', function () {
                    var mainPhoto = document.getElementById('property-main-photo');
                    mainPhoto.src = button.dataset.photoUrl;
                    mainPhoto.alt = button.dataset.photoAlt;
                    document.querySelectorAll('.property-gallery-item').forEach(function (item) {
                        item.setAttribute('aria-pressed', item === button ? 'true' : 'false');
                    });
                });
            });
        </script>
    @endif
@endsection
