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
        Schema::create('cards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('categories')->onDelete('cascade');
            $table->bigInteger('passcode')->unique(); // The YGOPRODeck ID (e.g., 89631139 for Blue-Eyes)
            $table->string('name');
            $table->string('type')->nullable(); // e.g., 'Normal Monster', 'Continuous Trap'
            $table->text('description');
            $table->string('image_url'); // Fetched from the API
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cards');
    }
};
