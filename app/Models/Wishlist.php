<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Wishlist extends Model
{
    // The Guest List
    protected $fillable =['user_id', 'card_id'];

    // The Bridge: A wishlist item belongs to a specific Card
    public function card()
    {
        return $this->belongsTo(Card::class);
    }
}