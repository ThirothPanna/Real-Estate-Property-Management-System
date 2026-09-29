@extends('layouts.app')

@section('title', 'Utility Requests – NEKJOUL IMANAGE')

@section('content')
    <style>
        .utility-page { max-width: 1100px; margin: 0 auto; }
        .utility-header { margin-bottom: 22px; }
        .utility-header h1 { margin: 0 0 5px; font-size: 24px; font-weight: 700; }
        .utility-header p { margin: 0; color: #6b7280; font-size: 14px; }
        .utility-grid { display: grid; grid-template-columns: minmax(280px, 360px) minmax(0, 1fr); gap: 20px; align-items: start; }
        .utility-panel { padding: 20px; background: #fff; border: 1px solid #e5e7eb; border-radius: 8px; }
        .utility-panel h2 { margin: 0 0 16px; font-size: 16px; }
        .utility-form { display: grid; gap: 13px; }
        .utility-form label { display: block; margin-bottom: 5px; font-size: 13px; font-weight: 600; }
        .utility-form input, .utility-form select, .utility-form textarea { box-sizing: border-box; width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 6px; font: inherit; font-size: 14px; }
        .utility-form textarea { min-height: 90px; resize: vertical; }
        .utility-submit { padding: 10px 14px; border: 0; border-radius: 6px; background: #15803d; color: #fff; font: inherit; font-weight: 600; cursor: pointer; }
        .utility-error { margin-top: 4px; color: #b91c1c; font-size: 12px; }
        .utility-alert { margin-bottom: 16px; padding: 12px 14px; border: 1px solid #bbf7d0; border-radius: 6px; background: #f0fdf4; color: #166534; }
        .utility-list { display: grid; gap: 12px; }
        .utility-card { padding: 15px; border: 1px solid #e5e7eb; border-radius: 6px; }
        .utility-card-head { display: flex; justify-content: space-between; gap: 12px; align-items: flex-start; }
        .utility-card h3 { margin: 0 0 3px; font-size: 15px; }
        .utility-meta { color: #6b7280; font-size: 12px; }
        .utility-status { padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: 700; text-transform: capitalize; white-space: nowrap; }
        .utility-status.pending { background: #fef3c7; color: #92400e; }
        .utility-status.approved { background: #dcfce7; color: #166534; }
        .utility-status.rejected { background: #fee2e2; color: #991b1b; }
        .utility-message { margin: 12px 0 0; white-space: pre-wrap; font-size: 14px; }
        .utility-response { margin-top: 12px; padding-top: 10px; border-top: 1px solid #e5e7eb; font-size: 13px; }
        .utility-empty { padding: 28px 12px; color: #6b7280; text-align: center; font-size: 14px; }
        @media (max-width: 760px) { .utility-grid { grid-template-columns: 1fr; } }
    </style>

    <div class="utility-page">
        <header class="utility-header">
            <h1>Utility Requests</h1>
            <p>Request approval for a utility service at one of your rentals.</p>
        </header>

        @if (session('success'))
            <div class="utility-alert" role="status">{{ session('success') }}</div>
        @endif

        <div class="utility-grid">
            <section class="utility-panel">
                <h2>Submit a request</h2>
                @if ($tenancies->isEmpty())
                    <div class="utility-empty">You need an active tenancy to submit a utility request.</div>
                @else
                    <form class="utility-form" method="POST" action="{{ route('tenant.utility-requests.store') }}">
                        @csrf
                        <div>
                            <label for="tenancy_id">Rental property</label>
                            <select id="tenancy_id" name="tenancy_id" required>
                                @foreach ($tenancies as $tenancy)
                                    <option value="{{ $tenancy->id }}" @selected(old('tenancy_id') == $tenancy->id)>{{ $tenancy->property->name }}</option>
                                @endforeach
                            </select>
                            @error('tenancy_id') <div class="utility-error">{{ $message }}</div> @enderror
                        </div>
                        <div>
                            <label for="utility_type">Utility type</label>
                            <select id="utility_type" name="utility_type" required>
                                @foreach (['electricity', 'water', 'gas', 'internet', 'trash', 'other'] as $type)
                                    <option value="{{ $type }}" @selected(old('utility_type') === $type)>{{ ucfirst($type) }}</option>
                                @endforeach
                            </select>
                            @error('utility_type') <div class="utility-error">{{ $message }}</div> @enderror
                        </div>
                        <div>
                            <label for="provider_name">Preferred provider (optional)</label>
                            <input id="provider_name" name="provider_name" value="{{ old('provider_name') }}" maxlength="255">
                            @error('provider_name') <div class="utility-error">{{ $message }}</div> @enderror
                        </div>
                        <div>
                            <label for="message">Request details</label>
                            <textarea id="message" name="message" required maxlength="2000">{{ old('message') }}</textarea>
                            @error('message') <div class="utility-error">{{ $message }}</div> @enderror
                        </div>
                        <button class="utility-submit" type="submit">Send for approval</button>
                    </form>
                @endif
            </section>

            <section class="utility-panel">
                <h2>Your requests</h2>
                @if ($requests->isEmpty())
                    <div class="utility-empty">No utility requests yet.</div>
                @else
                    <div class="utility-list">
                        @foreach ($requests as $utilityRequest)
                            <article class="utility-card">
                                <div class="utility-card-head">
                                    <div>
                                        <h3>{{ ucfirst($utilityRequest->utility_type) }}{{ $utilityRequest->provider_name ? ' · ' . $utilityRequest->provider_name : '' }}</h3>
                                        <div class="utility-meta">{{ $utilityRequest->tenancy->property->name }} · {{ $utilityRequest->created_at->format('M j, Y') }}</div>
                                    </div>
                                    <span class="utility-status {{ $utilityRequest->status }}">{{ $utilityRequest->status }}</span>
                                </div>
                                <p class="utility-message">{{ $utilityRequest->message }}</p>
                                @if ($utilityRequest->landlord_response)
                                    <div class="utility-response"><strong>Landlord response:</strong> {{ $utilityRequest->landlord_response }}</div>
                                @endif
                            </article>
                        @endforeach
                    </div>
                @endif
            </section>
        </div>
    </div>
@endsection