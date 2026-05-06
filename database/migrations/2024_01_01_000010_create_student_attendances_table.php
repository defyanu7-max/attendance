<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('unit_id')->constrained('units');
            $table->foreignId('student_id')->constrained('students');
            $table->foreignId('schedule_id')->constrained('schedules');
            $table->foreignId('academic_year_id')->constrained('academic_years');
            $table->date('date');
            $table->enum('status', ['hadir', 'alpha', 'sakit', 'izin']);
            $table->boolean('is_merged')->default(false);
            $table->foreignId('merged_from_class_id')->nullable()->constrained('classes');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->unique(['student_id', 'schedule_id', 'date']);
            $table->index(['student_id', 'date']);
            $table->index(['schedule_id', 'date']);
            $table->index('unit_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_attendances');
    }
};
