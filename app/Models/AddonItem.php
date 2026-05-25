<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['name', 'type', 'price', 'is_active'])]
class AddonItem extends Model
{
    public const TYPE_FOOD = 'food';

    public const TYPE_EXTRA_BED = 'extrabed';

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }
}
