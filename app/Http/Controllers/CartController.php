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

        if ($listing->seller_id == auth()->id()) {
            return response()->json(['success' => false, 'message' => 'You cannot buy your own card.']);
        }

        $cart = session()->get('cart', []);
        if (isset($cart[$listing->id])) {
            if ($cart[$listing->id]['quantity'] < $listing->quantity) {
                $cart[$listing->id]['quantity']++;
            } else {
                return response()->json(['success' => false, 'message' => 'Not enough stock.']);
            }
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
        // [JSON RESPONSE for Sarah's showNotification()]
        return response()->json(['success' => true, 'message' => 'Card added to the Vault!']);
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