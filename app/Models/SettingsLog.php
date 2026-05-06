<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SettingsLog extends Model
{
    // No SoftDeletes — audit log
    public $timestamps = false;

    protected $table = 'settings_logs';
    protected $fillable = ['key', 'old_value', 'new_value', 'changed_by', 'created_at'];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function changer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }

    public function changedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }

    protected static function booted(): void
    {
        static::creating(function ($log) {
            $log->created_at = $log->created_at ?? now();
        });
    }
}
