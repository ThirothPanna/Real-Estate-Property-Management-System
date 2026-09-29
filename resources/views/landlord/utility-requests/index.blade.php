@extends('layouts.app')

@section('title', 'Utility Requests – NEKJOUL IMANAGE')

@section('content')
    <style>
        .utility-page { max-width: 1100px; margin: 0 auto; }
        .utility-header { margin-bottom: 20px; }
        .utility-header h1 { margin: 0 0 5px; font-size: 24px; font-weight: 700; }
        .utility-header p { margin: 0; color: #6b7280; font-size: 14px; }
        .utility-counts { margin-top: 8px; color: #6b7280; font-size: 13px; }
        .utility-alert { margin-bottom: 16px; padding: 12px 14px; border: 1px solid #bbf7d0; border-radius: 6px; background: #f0fdf4; color: #166534; }
        .utility-tabs { display: flex; gap: 4px; margin-bottom: 16px; border-bottom: 1px solid #e5e7eb; overflow-x: auto; }
        .utility-tab { padding: 10px 14px; border-bottom: 2px solid transparent; color: #6b7280; text-decoration: none; white-space: nowrap; font-size: 14px; font-weight: 600; }
        .utility-tab.active { border-color: #15803d; color: #166534; }
        .utility-list { display: grid; gap: 12px; }
        .utility-card { padding: 18px; border: 1px solid #e5e7eb; border-radius: 8px; background: #fff; }
        .utility-card-head { display: flex; justify-content: space-between; gap: 14px; align-items: flex-start; }
        .utility-card h2 { margin: 0 0 4px; font-size: 16px; }
        .utility-meta { color: #6b7280; font-size: 13px; }
        .utility-status { padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: 700; text-transform: capitalize; white-space: nowrap; }
        .utility-status.pending { background: #fef3c7; color: #92400e; }
        .utility-status.approved { background: #dcfce7; color: #166534; }
        .utility-status.rejected { background: #fee2e2; color: #991b1b; }
        .utility-message { margin: 14px 0; white-space: pre-wrap; font-size: 14px; }
        .utility-decision { display: grid; grid-template-columns: minmax(0, 1fr) auto auto; gap: 8px; align-items: end; padding-top: 12px; border-top: 1px solid #e5e7eb; }
        .utility-decision label { grid-column: 1 / -1; font-size: 12px; font-weight: 600; color: #4b5563; }
        .utility-decision textarea { box-sizing: border-box; width: 100%; min-height: 40px; padding: 9px; border: 1px solid #d1d5db; border-radius: 6px; font: inherit; font-size: 13px; resize: vertical; }
        .utility-decision button { min-height: 40px; padding: 8px 12px; border: 1px solid transparent; border-radius: 6px; font: inherit; font-size: 13px; font-weight: 600; cursor: pointer; }
        .utility-approve { background: #15803d; color: #fff; }
        .utility-reject { background: #fff; border-color: #fecaca !important; color: #b91c1c; }
        .utility-response { margin-top: 12px; padding-top: 10px; border-top: 1px solid #e5e7eb; color: #4b5563; font-size: 13px; }
        .utility-empty { padding: 48px 16px; background: #fff; border: 1px solid #e5e7eb; border-radius: 8px; color: #6b7280; text-align: center; }
        .utility-pagination { margin-top: 16px; }
        @media (max-width: 620px) {
            .utility-decision { grid-template-columns: 1fr 1fr; }
            .utility-decision textarea { grid-column: 1 / -1; }
        }
    </style>

    <div class="utility-page">
        <header class="utility-header">
            <h1>Tenant Utility Requests</h1>
            <p>Review utility service requests for your properties.</p>
            <div class="utility-counts">{{ $counts['all'] }} total · {{ $counts['pending'] }} pending · {{ $counts['approved'] }} approved · {{ $counts['rejected'] }} rejected</div>
        </header>

        @if (session('success'))
            <div class="utility-alert" role="status">{{ session('success') }}</div>
        @endif

        <nav class="utility-tabs" aria-label="Filter utility requests">
            @foreach (['all' => 'All', 'pending' => 'Pending', 'approved' => 'Approved', 'rejected' => 'Rejected'] as $key => $label)
                <a class="utility-tab {{ $tab === $key ? 'active' : '' }}" href="{{ route('landlord.utility-requests.index', ['tab' => $key]) }}">{{ $label }} ({{ $key === 'all' ? $counts['all'] : $counts[$key] }})</a>
            @endforeach
        </nav>

        @if ($utilityRequests->isEmpty())
            <div class="utility-empty">No {{ $tab === 'all' ? '' : $tab . ' ' }}utility requests found.</div>
        @else
            <div class="utility-list">
                @foreach ($utilityRequests as $utilityRequest)
                    <article class="utility-card">
                        <header class="utility-card-head">
                            <div>
                                <h2>{{ ucfirst($utilityRequest->utility_type) }}{{ $utilityRequest->provider_name ? ' · ' . $utilityRequest->provider_name : '' }}</h2>
                                <div class="utility-meta">{{ $utilityRequest->tenancy->tenant->name }} · {{ $utilityRequest->tenancy->property->name }} · {{ $utilityRequest->created_at->format('M j, Y') }}</div>
                            </div>
                            <span class="utility-status {{ $utilityRequest->status }}">{{ $utilityRequest->status }}</span>
                        </header>
                        <p class="utility-message">{{ $utilityRequest->message }}</p>

                        @if ($utilityRequest->status === 'pending')
                            <form class="utility-decision" method="POST" action="{{ route('landlord.utility-requests.update', $utilityRequest) }}">
                                @csrf
                                @method('PATCH')
                                <label for="response-{{ $utilityRequest->id }}">Response to tenant (optional)</label>
                                <textarea id="response-{{ $utilityRequest->id }}" name="landlord_response" maxlength="1000" placeholder="Add a note for the tenant"></textarea>
                                <button class="utility-approve" type="submit" name="status" value="approved">Approve</button>
                                <button class="utility-reject" type="submit" name="status" value="rejected">Reject</button>
                            </form>
                        @elseif ($utilityRequest->landlord_response)
                            <div class="utility-response"><strong>Response sent:</strong> {{ $utilityRequest->landlord_response }}</div>
                        @endif
                    </article>
                @endforeach
            </div>
            <div class="utility-pagination">{{ $utilityRequests->links() }}</div>
        @endif
    </div>
@endsection