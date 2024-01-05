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
        Schema::create('game', function (Blueprint $table) {
            $table->id();
            $table->string("game_title");
            $table->float("game_rating");
            $table->string("game_store_type");
            $table->float("game_price");
            $table->integer("game_discount");
            $table->string("game_image");
            $table->string("game_video_link");
            $table->longText("game_description");
            $table->string("game_developer");
            $table->string("game_publisher");
            $table->date("game_release_date");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('game');
    }
};
