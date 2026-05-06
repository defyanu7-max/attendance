<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttendanceLog extends Model
{
    // No SoftDeletes — audit log must never be deleted
    public $timestamps = false;

    protected $fillable = [
        'attendance_id', 'changed_by', 'old_status', 'new_status', 'reason', 'changed_at',
    ];

    protected $casts = [
        'changed_at' => 'datetime',
    ];

    public function attendance(): BelongsTo
    {
        return $this->belongsTo(StudentAttendance::class, 'attendance_id');
    }

    public function changer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
