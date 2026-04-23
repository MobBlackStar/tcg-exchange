<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Card;

class WishlistController extends Controller
{
    // [TECH LEAD FIX]: This loads Sarah's Favorites Page
    public function index()
    {
        // Get the cards the CURRENT user has wishlisted
        $favorites = auth()->user()->wishlist()->with('category')->get();
        return view('wishlist', compact('favorites'));
    }

    // [TECH LEAD FIX]: This powers the AJAX Heart Button
    public function toggle(Request $request)
    {
        $request->validate(['card_id' => 'required|exists:cards,id']);

        $user = auth()->user();
        $cardId = $request->card_id;

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