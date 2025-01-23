<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('cities', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('original_id')->unique();
            $table->string('name');
            $table->string('state')->default('');
            $table->char('country', 2);
            $table->float('lat', 10, 8);
            $table->float('lon', 10, 8);
            $table->timestamps();

            $table->index(['country', 'name']);
            $table->index(['lat', 'lon']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cities');
    }
};
