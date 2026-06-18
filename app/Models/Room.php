<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

#[Fillable([
    'name',
    'description',
    'price',
    'capacity',
    'included_capacity',
    'max_capacity',
    'allow_unit_quantity',
    'extra_guest_charge_mode',
    'extra_guest_adult_price',
    'extra_guest_child_price',
    'capacity_rule_note',
    'facilities',
    'status',
    'image_path',
    'is_active',
])]
class Room extends Model
{
    public const STATUS_AVAILABLE = 'Available';

    public const STATUS_CLEANING = 'Cleaning';

    public const STATUS_MAINTENANCE = 'Maintenance';

    protected static function booted(): void
    {
        static::creating(function (Room $room): void {
            $capacity = max(1, (int) ($room->capacity ?: 2));

            if (! $room->included_capacity) {
                $room->included_capacity = $capacity;
            }

            if (! $room->max_capacity) {
                $room->max_capacity = max($capacity, (int) $room->included_capacity);
            }

            if (! $room->extra_guest_charge_mode) {
                $room->extra_guest_charge_mode = 'manual';
            }
        });

        static::created(function (Room $room): void {
            if (Schema::hasTable('room_units') && $room->units()->count() === 0) {
                $room->units()->create([
                    'name' => $room->name.' 01',
                    'status' => $room->status,
                    'is_active' => $room->is_active,
                ]);
            }
        });
    }

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'capacity' => 'integer',
            'included_capacity' => 'integer',
            'max_capacity' => 'integer',
            'allow_unit_quantity' => 'boolean',
            'extra_guest_adult_price' => 'decimal:2',
            'extra_guest_child_price' => 'decimal:2',
            'facilities' => 'array',
            'is_active' => 'boolean',
        ];
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function units(): HasMany
    {
        return $this->hasMany(RoomUnit::class);
    }

    public function scopeAvailableBetween(Builder $query, string $checkIn, string $checkOut): Builder
    {
        $lockedStatuses = "'".Booking::PAYMENT_DP."', '".Booking::PAYMENT_LUNAS."'";

        return $query
            ->where('is_active', true)
            ->where('status', self::STATUS_AVAILABLE)
            ->whereRaw(
                "(select count(*) from room_units where room_units.room_id = rooms.id and room_units.is_active = 1 and room_units.status = ?) - coalesce((select sum(bookings.unit_count) from bookings where bookings.room_id = rooms.id and bookings.payment_status in ($lockedStatuses) and bookings.check_in_date < ? and bookings.check_out_date > ?), 0) > 0",
                [self::STATUS_AVAILABLE, $checkOut, $checkIn],
            );
    }

    public function activeUnitCount(): int
    {
        return $this->units()
            ->where('is_active', true)
            ->where('status', self::STATUS_AVAILABLE)
            ->count();
    }

    public function bookedUnitCount(string $checkIn, string $checkOut): int
    {
        return (int) $this->bookings()
            ->whereIn('payment_status', [Booking::PAYMENT_DP, Booking::PAYMENT_LUNAS])
            ->where('check_in_date', '<', $checkOut)
            ->where('check_out_date', '>', $checkIn)
            ->sum('unit_count');
    }

    public function availableUnitCount(?string $checkIn = null, ?string $checkOut = null): int
    {
        $activeUnits = $this->relationLoaded('units')
            ? $this->units->where('is_active', true)->where('status', self::STATUS_AVAILABLE)->count()
            : $this->activeUnitCount();

        if (! $checkIn || ! $checkOut) {
            return $activeUnits;
        }

        return max(0, $activeUnits - $this->bookedUnitCount($checkIn, $checkOut));
    }

    public function availableUnitsForAssignment(string $checkIn, string $checkOut, ?int $exceptBookingId = null)
    {
        return $this->units()
            ->operational()
            ->whereNotIn('id', function ($query) use ($checkIn, $checkOut, $exceptBookingId): void {
                $query->select('booking_room_unit.room_unit_id')
                    ->from('booking_room_unit')
                    ->join('bookings', 'bookings.id', '=', 'booking_room_unit.booking_id')
                    ->whereIn('bookings.payment_status', [Booking::PAYMENT_DP, Booking::PAYMENT_LUNAS])
                    ->where('bookings.check_in_date', '<', $checkOut)
                    ->where('bookings.check_out_date', '>', $checkIn);

                if ($exceptBookingId) {
                    $query->where('bookings.id', '!=', $exceptBookingId);
                }
            })
            ->orderBy('name')
            ->get();
    }

    public function imageUrl(): ?string
    {
        if (! $this->image_path) {
            return null;
        }

        if (Str::startsWith($this->image_path, ['http://', 'https://', '/'])) {
            return $this->image_path;
        }

        return Storage::url($this->image_path);
    }
}
