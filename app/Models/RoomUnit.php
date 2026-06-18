<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[Fillable(['room_id', 'name', 'status', 'is_active', 'notes'])]
class RoomUnit extends Model
{
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function bookings(): BelongsToMany
    {
        return $this->belongsToMany(Booking::class, 'booking_room_unit')->withTimestamps();
    }

    public function scopeOperational(Builder $query): Builder
    {
        return $query
            ->where('is_active', true)
            ->where('status', Room::STATUS_AVAILABLE);
    }
}
