<?php

namespace App\Models;

use App\Models\Scopes\UnitScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotificationQueue extends Model
{
    protected $table = 'notification_queue';

    protected $fillable = [
        'unit_id', 'student_id', 'alpha_count', 'phone', 'type',
        'message', 'status', 'triggered_at', 'sent_at', 'sent_by',
    ];

    protected $casts = [
        'triggered_at' => 'datetime',
        'sent_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope(new UnitScope());
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * The user who manually sent/dismissed this notification.
     * (renamed from sender() to avoid ambiguity)
     */
    public function sentByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sent_by');
    }
}
