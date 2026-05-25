<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['key_name', 'value'])]
class Setting extends Model
{
    public static function value(string $key, mixed $default = null): mixed
    {
        return static::query()->where('key_name', $key)->value('value') ?? $default;
    }
}
