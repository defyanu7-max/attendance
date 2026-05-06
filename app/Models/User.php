<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasFactory, SoftDeletes;

    /**
     * CATATAN: User model TIDAK menggunakan UnitScope (mencegah infinite loop)
     */

    protected $fillable = [
        'name',
        'username',
        'password',
        'role',
        'unit_id',
        'phone',
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'password' => 'hashed',
        'deleted_at' => 'datetime',
    ];

    // --- Relationships ---

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(Schedule::class, 'teacher_id');
    }

    public function teacherAttendances(): HasMany
    {
        return $this->hasMany(TeacherAttendance::class);
    }

    public function substitutions(): HasMany
    {
        return $this->hasMany(ScheduleSubstitution::class, 'substitute_user_id');
    }

    // --- Accessors ---

    public function isSuperadmin(): bool
    {
        return $this->role === 'superadmin';
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isWalikelas(): bool
    {
        return $this->role === 'walikelas';
    }

    public function isGuru(): bool
    {
        return $this->role === 'guru';
    }

    public function isAdminOrAbove(): bool
    {
        return in_array($this->role, ['superadmin', 'admin']);
    }
}
