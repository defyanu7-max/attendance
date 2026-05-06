<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendance_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attendance_id')->constrained('student_attendances');
            $table->foreignId('changed_by')->constrained('users');
            $table->enum('old_status', ['hadir', 'alpha', 'sakit', 'izin']);
            $table->enum('new_status', ['hadir', 'alpha', 'sakit', 'izin']);
            $table->text('reason')->nullable();
            $table->timestamp('changed_at')->useCurrent();
            $table->index('attendance_id');
            $table->index('changed_by');
            $table->index('changed_at');
        });
        // No SoftDeletes — audit log must never be deleted
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_logs');
    }
};
