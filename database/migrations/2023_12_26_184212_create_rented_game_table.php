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
        Schema::create('rentedGame', function (Blueprint $table) {
            $table->id();
            $table->integer("userID");
            $table->foreignId('gameID')->constrained('game');
            $table->date("rentFrom");
            $table->date("rentTo");
            $table->float("totalPrice");
            $table->string("cardHolderName");
            $table->bigInteger("cardNumber");
            $table->string("status")->default("Rented");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rentedGame');
    }
};
