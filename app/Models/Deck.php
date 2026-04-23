<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Deck extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'name', 'preview_card_id'];

    // [GOD-TIER]: This defines the relationship Laravel was complaining about
    public function cards()
{
    return $this->belongsToMany(\App\Models\Card::class, 'deck_card')
                ->withPivot('quantity', 'location') // Ensure these are loaded
                ->withTimestamps();
}

    public function previewCard()
    {
        return $this->belongsTo(Card::class, 'preview_card_id');
    }
}