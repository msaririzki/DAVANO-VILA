<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PaymentProofController extends Controller
{
    public function __invoke(Payment $payment): StreamedResponse
    {
        abort_unless(
            $payment->proof_path && Storage::disk('local')->exists($payment->proof_path),
            404,
        );

        return Storage::disk('local')->response(
            $payment->proof_path,
            'bukti-transfer-'.$payment->id.'.webp',
            [
                'Cache-Control' => 'private, max-age=300',
                'Content-Disposition' => 'inline',
                'X-Content-Type-Options' => 'nosniff',
            ],
        );
    }
}
