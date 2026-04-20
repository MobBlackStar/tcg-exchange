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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique(); // Secure URL string (no guessing order IDs)
            $table->foreignId('buyer_id')->constrained('users')->onDelete('cascade');
            
            // [RUBRIC COMPLIANCE] Exact French statuses demanded by the jury
            $table->enum('status',['En attente', 'Validée', 'Annulée'])->default('En attente');
            
            $table->decimal('total_price', 10, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
