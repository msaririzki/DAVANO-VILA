<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

#[Fillable([
    'booking_code',
    'public_token',
    'guest_name',
    'guest_phone',
    'acquisition_source',
    'guest_request',
    'adult_count',
    'child_count',
    'total_guest_count',
    'room_id',
    'unit_count',
    'check_in_date',
    'check_out_date',
    'total_room_price',
    'total_addons_price',
    'occupancy_adjustment_amount',
    'occupancy_adjustment_note',
    'late_fee',
    'discount_amount',
    'discount_note',
    'grand_total',
    'paid_amount',
    'balance_due',
    'payment_status',
    'booking_status',
    'hold_expires_at',
    'cancelled_at',
    'cancellation_note',
])]
class Booking extends Model
{
    public const PAYMENT_PENDING = 'Pending';

    public const PAYMENT_DP = 'DP';

    public const PAYMENT_LUNAS = 'Lunas';

    public const PAYMENT_CANCELLED = 'Cancelled';

    public const STATUS_BOOKED = 'Booked';

    public const STATUS_IN_HOUSE = 'In-House';

    public const STATUS_COMPLETED = 'Completed';

    public const STATUS_NO_SHOW = 'No-Show';

    public const STATUS_CANCELLED = 'Cancelled';

    protected static function booted(): void
    {
        static::creating(function (Booking $booking): void {
            if (! $booking->public_token) {
                do {
                    $token = Str::random(48);
                } while (self::query()->where('public_token', $token)->exists());

                $booking->public_token = $token;
            }
        });
    }

    protected function casts(): array
    {
        return [
            'check_in_date' => 'date',
            'check_out_date' => 'date',
            'adult_count' => 'integer',
            'child_count' => 'integer',
            'total_guest_count' => 'integer',
            'unit_count' => 'integer',
            'total_room_price' => 'decimal:2',
            'total_addons_price' => 'decimal:2',
            'occupancy_adjustment_amount' => 'decimal:2',
            'late_fee' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'grand_total' => 'decimal:2',
            'paid_amount' => 'decimal:2',
            'balance_due' => 'decimal:2',
            'cancelled_at' => 'datetime',
            'hold_expires_at' => 'datetime',
        ];
    }

    public function hasActiveHold(): bool
    {
        return $this->payment_status === self::PAYMENT_PENDING
            && $this->booking_status === self::STATUS_BOOKED
            && $this->hold_expires_at
            && $this->hold_expires_at->isFuture();
    }

    public function hasExpiredHold(): bool
    {
        return $this->payment_status === self::PAYMENT_PENDING
            && $this->booking_status === self::STATUS_BOOKED
            && $this->hold_expires_at
            && $this->hold_expires_at->isPast();
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function addons(): HasMany
    {
        return $this->hasMany(BookingAddon::class);
    }

    public function units(): BelongsToMany
    {
        return $this->belongsToMany(RoomUnit::class, 'booking_room_unit')->withTimestamps();
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function recalculateTotals(): void
    {
        $wasFinanciallyConfirmed = in_array($this->payment_status, [self::PAYMENT_DP, self::PAYMENT_LUNAS], true);

        $this->total_addons_price = $this->addons()
            ->where('payment_status', '!=', BookingAddon::PAYMENT_CANCELLED)
            ->sum('subtotal');
        $this->grand_total = max(
            0,
            (float) $this->total_room_price
            + (float) $this->total_addons_price
            + (float) $this->occupancy_adjustment_amount
            + (float) $this->late_fee
            - (float) $this->discount_amount
        );
        $incomingAmount = (float) $this->payments()
            ->whereIn('type', Payment::INCOMING_TYPES)
            ->sum('amount');
        $refundAmount = (float) $this->payments()
            ->where('type', Payment::TYPE_REFUND)
            ->sum('amount');

        $this->paid_amount = max(0, $incomingAmount - $refundAmount);

        if ($this->booking_status === self::STATUS_CANCELLED) {
            $this->balance_due = 0;
            $this->payment_status = self::PAYMENT_CANCELLED;

            return;
        }

        $this->balance_due = max(0, (float) $this->grand_total - (float) $this->paid_amount);
        $minimumDpAmount = (float) $this->grand_total * ((int) Setting::value('min_dp_percent', 50) / 100);

        if (
            (float) $this->paid_amount < $minimumDpAmount
            && (float) $this->balance_due > 0
            && (! $wasFinanciallyConfirmed || (float) $this->paid_amount <= 0)
        ) {
            $this->payment_status = self::PAYMENT_PENDING;
        } elseif ((float) $this->balance_due <= 0) {
            $this->payment_status = self::PAYMENT_LUNAS;
        } else {
            $this->payment_status = self::PAYMENT_DP;
        }
    }
}
