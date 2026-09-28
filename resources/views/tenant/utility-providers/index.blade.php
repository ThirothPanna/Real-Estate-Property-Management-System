@extends('layouts.app')

@section('title', 'Utility Providers – NEKJOUL IMANAGE')

@section('content')
    <style>
        .providers-page { max-width: 1100px; margin: 0 auto; }
        .providers-header { margin-bottom: 24px; }
        .providers-header h1 { margin: 0 0 6px; font-size: 24px; font-weight: 700; }
        .providers-header p { margin: 0; color: #6b7280; font-size: 14px; }
        .providers-grid { display: grid; grid-template-columns: minmax(280px, 360px) minmax(0, 1fr); gap: 20px; align-items: start; }
        .providers-panel { padding: 22px; background: #fff; border: 1px solid #e5e7eb; border-radius: 14px; box-shadow: 0 1px 3px rgba(0, 0, 0, .05); }
        .providers-panel h2 { margin: 0 0 16px; font-size: 16px; font-weight: 700; }
        .provider-form { display: grid; gap: 13px; }
        .provider-form label { display: block; margin-bottom: 5px; font-size: 13px; font-weight: 600; color: #374151; }
        .provider-form input, .provider-form select, .provider-form textarea { box-sizing: border-box; width: 100%; padding: 10px 11px; border: 1px solid #d1d5db; border-radius: 8px; background: #fff; color: #111827; font: inherit; font-size: 14px; }
        .provider-form textarea { min-height: 82px; resize: vertical; }
        .provider-form input:focus, .provider-form select:focus, .provider-form textarea:focus { outline: 2px solid #bbf7d0; border-color: #22c55e; }
        .field-error { margin-top: 4px; color: #dc2626; font-size: 12px; }
        .provider-button { display: inline-flex; justify-content: center; align-items: center; padding: 10px 15px; border: 0; border-radius: 8px; background: #16a34a; color: #fff; font: inherit; font-size: 14px; font-weight: 600; cursor: pointer; }
        .provider-button:hover { background: #15803d; }
        .provider-button-danger { background: #fff; border: 1px solid #fecaca; color: #dc2626; }
        .provider-button-danger:hover { background: #fef2f2; }
        .provider-alert { margin-bottom: 18px; padding: 12px 15px; border-radius: 9px; font-size: 14px; }
        .provider-alert-success { background: #f0fdf4; color: #166534; border: 1px solid #bbf7d0; }
        .provider-list { display: grid; gap: 14px; }
        .provider-card { padding: 17px; border: 1px solid #e5e7eb; border-radius: 11px; }
        .provider-card-header { display: flex; justify-content: space-between; align-items: flex-start; gap: 12px; margin-bottom: 13px; }
        .provider-card h3 { margin: 0 0 4px; font-size: 15px; font-weight: 700; }
        .provider-type { color: #6b7280; font-size: 12px; text-transform: capitalize; }
        .provider-actions { display: flex; gap: 8px; flex-wrap: wrap; }
        .provider-actions .provider-button { padding: 7px 10px; font-size: 12px; }
        .provider-details { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 10px 18px; margin: 0; }
        .provider-details div { min-width: 0; }
        .provider-details dt { color: #6b7280; font-size: 11px; text-transform: uppercase; letter-spacing: .35px; }
        .provider-details dd { margin: 3px 0 0; overflow-wrap: anywhere; font-size: 13px; }
        .provider-notes { grid-column: 1 / -1; white-space: pre-wrap; }
        .provider-empty { padding: 30px 15px; color: #6b7280; text-align: center; font-size: 14px; }
        @media (max-width: 760px) {
            .providers-grid { grid-template-columns: 1fr; }
            .provider-details { grid-template-columns: 1fr; }
            .provider-notes { grid-column: auto; }
        }
    </style>

    <div class="providers-page">
        <header class="providers-header">
            <h1>Utility Providers</h1>
            <p>Keep your utility provider and account details in one place.</p>
        </header>

        @if (session('success'))
            <div class="provider-alert provider-alert-success" role="status">{{ session('success') }}</div>
        @endif

        <div class="providers-grid">
            <section class="providers-panel">
                <h2>Add a provider</h2>
                <form class="provider-form" method="POST" action="{{ route('tenant.utility-providers.store') }}">
                    @csrf
                    <div>
                        <label for="new-name">Provider name</label>
                        <input id="new-name" name="name" value="{{ old('name') }}" required maxlength="255">
                        @error('name') <div class="field-error">{{ $message }}</div> @enderror
                    </div>
                    <div>
                        <label for="new-type">Utility type</label>
                        <select id="new-type" name="type" required>
                            <option value="">Select a type</option>
                            @foreach (['electricity', 'water', 'gas', 'internet', 'trash', 'other'] as $type)
                                <option value="{{ $type }}" @selected(old('type') === $type)>{{ ucfirst($type) }}</option>
                            @endforeach
                        </select>
                        @error('type') <div class="field-error">{{ $message }}</div> @enderror
                    </div>
                    <div>
                        <label for="new-account-number">Account number</label>
                        <input id="new-account-number" name="account_number" value="{{ old('account_number') }}" maxlength="255">
                        @error('account_number') <div class="field-error">{{ $message }}</div> @enderror
                    </div>
                    <div>
                        <label for="new-contact-phone">Contact phone</label>
                        <input id="new-contact-phone" name="contact_phone" type="tel" value="{{ old('contact_phone') }}" maxlength="50">
                        @error('contact_phone') <div class="field-error">{{ $message }}</div> @enderror
                    </div>
                    <div>
                        <label for="new-contact-email">Contact email</label>
                        <input id="new-contact-email" name="contact_email" type="email" value="{{ old('contact_email') }}" maxlength="255">
                        @error('contact_email') <div class="field-error">{{ $message }}</div> @enderror
                    </div>
                    <div>
                        <label for="new-notes">Notes</label>
                        <textarea id="new-notes" name="notes" maxlength="2000">{{ old('notes') }}</textarea>
                        @error('notes') <div class="field-error">{{ $message }}</div> @enderror
                    </div>
                    <button class="provider-button" type="submit">Add provider</button>
                </form>
            </section>

            <section class="providers-panel">
                <h2>Your providers</h2>
                @if ($providers->isEmpty())
                    <div class="provider-empty">No utility providers added yet.</div>
                @else
                    <div class="provider-list">
                        @foreach ($providers as $provider)
                            <article class="provider-card">
                                <header class="provider-card-header">
                                    <div>
                                        <h3>{{ $provider->name }}</h3>
                                        <div class="provider-type">{{ $provider->type }}</div>
                                    </div>
                                    <div class="provider-actions">
                                        <form method="POST" action="{{ route('tenant.utility-providers.destroy', $provider) }}" onsubmit="return confirm('Remove this utility provider?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="provider-button provider-button-danger" type="submit">Remove</button>
                                        </form>
                                    </div>
                                </header>
                                <dl class="provider-details">
                                    @if ($provider->account_number)
                                        <div><dt>Account number</dt><dd>{{ $provider->account_number }}</dd></div>
                                    @endif
                                    @if ($provider->contact_phone)
                                        <div><dt>Contact phone</dt><dd>{{ $provider->contact_phone }}</dd></div>
                                    @endif
                                    @if ($provider->contact_email)
                                        <div><dt>Contact email</dt><dd>{{ $provider->contact_email }}</dd></div>
                                    @endif
                                    @if ($provider->notes)
                                        <div class="provider-notes"><dt>Notes</dt><dd>{{ $provider->notes }}</dd></div>
                                    @endif
                                </dl>
                            </article>
                        @endforeach
                    </div>
                @endif
            </section>
        </div>
    </div>
@endsection
