<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Support\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Validation\ValidationException;

class BookingReceiptShareController extends Controller
{
    public function __invoke(Request $request, Booking $booking): RedirectResponse
    {
        $receiptUrl = URL::temporarySignedRoute(
            'public.bookings.receipt',
            now()->addDays(30),
            ['booking' => $booking->public_token],
        );
        $phone = $this->normalizeWhatsappNumber($booking->guest_phone);

        if (strlen($phone) < 10) {
            throw ValidationException::withMessages([
                'guest_phone' => 'Nomor WhatsApp tamu tidak valid. Perbaiki nomor tamu sebelum mengirim resi.',
            ]);
        }

        $paymentLabel = match ($booking->payment_status) {
            Booking::PAYMENT_LUNAS => 'LUNAS',
            Booking::PAYMENT_DP => 'DP DITERIMA',
            Booking::PAYMENT_CANCELLED => 'DIBATALKAN',
            default => 'MENUNGGU PEMBAYARAN',
        };
        $message = implode("\n", [
            'Halo '.$booking->guest_name.',',
            '',
            'Berikut resi/tagihan resmi reservasi Villa Dafano:',
            'Kode booking: '.$booking->booking_code,
            'Status pembayaran: '.$paymentLabel,
            'Total tagihan: Rp '.number_format((float) $booking->grand_total, 0, ',', '.'),
            'Sudah dibayar: Rp '.number_format((float) $booking->paid_amount, 0, ',', '.'),
            'Sisa tagihan: Rp '.number_format((float) $booking->balance_due, 0, ',', '.'),
            '',
            'Buka atau unduh PDF:',
            $receiptUrl,
            '',
            'Terima kasih.',
        ]);

        AuditLogger::record(
            $request,
            'booking.receipt_whatsapp_opened',
            'Membuka WhatsApp untuk mengirim resi pemesanan '.$booking->booking_code.' kepada '.$booking->guest_phone,
            $booking,
            null,
            [
                'guest_phone' => $booking->guest_phone,
                'receipt_url_expires_at' => now()->addDays(30)->toIso8601String(),
            ],
        );

        return redirect()->away('https://wa.me/'.$phone.'?text='.rawurlencode($message));
    }

    private function normalizeWhatsappNumber(string $phone): string
    {
        $digits = preg_replace('/\D+/', '', $phone) ?: '';

        if (str_starts_with($digits, '0')) {
            return '62'.substr($digits, 1);
        }

        if (str_starts_with($digits, '8')) {
            return '62'.$digits;
        }

        return $digits;
    }
}
