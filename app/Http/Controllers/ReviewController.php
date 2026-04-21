<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Review;
use App\Models\Order;
use App\Models\User;

class ReviewController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'seller_id' => 'required|exists:users,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:500'
        ]);

        $order = Order::findOrFail($request->order_id);

        // Security: Did the user actually buy this? Is the order finished?
        if ($order->buyer_id !== auth()->id() || $order->status !== 'Validée') {
            return redirect()->back()->with('error', 'You can only review completed orders.');
        }

        // Security: Have they already reviewed this order?
        $existingReview = Review::where('order_id', $order->id)
            ->where('reviewer_id', auth()->id())
            ->first();

        if ($existingReview) {
            return redirect()->back()->with('error', 'You have already reviewed this transaction.');
        }

        // Save Review
        Review::create([
            'reviewer_id' => auth()->id(),
            'seller_id' => $request->seller_id,
            'order_id' => $order->id,
            'rating' => $request->rating,
            'comment' => $request->comment
        ]);

        // [GOD-TIER]: Dynamically update the seller's overall reputation score
        $seller = User::find($request->seller_id);
        $averageRating = Review::where('seller_id', $seller->id)->avg('rating');
        $seller->update(['reputation_score' => $averageRating]);

        return redirect()->back()->with('success', 'Review submitted! The seller\'s reputation has been updated.');
    }
}