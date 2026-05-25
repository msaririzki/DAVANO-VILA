<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['booking_id', 'booking_addon_id', 'type', 'amount', 'bank_account_id', 'validated_by', 'validated_at', 'note'])]
class Payment extends Model
{
    public const TYPE_BOOKING_DP = 'booking_dp';

    public const TYPE_BOOKING_LUNAS = 'booking_lunas';

    public const TYPE_ADDON = 'addon';

    public const TYPE_REFUND = 'refund';

    public const TYPE_ADJUSTMENT = 'adjustment';

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'validated_at' => 'datetime',
        ];
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function bookingAddon(): BelongsTo
    {
        return $this->belongsTo(BookingAddon::class);
    }

    public function bankAccount(): BelongsTo
    {
        return $this->belongsTo(BankAccount::class);
    }

    public function validator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'validated_by');
    }
}
