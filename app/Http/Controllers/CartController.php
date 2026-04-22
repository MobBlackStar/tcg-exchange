<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Listing;

class CartController extends Controller
{
    public function index()
    {
        $cart = session()->get('cart', []);
        $total = 0;
        foreach ($cart as $item) {
            $total += (isset($item['price']) && isset($item['quantity'])) ? ($item['price'] * $item['quantity']) : 0;
        }
        return view('cart.index', compact('cart', 'total'));
    }

    public function add(Request $request)
{
    $listing = Listing::with('card')->findOrFail($request->listing_id);

    // 1. Logic Gate: Prevent self-purchase
    if ($listing->seller_id == auth()->id()) {
        return redirect()->back()->with('error', 'You cannot buy your own card!');
    }

    $cart = session()->get('cart', []);

    // 2. Logic Gate: Stock Check
    if (isset($cart[$listing->id])) {
        if ($cart[$listing->id]['quantity'] >= $listing->quantity) {
            return redirect()->back()->with('error', 'Not enough stock available.');
        }
        $cart[$listing->id]['quantity']++;
    } else {
        $cart[$listing->id] = [
            "listing_id" => $listing->id,
            "name" => $listing->card->name,
            "condition" => $listing->condition,
            "quantity" => 1,
            "price" => $listing->price,
            "image" => $listing->card->image_url,
            "seller_id" => $listing->seller_id
        ];
    }

    session()->put('cart', $cart);
    return redirect()->back()->with('success', 'Card added to the Vault!');
}

    public function remove(Request $request)
    {
        $cart = session()->get('cart');
        if (isset($cart[$request->listing_id])) {
            unset($cart[$request->listing_id]);
            session()->put('cart', $cart);
        }
        return redirect()->back()->with('success', 'Removed.');
    }
}