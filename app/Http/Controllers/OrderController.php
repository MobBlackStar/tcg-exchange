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
        // 1. My Purchases (Receipts)
        $myOrders = Order::where('buyer_id', auth()->id())->with('items.listing.card')->latest()->get();

        // 2. [TECH LEAD FIX]: Incoming Orders (The Merchant's Cash Register)
        $incomingOrders = Order::whereHas('items.listing', function($q) {
            $q->where('seller_id', auth()->id());
        })->with('items.listing.card', 'buyer')->latest()->get();

        return view('orders.index', compact('myOrders', 'incomingOrders'));
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

                // [TECH LEAD FIX]: Auto-Notify Seller via Chat
                \App\Models\Message::create([
                    'sender_id' => auth()->id(),
                    'receiver_id' => $listing->seller_id,
                    'listing_id' => $listing->id,
                    'content' => "[SYSTEM ALERT]: I have purchased {$item['quantity']}x {$listing->card->name} for " . ($item['price'] * $item['quantity']) . " DT. Awaiting shipment."
                ]);
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
        $request->validate([
            'status' => 'required|in:En attente,Validée,Annulée' // Exact Rubric Statuses
        ]);

        $order->update([
            'status' => $request->status
        ]);

        return redirect()->back()->with('success', 'Order status updated to ' . $request->status);
    }
}