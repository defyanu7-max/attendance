<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('school_calendars', function (Blueprint $table) {
            $table->id();
            $table->date('date')->unique();
            $table->enum('type', ['holiday', 'special_event']);
            $table->string('description', 150)->nullable();
            $table->foreignId('unit_id')->nullable()->constrained('units');
            $table->timestamps();
            $table->index(['date', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('school_calendars');
    }
};
