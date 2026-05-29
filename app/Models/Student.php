<?php

namespace App\Models;

use App\Models\Scopes\UnitScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Student extends Model
{
    use SoftDeletes;

    protected $fillable = ['nis', 'nisn', 'name', 'unit_id', 'status'];
    protected $casts = ['deleted_at' => 'datetime'];

    protected static function booted(): void
    {
        static::addGlobalScope(new UnitScope());
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function classes(): BelongsToMany
    {
        return $this->belongsToMany(Classes::class, 'class_student', 'student_id', 'class_id')
            ->withPivot(['academic_year_id', 'enrolled_at'])
            ->withTimestamps();
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(StudentAttendance::class);
    }

    public function leaves(): HasMany
    {
        return $this->hasMany(StudentLeave::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'aktif');
    }

    public function isOnLeaveToday(): bool
    {
        return $this->leaves()->whereDate('date', today())->exists();
    }

    /**
     * Get the current class for the active academic year.
     * NOTE: Jangan panggil ini di dalam Observer/Transaction karena
     * AcademicYear::active() menembak query di dalam DB transaction.
     * Gunakan currentClassForYear($yearId) sebagai gantinya.
     */
    public function currentClass(): ?Classes
    {
        $activeYear = AcademicYear::active();
        if (! $activeYear) return null;

        return $this->currentClassForYear($activeYear->id);
    }

    /**
     * Get the current class for a specific academic year ID.
     * Aman dipanggil di dalam Observer/Transaction karena tidak
     * membuat query AcademicYear tambahan.
     *
     * @param int $academicYearId
     */
    public function currentClassForYear(int $academicYearId): ?Classes
    {
        return $this->classes()
            ->wherePivot('academic_year_id', $academicYearId)
            ->first();
    }
}
