<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings_logs', function (Blueprint $table) {
            $table->id();
            $table->string('key', 100);
            $table->text('old_value')->nullable();
            $table->text('new_value');
            $table->foreignId('changed_by')->constrained('users');
            $table->timestamp('created_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('settings_logs');
    }
};
