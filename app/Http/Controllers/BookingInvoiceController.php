<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;

class BookingInvoiceController extends Controller
{
    public function __invoke(Booking $booking): Response
    {
        $booking->load(['room', 'units', 'addons', 'payments.bankAccount', 'payments.validator']);

        $pdf = Pdf::loadView('bookings.invoice', [
            'booking' => $booking,
            'groupedAddons' => $booking->addons->groupBy(fn ($addon) => $addon->category ?: $addon->type),
            'logoDataUri' => $this->dataUri(public_path('dafano-media/brand/dafano-logo.png')),
            'signatureDataUri' => $this->dataUri(public_path('dafano-media/brand/signature-dafano.png')),
        ])->setPaper('a4');

        return $pdf->download('invoice-dafano-villa-'.$booking->booking_code.'.pdf');
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
