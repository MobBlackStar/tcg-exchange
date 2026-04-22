<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Deck extends Model
{
    protected $fillable = ['user_id', 'name'];

    public function cards() {
        return $this->belongsToMany(Card::class, 'deck_card')->withPivot('quantity');
    }
}

