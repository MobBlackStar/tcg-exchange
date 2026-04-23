<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void {
    Schema::table('decks', function (Blueprint $table) {
        $table->foreignId('preview_card_id')->nullable()->constrained('cards')->onDelete('set null');
    });
}
};
