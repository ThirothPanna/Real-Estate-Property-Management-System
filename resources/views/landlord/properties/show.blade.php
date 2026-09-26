@extends('layouts.app')

@section('title', $property->name . ' – NEKJOUL IMANAGE')

@section('content')

    @if (session('status'))
        <div style="background:#dcfce7; color:#16a34a; padding:12px 16px; border-radius:10px; margin-bottom:16px; font-size:14px;">
            {{ session('status') }}
        </div>
    @endif

    <div style="max-width:960px; margin:0 auto;">

        <a href="{{ route('landlord.properties.index') }}" style="font-size:13px; color:#6b7280; text-decoration:none; display:inline-block; margin-bottom:16px;">← Back to Properties</a>

        {{-- Photo gallery --}}
        @php $firstPhoto = $property->photos->first(); @endphp
        @if ($firstPhoto)
            <div style="margin-bottom:24px;">
                <div id="heroPhoto" style="height:360px; border-radius:18px; overflow:hidden; background:#f3f4f6; position:relative;">
                    <img id="heroImg" src="{{ $firstPhoto->url }}" alt="{{ $property->name }}"
                         style="width:100%;height:100%;object-fit:cover;display:block;">
                    <div style="position:absolute;bottom:0;left:0;right:0;padding:40px 24px 20px;background:linear-gradient(transparent,rgba(0,0,0,.75));color:#fff;">
                        <h1 style="font-size:26px;font-weight:800;margin-bottom:4px;">{{ $property->name }}</h1>
                        <p style="font-size:14px;opacity:.9;">{{ $property->full_address }}</p>
                    </div>
                </div>

                @if ($property->photos->count() > 1)
                    <div style="display:flex; gap:10px; margin-top:12px; overflow-x:auto; padding-bottom:6px;">
                        @foreach ($property->photos as $photo)
                            <img src="{{ $photo->url }}"
                                 onclick="document.getElementById('heroImg').src='{{ $photo->url }}'"
                                 style="width:90px;height:70px;object-fit:cover;border-radius:10px;cursor:pointer;border:2px solid #e5e7eb;flex-shrink:0;">
                        @endforeach
                    </div>
                @endif
            </div>
        @endif

        {{-- Details --}}
        <div class="panel">
            <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:16px;">
                <h3 style="margin:0;">Details</h3>
                <span style="padding:6px 14px; border-radius:20px; font-size:13px; font-weight:600; {{ $property->status_badge }}">
                    {{ ucfirst($property->status) }}
                </span>
            </div>

            <div style="display:grid; grid-template-columns:repeat(4, 1fr); gap:24px; margin-top:20px;">
                <div>
                    <div style="font-size:12px; color:#6b7280; text-transform:uppercase; letter-spacing:.5px; margin-bottom:4px;">Type</div>
                    <div style="font-size:15px; font-weight:600; color:#111827;">{{ ucfirst($property->type) }}</div>
                </div>
                <div>
                    <div style="font-size:12px; color:#6b7280; text-transform:uppercase; letter-spacing:.5px; margin-bottom:4px;">Bedrooms</div>
                    <div style="font-size:15px; font-weight:600; color:#111827;">{{ $property->bedrooms }}</div>
                </div>
                <div>
                    <div style="font-size:12px; color:#6b7280; text-transform:uppercase; letter-spacing:.5px; margin-bottom:4px;">Bathrooms</div>
                    <div style="font-size:15px; font-weight:600; color:#111827;">{{ $property->bathrooms }}</div>
                </div>
                <div>
                    <div style="font-size:12px; color:#6b7280; text-transform:uppercase; letter-spacing:.5px; margin-bottom:4px;">Square Feet</div>
                    <div style="font-size:15px; font-weight:600; color:#111827;">{{ $property->square_feet ? number_format($property->square_feet) : '—' }}</div>
                </div>
            </div>

            <div style="margin-top:24px; padding-top:20px; border-top:1px solid #f3f4f6;">
                <div style="font-size:12px; color:#6b7280; text-transform:uppercase; letter-spacing:.5px; margin-bottom:4px;">Monthly Rent</div>
                <div style="font-size:28px; font-weight:800; color:#16a34a;">${{ number_format($property->rent_amount, 2) }}</div>
            </div>

            @if ($property->description)
                <div style="margin-top:24px; padding-top:20px; border-top:1px solid #f3f4f6;">
                    <div style="font-size:12px; color:#6b7280; text-transform:uppercase; letter-spacing:.5px; margin-bottom:8px;">Description</div>
                    <div style="font-size:14px; color:#374151; line-height:1.6;">{{ $property->description }}</div>
                </div>
            @endif
        </div>

        {{-- Map --}}
        <div class="panel">
            <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px; margin-bottom:16px;">
                <h3 style="margin:0;">Location</h3>
                <a href="{{ $property->map_link }}" target="_blank"
                   style="font-size:13px; font-weight:600; color:#16a34a; text-decoration:none;">
                    Open in Google Maps ↗
                </a>
            </div>

            <div style="border-radius:14px; overflow:hidden; border:1px solid #e5e7eb;">
                <iframe src="{{ $property->map_embed_url }}"
                        width="100%" height="380"
                        style="border:0; display:block;"
                        allowfullscreen=""
                        loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade"></iframe>
            </div>

            <div style="font-size:13px; color:#6b7280; margin-top:12px;">
                📍 {{ $property->full_address }}
            </div>
        </div>

        <div style="display:flex; justify-content:flex-end; gap:12px; margin-bottom:40px;">
            <a href="{{ route('landlord.properties.edit', $property) }}" class="btn btn-outline">Edit Property</a>
        </div>

    </div>

@endsection