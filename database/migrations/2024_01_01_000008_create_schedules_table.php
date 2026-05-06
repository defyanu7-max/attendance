<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('unit_id')->constrained('units');
            $table->foreignId('class_id')->constrained('classes');
            $table->foreignId('subject_id')->constrained('subjects');
            $table->foreignId('teacher_id')->constrained('users');
            $table->foreignId('academic_year_id')->constrained('academic_years');
            $table->tinyInteger('day_of_week')->unsigned(); // 0=Sun, 1=Mon..6=Sat
            $table->time('start_time');
            $table->time('end_time');
            $table->string('unique_code', 30)->nullable()->unique(); // 'BIND-10A-25'
            $table->softDeletes();
            $table->timestamps();
            $table->index(['teacher_id', 'day_of_week']);
            $table->index(['class_id', 'day_of_week']);
            $table->index('unit_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('schedules');
    }
};
