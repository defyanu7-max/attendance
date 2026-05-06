<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('teacher_attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('unit_id')->constrained('units');
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('academic_year_id')->constrained('academic_years');
            $table->date('date');
            $table->enum('status', ['hadir', 'alpha', 'sakit', 'izin']);
            $table->boolean('is_auto')->default(false);
            $table->timestamps();
            $table->unique(['user_id', 'date']);
            $table->index(['user_id', 'date']);
            $table->index('unit_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('teacher_attendances');
    }
};
