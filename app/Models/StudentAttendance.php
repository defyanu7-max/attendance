<?php

namespace App\Models;

use App\Models\Scopes\UnitScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StudentAttendance extends Model
{
    // No SoftDeletes — attendance data is immutable

    protected $fillable = [
        'unit_id', 'student_id', 'schedule_id', 'academic_year_id',
        'date', 'status', 'is_merged', 'merged_from_class_id', 'created_by', 'notes',
    ];

    protected $casts = [
        'date' => 'date',
        'is_merged' => 'boolean',
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

    public function schedule(): BelongsTo
    {
        return $this->belongsTo(Schedule::class);
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function mergedFromClass(): BelongsTo
    {
        return $this->belongsTo(Classes::class, 'merged_from_class_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function logs(): HasMany
    {
        return $this->hasMany(AttendanceLog::class, 'attendance_id');
    }
}
