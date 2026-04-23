<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Card;

class WishlistController extends Controller
{
    // 🧑‍🏫 Toggles a card in the user's wishlist via AJAX
    public function toggle(Request $request)
    {
        $request->validate(['card_id' => 'required|exists:cards,id']);

        $user = auth()->user();
        $cardId = $request->card_id;

        // Check if the card is already favorited
        $exists = $user->wishlist()->where('card_id', $cardId)->exists();

        if ($exists) {
            $user->wishlist()->detach($cardId);
            return response()->json(['status' => 'removed']);
        } else {
            $user->wishlist()->attach($cardId);
            return response()->json(['status' => 'added']);
        }
    }
}