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
        return $this->receipt($payment)->download($this->filename($payment));
    }

    public function view(Payment $payment)
    {
        return $this->receipt($payment)->stream($this->filename($payment));
    }

    private function receipt(Payment $payment)
    {
        abort_unless($payment->user_id === Auth::id(), 403);

        $user = Auth::user();

        $pdf = Pdf::loadView('tenant.receipt', [
            'payment' => $payment,
            'user'    => $user,
        ]);

        $pdf->setPaper('A4', 'portrait');

        return $pdf;
    }

    private function filename(Payment $payment): string
    {
        return 'receipt-' . str_pad($payment->id, 6, '0', STR_PAD_LEFT)
            . '-' . $payment->paid_on->format('Y-m-d') . '.pdf';
    }
}