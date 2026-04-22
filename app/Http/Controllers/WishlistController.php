<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Wishlist;
use App\Models\Card;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    // View the Favorites Page
    public function index()
    {
        // Hardcoded to User 1 for testing
        $favorites = Wishlist::where('user_id', 1)->with('card.category')->get();
        return view('wishlist', compact('favorites'));
    }

    public function toggle(Request $request)
    {
        // Hardcoded to User 1 for testing
        $exists = Wishlist::where('user_id', 1)
                          ->where('card_id', $request->card_id)
                          ->first();

        if ($exists) {
            $exists->delete();
            return response()->json(['status' => 'removed']);
        }

        Wishlist::create([
            'user_id' => 1,
            'card_id' => $request->card_id
        ]);

        return response()->json(['status' => 'added']);
    }
}