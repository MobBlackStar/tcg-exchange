<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Listing;
use App\Models\Card;
use Illuminate\Support\Facades\Auth;

class ListingController extends Controller
{
    // 1. Show the user all the cards they are currently selling
    public function index()
    {
        // Fetch listings belonging to the logged-in user, and load the Card data
        $myListings = Listing::where('seller_id', Auth::id())->with('card')->get();
        
        // We will pass this to Sarah's blade view later
        return view('inventory.index', compact('myListings'));
    }

    // 2. Add a new card to sell (Satisfies Rubric Section B: Ajouter)
    public function store(Request $request)
    {
        // Basic validation (Ritej will handle advanced validation later)
        $request->validate([
            'card_id' => 'required|exists:cards,id',
            'condition' => 'required|string',
            'price' => 'required|numeric|min:0.1',
            'quantity' => 'required|integer|min:1'
        ]);

        // Insert into the database
        Listing::create([
            'seller_id' => Auth::id(),
            'card_id' => $request->card_id,
            'condition' => $request->condition,
            'price' => $request->price,
            'quantity' => $request->quantity,
            'is_active' => true,
        ]);

        return redirect()->back()->with('success', 'Your card is now listed for sale!');
    }

    // 3. Remove a card from the market (Satisfies Rubric Section B: Supprimer)
    public function destroy(Listing $listing)
    {
        // Security: Ensure the user actually owns this listing before deleting
        if ($listing->seller_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $listing->delete();

        return redirect()->back()->with('success', 'Listing removed successfully.');
    }
    // [TECH LEAD FIX]: Rubric Section B (Modifier)
    public function update(Request $request, Listing $listing)
    {
        if ($listing->seller_id !== Auth::id()) abort(403, 'Unauthorized.');
        
        $request->validate([
            'price' => 'required|numeric|min:0.01',
            'condition' => 'required|string',
            'quantity' => 'required|integer|min:1'
        ]);

        $listing->update([
            'price' => $request->price,
            'condition' => $request->condition,
            'quantity' => $request->quantity,
        ]);

        return back()->with('success', 'Artifact data updated.');
    }
}