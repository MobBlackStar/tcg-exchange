<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Listing extends Model
{
    use HasFactory;

    // The Mass Assignment Guest List
    protected $fillable = [
        'seller_id',
        'card_id',
        'condition',
        'price',
        'quantity',
        'photo_path',
        'is_active'
    ];

    // [Eloquent Relationship]: A listing belongs to a specific Yu-Gi-Oh Card
    public function card()
    {
        return $this->belongsTo(Card::class);
    }

    // [Eloquent Relationship]: A listing belongs to the user who is selling it
    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }
}