<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;

class ReceiptController extends Controller
{
    public function download(Payment $payment)
    {
        if ($payment->user_id !== Auth::id()) {
            abort(403);
        }

        $user = Auth::user();

        $pdf = Pdf::loadView('tenant.receipt', [
            'payment' => $payment,
            'user'    => $user,
        ]);

        $pdf->setPaper('A4', 'portrait');

        $filename = 'receipt-' . str_pad($payment->id, 6, '0', STR_PAD_LEFT)
                  . '-' . $payment->paid_on->format('Y-m-d') . '.pdf';

        return $pdf->download($filename);
    }
}