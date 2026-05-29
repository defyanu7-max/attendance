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
     *
     * Satu query saja: `updateOrCreate` — tidak perlu query SELECT tambahan untuk `type`.
     * Untuk key yang sudah ada: type tidak berubah (DB menyimpan sesuai yang sudah terseed).
     * Untuk key baru: type di-infer dari nilai yang diberikan.
     */
    public static function setValue(string $key, mixed $value): void
    {
        $strValue = is_array($value) ? json_encode($value) : (string) $value;

        // Infer type dari nilai — hanya dipakai jika key belum ada (insert baru)
        $inferredType = match (true) {
            is_array($value)                              => 'json',
            in_array($strValue, ['true', 'false'], true) => 'boolean',
            is_numeric($strValue)                         => 'integer',
            default                                       => 'string',
        };

        static::updateOrCreate(
            ['key' => $key],
            [
                'value'      => $strValue,
                'type'       => $inferredType,
                'updated_at' => now(),
            ]
        );
    }
}
