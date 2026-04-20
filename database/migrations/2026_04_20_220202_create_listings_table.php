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
        Schema::create('listings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('seller_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('card_id')->constrained('cards')->onDelete('cascade');
            
            // E-Commerce Data
            $table->string('condition'); // Mint, Near Mint, Lightly Played, Damaged
            $table->decimal('price', 10, 2); // Supports currency like DT
            $table->integer('quantity')->default(1);
            $table->string('photo_path')->nullable(); // Satisfies "Image optionnel" rubric
            $table->boolean('is_active')->default(true); // Can hide if sold out
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('listings');
    }
};
