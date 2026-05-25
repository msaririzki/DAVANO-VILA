<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['booking_id', 'addon_item_id', 'item_name', 'type', 'qty', 'price', 'subtotal', 'payment_status'])]
class BookingAddon extends Model
{
    public const TYPE_FOOD = 'food';

    public const TYPE_EXTRA_BED = 'extrabed';

    public const PAYMENT_PENDING = 'Pending';

    public const PAYMENT_PAID = 'Paid';

    public const PAYMENT_CANCELLED = 'Cancelled';

    protected function casts(): array
    {
        return [
            'qty' => 'integer',
            'price' => 'decimal:2',
            'subtotal' => 'decimal:2',
        ];
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function addonItem(): BelongsTo
    {
        return $this->belongsTo(AddonItem::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }
}
