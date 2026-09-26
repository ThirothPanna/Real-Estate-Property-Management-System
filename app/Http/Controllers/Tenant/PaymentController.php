<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Notification;
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
            'method'       => ['required', 'in:card'],
            'card_number'  => ['required', 'string', 'min:13', 'max:25'],
            'card_expiry'  => ['required', 'string', 'max:5'],
            'card_cvc'     => ['required', 'string', 'min:3', 'max:4'],
            'card_name'    => ['required', 'string', 'max:255'],
        ]);

        $digits = preg_replace('/\D/', '', $validated['card_number']);
        $last4  = substr($digits, -4);

        $payment = Payment::create([
            'user_id'     => Auth::id(),
            'amount'      => $validated['amount'],
            'paid_on'     => $validated['paid_on'],
            'method'      => 'card',
            'status'      => 'completed',
            'category'    => 'Rent',
            'card_last4'  => $last4,
            'card_expiry' => $validated['card_expiry'],
            'card_name'   => $validated['card_name'],
        ]);

        Notification::create([
            'user_id' => Auth::id(),
            'title'   => 'Payment received',
            'body'    => 'Your $' . number_format($payment->amount, 2) . ' payment was recorded.',
            'icon'    => 'payment',
        ]);

        return redirect()
            ->route('tenant.dashboard')
            ->with('payment_success', $payment->amount);
    }
}