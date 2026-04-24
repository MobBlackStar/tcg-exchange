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
        $cartUpdated = false;

        // 1. Pre-flight Validation: Ensure cart data matches database reality
        foreach ($cart as $id => &$item) {
            $listing = Listing::find($item['listing_id'] ?? $id);
            
            // If the listing disappeared or became inactive
            if (!$listing || !$listing->is_active) {
                unset($cart[$id]);
                $cartUpdated = true;
                continue;
            }

            // If the user's cart quantity exceeds the current stock
            if ($item['quantity'] > $listing->quantity) {
                $item['quantity'] = $listing->quantity;
                $cartUpdated = true;
            }

            // If stock dropped to zero (after adjustments)
            if ($item['quantity'] <= 0) {
                unset($cart[$id]);
                $cartUpdated = true;
                continue;
            }

            // Inject max_stock for the UI to use
            $item['max_stock'] = $listing->quantity;
            $total += (isset($item['price']) && isset($item['quantity'])) ? ($item['price'] * $item['quantity']) : 0;
        }

        if ($cartUpdated) {
            session()->put('cart', $cart);
            session()->flash('error', 'Your cart was adjusted to reflect current market availability.');
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

        // 2. Logic Gate: Ensure stock exists
        if ($listing->quantity <= 0 || !$listing->is_active) {
            return redirect()->back()->with('error', 'This artifact is no longer available.');
        }

        $cart = session()->get('cart', []);
        
        // 3. Logic Gate: Stock Check inside cart
        if (isset($cart[$listing->id])) {
            if ($cart[$listing->id]['quantity'] >= $listing->quantity) {
                return redirect()->back()->with('error', 'Not enough stock available to add more.');
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
        return redirect()->back()->with('success', 'Card secured in the Vault!');
    }

    public function update(Request $request)
    {
        $request->validate([
            'listing_id' => 'required|exists:listings,id',
            'quantity' => 'required|integer|min:1'
        ]);

        $cart = session()->get('cart', []);
        $listing = Listing::find($request->listing_id);
        
        if (!isset($cart[$request->listing_id])) {
            return redirect()->back()->with('error', 'Item not found in your cart.');
        }

        if (!$listing || !$listing->is_active) {
            unset($cart[$request->listing_id]);
            session()->put('cart', $cart);
            return redirect()->back()->with('error', 'This item is no longer available and was removed.');
        }

        // Clean limiter: Cap the quantity to the available stock
        $requestedQty = (int) $request->quantity;
        if ($requestedQty > $listing->quantity) {
            $cart[$request->listing_id]["quantity"] = $listing->quantity;
            session()->put('cart', $cart);
            return redirect()->back()->with('error', 'Quantity capped. Only ' . $listing->quantity . ' available in stock.');
        }

        $cart[$request->listing_id]["quantity"] = $requestedQty;
        session()->put('cart', $cart);
        return redirect()->back()->with('success', 'Cart sequence updated.');
    }

    public function remove(Request $request)
    {
        $cart = session()->get('cart', []);
        if (isset($cart[$request->listing_id])) {
            unset($cart[$request->listing_id]);
            session()->put('cart', $cart);
        }
        return redirect()->back()->with('success', 'Artifact dropped from Vault.');
    }
}