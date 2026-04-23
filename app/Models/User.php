<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    public function collections() {
    return $this->belongsToMany(Card::class, 'collections')->withTimestamps();
}

public function decks() {
    return $this->hasMany(Deck::class);
}
// The user's saved favorites
    public function wishlist() {
        return $this->belongsToMany(Card::class, 'wishlist')->withTimestamps();
    }

    // The decks the user has built
    public function decks() {
        return $this->hasMany(Deck::class);
    }
}
