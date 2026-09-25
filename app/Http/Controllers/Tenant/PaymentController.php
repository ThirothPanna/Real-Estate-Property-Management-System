<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    public function create()
    {
        return view('tenant.pay');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'amount'       => ['required', 'numeric', 'min:1', 'max:100000'],
            'paid_on'      => ['required', 'date'],
            'method'       => ['required', 'in:card,aba_khqr,cash'],

            // card fields required only if method=card
            'card_number'  => ['required_if:method,card', 'nullable', 'string', 'min:13', 'max:25'],
            'card_expiry'  => ['required_if:method,card', 'nullable', 'string', 'max:5'],
            'card_cvc'     => ['required_if:method,card', 'nullable', 'string', 'min:3', 'max:4'],
            'card_name'    => ['required_if:method,card', 'nullable', 'string', 'max:255'],
        ]);

        // Save only the last 4 digits (never the full number)
        $last4 = null;
        if (!empty($validated['card_number'])) {
            $digits = preg_replace('/\D/', '', $validated['card_number']);
            $last4  = substr($digits, -4);
        }

        // ABA KHQR payments are marked as pending until confirmed
        $status = $validated['method'] === 'aba_khqr' ? 'pending' : 'completed';

        $payment = Payment::create([
            'user_id'      => Auth::id(),
            'amount'       => $validated['amount'],
            'paid_on'      => $validated['paid_on'],
            'method'       => $validated['method'],
            'status'       => $status,
            'category'     => 'Rent',
            'card_last4'   => $last4,
            'card_expiry'  => $validated['card_expiry'] ?? null,
            'card_name'    => $validated['card_name'] ?? null,
        ]);

        return redirect()
            ->route('tenant.dashboard')
            ->with('payment_success', $payment->amount);
    }
}