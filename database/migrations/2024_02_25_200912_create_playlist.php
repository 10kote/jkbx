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
        Schema::create('playlist', function (Blueprint $table) {
            $table->id()->unique();
            $table->unsignedBigInteger('track_id');
            $table->unsignedBigInteger('artist_id');
            $table->unsignedInteger('position');
            $table->boolean('playing')->default(false);
            $table->foreign('track_id')->references('id')->on('tracks');
            $table->foreign('artist_id')->references('id')->on('artists');
            $table->unique(['track_id', 'artist_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('playlist');
    }
};
