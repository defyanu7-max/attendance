<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    // No UnitScope — global config
    public $timestamps = false;

    protected $fillable = ['key', 'value', 'type', 'description', 'updated_at'];

    protected $casts = [
        'updated_at' => 'datetime',
    ];

    /**
     * Get a setting value by key with optional default.
     */
    public static function getValue(string $key, mixed $default = null): mixed
    {
        $setting = static::where('key', $key)->first();
        if (! $setting) return $default;

        return match ($setting->type) {
            'integer' => (int) $setting->value,
            'boolean' => filter_var($setting->value, FILTER_VALIDATE_BOOLEAN),
            'json'    => json_decode($setting->value, true),
            default   => $setting->value,
        };
    }

    /**
     * Set a setting value by key.
     */
    public static function setValue(string $key, mixed $value): void
    {
        $strValue = is_array($value) ? json_encode($value) : (string) $value;
        $type = is_array($value) ? 'json' : (in_array($strValue, ['true', 'false'], true) ? 'boolean' : (is_numeric($strValue) ? 'integer' : 'string'));

        static::updateOrCreate(
            ['key' => $key],
            [
                'value' => $strValue,
                'type' => static::where('key', $key)->value('type') ?? $type,
                'updated_at' => now(),
            ]
        );
    }
}
