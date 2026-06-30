<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'booking_id',
    'booking_addon_id',
    'type',
    'amount',
    'bank_account_id',
    'transfer_reference',
    'validated_by',
    'validated_at',
    'note',
    'proof_path',
    'proof_sha256',
    'ocr_confidence',
    'ocr_detected_amount',
    'ocr_detected_reference',
    'resolution_status',
    'resolution_note',
    'resolved_by',
    'resolved_at',
])]
class Payment extends Model
{
    public const TYPE_BOOKING_DP = 'booking_dp';

    public const TYPE_BOOKING_LUNAS = 'booking_lunas';

    public const TYPE_ADDON = 'addon';

    public const TYPE_REFUND = 'refund';

    public const TYPE_ADJUSTMENT = 'adjustment';

    public const TYPE_TRANSFER_ISSUE = 'transfer_issue';

    public const TYPE_TRANSFER_ISSUE_REFUND = 'transfer_issue_refund';

    public const RESOLUTION_UNRESOLVED = 'unresolved';

    public const RESOLUTION_ACCEPTED = 'accepted';

    public const RESOLUTION_REFUNDED = 'refunded';

    public const INCOMING_TYPES = [
        self::TYPE_BOOKING_DP,
        self::TYPE_BOOKING_LUNAS,
        self::TYPE_ADDON,
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'ocr_detected_amount' => 'decimal:2',
            'ocr_confidence' => 'integer',
            'validated_at' => 'datetime',
            'resolved_at' => 'datetime',
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
