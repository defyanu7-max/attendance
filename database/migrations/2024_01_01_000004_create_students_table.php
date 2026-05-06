<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('nis', 20)->unique();
            $table->string('nisn', 10)->nullable();
            $table->string('name', 100);
            $table->foreignId('unit_id')->constrained('units');
            $table->enum('status', ['aktif', 'lulus', 'pindah', 'dikeluarkan'])->default('aktif');
            $table->softDeletes();
            $table->timestamps();
            $table->index(['unit_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
