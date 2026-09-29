<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\Payment;
use App\Models\Tenancy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    public function create()
    {
        $defaultAmount = Tenancy::where('user_id', Auth::id())
            ->where('status', 'active')
            ->sum('rent_amount');

        return view('tenant.pay', compact('defaultAmount'));
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

        $payment = DB::transaction(function () use ($validated, $digits) {
            $payment = Payment::create([
                'user_id'     => Auth::id(),
                'amount'      => $validated['amount'],
                'paid_on'     => $validated['paid_on'],
                'method'      => 'card',
                'status'      => 'completed',
                'category'    => 'Rent',
                'card_last4'  => substr($digits, -4),
                'card_expiry' => $validated['card_expiry'],
                'card_name'   => $validated['card_name'],
            ]);

            $amount = number_format((float) $payment->amount, 2);
            Notification::create([
                'user_id' => $payment->user_id,
                'title'   => 'Payment received',
                'body'    => 'Your $' . $amount . ' payment was recorded.',
                'icon'    => 'payment',
            ]);

            $activeTenancies = Tenancy::with('property')
                ->where('user_id', $payment->user_id)
                ->where('status', 'active')
                ->get();

            foreach ($activeTenancies->groupBy('landlord_id') as $landlordTenancies) {
                $properties = $landlordTenancies->pluck('property.name')->filter()->unique()->implode(', ');
                $propertyText = $properties ? ' for ' . $properties : '';

                Notification::create([
                    'user_id' => $landlordTenancies->first()->landlord_id,
                    'title'   => 'Tenant rent payment received',
                    'body'    => $payment->user->name . ' recorded a $' . $amount . ' rent payment' . $propertyText . '.',
                    'icon'    => 'payment',
                ]);
            }

            return $payment;
        });

        return redirect()
            ->route('tenant.dashboard')
            ->with('payment_success', $payment->amount);
    }
}
