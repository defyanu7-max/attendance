<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('schedule_substitutions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('schedule_id')->constrained('schedules');
            $table->foreignId('substitute_user_id')->constrained('users');
            $table->date('date');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->unique(['schedule_id', 'date']);
            $table->index(['substitute_user_id', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('schedule_substitutions');
    }
};
