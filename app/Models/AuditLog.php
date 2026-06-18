<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use LogicException;

#[Fillable([
    'user_id',
    'action',
    'category',
    'is_financial',
    'auditable_type',
    'auditable_id',
    'summary',
    'old_values',
    'new_values',
    'ip_address',
    'user_agent',
])]
class AuditLog extends Model
{
    public $timestamps = false;

    protected static function booted(): void
    {
        static::updating(function (): never {
            throw new LogicException('Audit log tidak boleh diedit.');
        });

        static::deleting(function (): never {
            throw new LogicException('Audit log tidak boleh dihapus.');
        });
    }

    protected function casts(): array
    {
        return [
            'is_financial' => 'boolean',
            'old_values' => 'array',
            'new_values' => 'array',
            'created_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
