<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_leaves', function (Blueprint $table) {
            $table->id();
            $table->foreignId('unit_id')->constrained('units');
            $table->foreignId('student_id')->constrained('students');
            $table->date('date');
            $table->enum('status', ['sakit', 'izin']);
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
            $table->unique(['student_id', 'date']);
            $table->index('date');
            $table->index('unit_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_leaves');
    }
};
