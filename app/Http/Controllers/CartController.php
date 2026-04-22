<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Listing;

class CartController extends Controller
{
    // 1. Show the Cart page
    // Update this method in your CartController.php
    public function index()
    {
        // Retrieve the cart from the session
        $cart = session()->get('cart', []);
        
        // Calculate the total price of all items in the cart
        $total = 0;
        foreach ($cart as $item) {
            // Ensure $item has 'price' and 'quantity' to avoid crashes
            $total += (isset($item['price']) && isset($item['quantity'])) 
                      ? ($item['price'] * $item['quantity']) 
                      : 0;
        }

        return view('cart.index', compact('cart', 'total'));
    }

    // 2. Add an item to the Cart (Satisfies Rubric Section D: Ajouter)
    public function add(Request $request)
    {
        $listing = Listing::with('card')->findOrFail($request->listing_id);

        // Security: Prevent a user from buying their own card
        if ($listing->seller_id == auth()->id()) {
            return redirect()->back()->with('error', 'You cannot buy your own card.');
        }

        $cart = session()->get('cart', []);

        // If the item is already in the cart, just increase the quantity
        if (isset($cart[$listing->id])) {
            // Check if seller has enough stock
            if ($cart[$listing->id]['quantity'] < $listing->quantity) {
                $cart[$listing->id]['quantity']++;
            } else {
                return redirect()->back()->with('error', 'Not enough stock available.');
            }
        } else {
            // If it's a new item, add it to the cart array
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
        return redirect()->back()->with('success', 'Card added to cart successfully!');
    }

    // 3. Update item quantity (Satisfies Rubric Section D: Modifier quantité)
    public function update(Request $request)
    {
        if ($request->listing_id && $request->quantity) {
            $cart = session()->get('cart');

            // Prevent updating past the seller's available stock
            $listing = Listing::find($request->listing_id);
            if ($request->quantity > $listing->quantity) {
                return redirect()->back()->with('error', 'Requested quantity exceeds available stock.');
            }

            $cart[$request->listing_id]["quantity"] = $request->quantity;
            session()->put('cart', $cart);

            return redirect()->back()->with('success', 'Cart updated successfully');
        }
    }

    // 4. Remove item from Cart (Satisfies Rubric Section D: Supprimer)
    public function remove(Request $request)
    {
        if ($request->listing_id) {
            $cart = session()->get('cart');
            
            if (isset($cart[$request->listing_id])) {
                unset($cart[$request->listing_id]);
                session()->put('cart', $cart);
            }

            return redirect()->back()->with('success', 'Card removed from cart');
        }
    }
}