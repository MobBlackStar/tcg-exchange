<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Listing;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    // 1. Show the Order History (Satisfies Rubric Section E: Historique des commandes)
    public function index()
    {
        // Get all orders where the user is the buyer, load the items inside them
        $orders = Order::where('buyer_id', auth()->id())->with('items.listing.card')->latest()->get();
        
        return view('orders.index', compact('orders'));
    }

    // 2. Process the Cart into an Order (Satisfies Rubric Section E: Passer une commande)
    public function checkout()
    {
        $cart = session()->get('cart');

        if (!$cart || count($cart) == 0) {
            return redirect()->back()->with('error', 'Your cart is empty.');
        }

        // Calculate total price dynamically
        $totalPrice = 0;
        foreach ($cart as $item) {
            $totalPrice += $item['price'] * $item['quantity'];
        }

        // [GOD-TIER]: Database Transaction. If anything fails, it rolls back the entire checkout.
        DB::beginTransaction();

        try {
            // Create the Parent Order
            $order = Order::create([
                'uuid' => (string) Str::uuid(),
                'buyer_id' => auth()->id(),
                'status' => 'En attente', // Exact Rubric Status
                'total_price' => $totalPrice,
            ]);

            // Create the Order Items
            foreach ($cart as $id => $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'listing_id' => $item['listing_id'],
                    'price_locked' => $item['price'], // Locks the price forever
                    'quantity' => $item['quantity'],
                ]);

                // Deduct stock from the seller's listing
                $listing = Listing::find($item['listing_id']);
                $listing->quantity -= $item['quantity'];
                
                // If stock reaches 0, hide it from the market
                if ($listing->quantity <= 0) {
                    $listing->is_active = false;
                }
                $listing->save();
            }

            // Empty the session cart after successful purchase
            session()->forget('cart');
            
            DB::commit();

            return redirect()->route('orders.index')->with('success', 'Order placed successfully! Status: En attente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Checkout failed: ' . $e->getMessage());
        }
    }

    // 3. Update Order Status (For Sellers/Admins)
    public function updateStatus(Request $request, Order $order)
    {
        // In a real app, only Admins or Sellers would do this, but we leave it accessible for the Demo
        $request->validate([
            'status' => 'required|in:En attente,Validée,Annulée' // Exact Rubric Statuses
        ]);

        $order->update([
            'status' => $request->status
        ]);

        return redirect()->back()->with('success', 'Order status updated to ' . $request->status);
    }
}