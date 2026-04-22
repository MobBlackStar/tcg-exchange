<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Card;

class WishlistController extends Controller
{
    public function toggle(Request $request)
    {
        $request->validate([
            'card_id' => 'required|exists:cards,id'
        ]);

        $user = auth()->user();
        $cardId = $request->card_id;

        // Toggle: If exists, delete. If not, create.
        $exists = $user->wishlist()->where('card_id', $cardId)->exists();
        
        if ($exists) {
            $user->wishlist()->detach($cardId);
            return response()->json(['status' => 'removed', 'message' => 'Removed from Wishlist']);
        } else {
            $user->wishlist()->attach($cardId);
            return response()->json(['status' => 'added', 'message' => 'Added to Wishlist']);
        }
    }
}