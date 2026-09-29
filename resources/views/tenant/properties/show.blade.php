@extends('layouts.app')

@section('title', $property->name . ' – NEKJOUL IMANAGE')

@section('content')
    <style>
        .property-detail { max-width:1000px; margin:0 auto; }
        .property-back { display:inline-block; margin-bottom:16px; color:#4b5563; font-size:14px; font-weight:600; text-decoration:none; }
        .property-photo { display:block; width:100%; height:340px; border-radius:8px; object-fit:cover; background:#f3f4f6; }
        .property-detail-panel { margin-top:18px; padding:22px; border:1px solid #e5e7eb; border-radius:8px; background:#fff; }
        .property-detail-heading { display:flex; justify-content:space-between; align-items:flex-start; gap:18px; flex-wrap:wrap; }
        .property-detail-name { margin:0; color:#111827; font-size:24px; font-weight:700; }
        .property-detail-address { margin:6px 0 0; color:#6b7280; font-size:14px; }
        .property-detail-price { color:#15803d; font-size:24px; font-weight:800; white-space:nowrap; }
        .property-detail-price span { color:#6b7280; font-size:13px; font-weight:500; }
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
        <img class="property-photo" src="{{ $property->image_url }}" alt="{{ $property->name }}">

        <section class="property-detail-panel">
            <div class="property-detail-heading">
                <div>
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
@endsection