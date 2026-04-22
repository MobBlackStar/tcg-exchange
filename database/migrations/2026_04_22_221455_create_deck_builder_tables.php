<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void {
    // Stores the user's purchased cards (Vault)
    Schema::create('collections', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained();
        $table->foreignId('card_id')->constrained();
        $table->timestamps();
    });

    // Stores the actual Deck structures
    Schema::create('decks', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained();
        $table->string('name');
        $table->timestamps();
    });

    // Pivot for Deck contents
    Schema::create('deck_card', function (Blueprint $table) {
        $table->id();
        $table->foreignId('deck_id')->constrained()->onDelete('cascade');
        $table->foreignId('card_id')->constrained()->onDelete('cascade');
        $table->integer('quantity')->default(1);
        $table->timestamps();
    });
}
};

