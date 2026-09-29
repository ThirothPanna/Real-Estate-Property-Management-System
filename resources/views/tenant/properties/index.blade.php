@extends('layouts.app')

@section('title', 'Available Homes – NEKJOUL IMANAGE')

@section('content')
    <style>
        .available-page { max-width:1200px; margin:0 auto; }
        .available-header { display:flex; justify-content:space-between; align-items:flex-end; gap:20px; flex-wrap:wrap; margin-bottom:22px; }
        .available-title { margin:0; color:#111827; font-size:24px; font-weight:700; }
        .available-subtitle { margin:5px 0 0; color:#6b7280; font-size:14px; }
        .available-search { display:flex; gap:8px; width:min(100%, 390px); }
        .available-search input { min-width:0; flex:1; padding:10px 12px; border:1px solid #d1d5db; border-radius:7px; font:inherit; font-size:14px; }
        .available-search button { padding:10px 14px; border:0; border-radius:7px; background:#15803d; color:#fff; font:inherit; font-size:14px; font-weight:600; cursor:pointer; }
        .available-grid { display:grid; grid-template-columns:repeat(auto-fill, minmax(min(300px, 100%), 1fr)); gap:18px; }
        .available-card { min-width:0; overflow:hidden; border:1px solid #e5e7eb; border-radius:8px; background:#fff; }
        .available-image { display:block; width:100%; height:170px; object-fit:cover; background:#f3f4f6; }
        .available-info { padding:17px; }
        .available-name { margin:0; color:#111827; font-size:16px; font-weight:700; }
        .available-address { margin:5px 0 13px; color:#6b7280; font-size:13px; line-height:1.5; }
        .available-specs { display:flex; flex-wrap:wrap; gap:12px; color:#4b5563; font-size:13px; }
        .available-price { margin-top:14px; color:#15803d; font-size:21px; font-weight:800; }
        .available-price span { color:#6b7280; font-size:13px; font-weight:500; }
        .available-actions { display:flex; justify-content:space-between; align-items:center; gap:12px; border-top:1px solid #f3f4f6; padding:12px 17px; }
        .available-map { color:#4b5563; font-size:13px; font-weight:600; text-decoration:none; }
        .available-details { color:#15803d; font-size:13px; font-weight:700; text-decoration:none; }
        .available-empty { padding:42px 18px; border:1px solid #e5e7eb; background:#fff; text-align:center; color:#6b7280; }
        .available-pagination { margin-top:22px; }
        @media (max-width:600px) { .available-header { align-items:stretch; } .available-search { width:100%; } }
    </style>

    <main class="available-page">
        <header class="available-header">
            <div>
                <h1 class="available-title">Available Homes</h1>
                <p class="available-subtitle">Browse rental properties currently available.</p>
            </div>
            <form class="available-search" method="GET" action="{{ route('tenant.properties.index') }}" role="search">
                <label class="sr-only" for="property-search">Search by property or location</label>
                <input id="property-search" name="search" value="{{ $search }}" placeholder="Property, city, or state">
                <button type="submit">Search</button>
            </form>
        </header>

        @if ($properties->isEmpty())
            <div class="available-empty">
                {{ $search !== '' ? 'No available properties match that search.' : 'There are no available properties right now.' }}
            </div>
        @else
            <div class="available-grid">
                @foreach ($properties as $property)
                    <article class="available-card">
                        <img class="available-image" src="{{ $property->image_url }}" alt="{{ $property->name }}">
                        <div class="available-info">
                            <h2 class="available-name">{{ $property->name }}</h2>
                            <p class="available-address">{{ $property->full_address }}</p>
                            <div class="available-specs">
                                <span>{{ $property->bedrooms }} bed</span>
                                <span>{{ $property->bathrooms }} bath</span>
                                @if ($property->square_feet)
                                    <span>{{ number_format($property->square_feet) }} sq ft</span>
                                @endif
                                <span>{{ ucfirst($property->type) }}</span>
                            </div>
                            <div class="available-price">
                                ${{ number_format((float) $property->rent_amount, 2) }}
                                <span>per month</span>
                            </div>
                        </div>
                        <div class="available-actions">
                            <a class="available-map" href="{{ $property->map_link }}" target="_blank" rel="noopener noreferrer">Location</a>
                            <a class="available-details" href="{{ route('tenant.properties.show', $property) }}">View details</a>
                        </div>
                    </article>
                @endforeach
            </div>

            <div class="available-pagination">{{ $properties->links() }}</div>
        @endif
    </main>
@endsection