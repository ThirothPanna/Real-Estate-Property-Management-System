@extends('layouts.app')

@section('title', 'Properties – NEKJOUL IMANAGE')

@section('content')

    @if (session('status'))
        <div style="background:#dcfce7; color:#16a34a; padding:12px 16px; border-radius:10px; margin-bottom:16px; font-size:14px;">
            {{ session('status') }}
        </div>
    @endif

    <div style="display:flex; justify-content:space-between; align-items:flex-start; gap:16px; flex-wrap:wrap; margin-bottom:24px;">
        <div>
            <h1 style="font-size:24px; font-weight:700; margin-bottom:4px;">Properties</h1>
            <p style="color:#6b7280; font-size:14px;">{{ $properties->total() }} total properties in your portfolio.</p>
        </div>

        <a href="{{ route('landlord.properties.create') }}" class="btn btn-primary">➕ Add Property</a>
    </div>

    @if ($properties->isEmpty())
        <div class="panel">
            <div class="empty">
                <div style="font-size:52px; margin-bottom:16px;">🏠</div>
                <div style="font-size:16px; font-weight:600; color:#111827; margin-bottom:6px;">No properties yet</div>
                <div style="margin-bottom:20px;">Start by adding your first rental property.</div>
                <a href="{{ route('landlord.properties.create') }}" class="btn btn-primary" style="display:inline-flex;">
                    ➕ Add Your First Property
                </a>
            </div>
        </div>
    @else
        <div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(300px, 1fr)); gap:20px;">
            @foreach ($properties as $prop)
                <div style="background:#fff; border:1px solid #e5e7eb; border-radius:16px; overflow:hidden; transition:transform .2s, box-shadow .2s;"
                     onmouseover="this.style.transform='translateY(-4px)'; this.style.boxShadow='0 10px 24px rgba(0,0,0,.08)';"
                     onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none';">

                    {{-- Photo --}}
                    <div style="height:170px; background:#f3f4f6; overflow:hidden; position:relative;">
                        <img src="{{ $prop->image_url }}" alt="{{ $prop->name }}"
                             style="width:100%; height:100%; object-fit:cover; display:block;">

                        <span style="position:absolute; top:12px; right:12px; padding:4px 10px; border-radius:20px; font-size:12px; font-weight:600; {{ $prop->status_badge }}">
                            {{ ucfirst($prop->status) }}
                        </span>
                    </div>

                    {{-- Body --}}
                    <div style="padding:20px;">
                        <div style="font-size:16px; font-weight:700; color:#111827; margin-bottom:6px;">{{ $prop->name }}</div>
                        <div style="font-size:13px; color:#6b7280; margin-bottom:16px; line-height:1.5;">{{ $prop->full_address }}</div>

                        <div style="display:flex; gap:16px; margin-bottom:16px; font-size:13px; color:#6b7280;">
                            <span>🛏 {{ $prop->bedrooms }} bed</span>
                            <span>🛁 {{ $prop->bathrooms }} bath</span>
                            @if ($prop->square_feet)
                                <span>📐 {{ number_format($prop->square_feet) }} ft²</span>
                            @endif
                        </div>

                        <div style="font-size:22px; font-weight:800; color:#16a34a;">
                            ${{ number_format($prop->rent_amount, 2) }}
                            <span style="font-size:13px; font-weight:500; color:#6b7280;">/month</span>
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div style="display:grid; grid-template-columns:1fr 1fr 1fr; border-top:1px solid #f3f4f6;">
                        <a href="{{ route('landlord.properties.show', $prop) }}"
                           style="padding:12px; text-align:center; font-size:13px; font-weight:600; color:#374151; text-decoration:none; border-right:1px solid #f3f4f6;">View</a>
                        <a href="{{ route('landlord.properties.edit', $prop) }}"
                           style="padding:12px; text-align:center; font-size:13px; font-weight:600; color:#374151; text-decoration:none; border-right:1px solid #f3f4f6;">Edit</a>
                        <form method="POST" action="{{ route('landlord.properties.destroy', $prop) }}"
                              onsubmit="return confirm('Delete {{ $prop->name }}?');" style="margin:0;">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    style="width:100%; padding:12px; background:none; border:none; font-size:13px; font-weight:600; color:#dc2626; cursor:pointer;">Delete</button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>

        <div style="margin-top:24px;">
            {{ $properties->links() }}
        </div>
    @endif

@endsection