<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\BookingAddon;
use App\Support\BusinessProfile;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;

class BookingInvoiceController extends Controller
{
    public function download(Booking $booking): Response
    {
        return $this->makePdf($booking)->download($this->filename($booking));
    }

    public function public(Booking $booking): Response
    {
        return $this->makePdf($booking)->stream($this->filename($booking));
    }

    private function makePdf(Booking $booking)
    {
        $booking->load(['room', 'units', 'addons', 'payments.bankAccount', 'payments.validator']);

        return Pdf::loadView('bookings.invoice', [
            'booking' => $booking,
            'groupedAddons' => $booking->addons
                ->where('payment_status', '!=', BookingAddon::PAYMENT_CANCELLED)
                ->groupBy(fn ($addon) => $addon->category ?: $addon->type),
            'logoDataUri' => $this->dataUri(public_path('dafano-media/brand/dafano-logo.png')),
            'signatureDataUri' => $this->dataUri(public_path('dafano-media/brand/signature-dafano.png')),
            'issuedAt' => now(),
            'businessProfile' => BusinessProfile::all(),
        ])->setPaper('a4');
    }

    private function filename(Booking $booking): string
    {
        $prefix = (float) $booking->balance_due <= 0 ? 'kwitansi' : 'tagihan';

        return $prefix.'-dafano-villa-'.$booking->booking_code.'.pdf';
    }

    private function dataUri(string $path): ?string
    {
        if (! is_file($path)) {
            return null;
        }

        $mime = mime_content_type($path) ?: 'image/png';

        return 'data:'.$mime.';base64,'.base64_encode((string) file_get_contents($path));
    }
}
