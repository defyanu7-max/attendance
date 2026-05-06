<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SchoolCalendar extends Model
{
    // No UnitScope — filtered manually (unit_id nullable = global)

    protected $fillable = ['date', 'type', 'description', 'unit_id'];

    protected $casts = [
        'date' => 'date',
    ];

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    /**
     * Check if a given date is a holiday.
     */
    public static function isHoliday(\DateTimeInterface $date, ?int $unitId = null): bool
    {
        $query = static::where('date', $date->format('Y-m-d'))
            ->where('type', 'holiday');

        if ($unitId) {
            $query->where(function ($q) use ($unitId) {
                $q->whereNull('unit_id')
                  ->orWhere('unit_id', $unitId);
            });
        }

        return $query->exists();
    }
}
