<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Category; 


class Card extends Model
{
    use HasFactory;

    // The Guest List: Allowed to be mass-assigned
    protected $fillable =[
        'category_id', 
        'passcode', 
        'name', 
        'type', 
        'description', 
        'image_url'
    ];
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    public function listings()
    {
        return $this->hasMany(Listing::class);
    }
    // [TECH LEAD FIX]: The link Sarah's UI needs to check if a card is favorited
    public function wishlistedBy()
    {
        return $this->belongsToMany(User::class, 'wishlist')->withTimestamps();
    }
}