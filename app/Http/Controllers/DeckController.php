<?php

namespace App\Http\Controllers;

use App\Models\Deck;
use Illuminate\Http\Request;

class DeckController extends Controller
{
    // 1. Show the Deck Builder (The Yu-Gi-Oh Simulator UI)
    public function builder($deckId)
    {
        $deck = Deck::with('cards')->findOrFail($deckId);
        
        // Get all cards the user has purchased (from their OrderHistory)
        $ownedCards = auth()->user()->collections()->get();

        // Count how many of each card name they own vs how many are in the deck
        $ownedCounts = $ownedCards->groupBy('name')->map->count();
        $deckCounts = $deck->cards->groupBy('name')->map->count();

        return view('decks.builder', compact('deck', 'ownedCounts', 'deckCounts'));
    }

    // 2. Add card to deck
    public function addCard(Request $request, $deckId)
    {
        $deck = Deck::findOrFail($deckId);
        // Sync the card to the deck pivot table
        $deck->cards()->syncWithoutDetaching([$request->card_id => ['quantity' => 1]]);
        
        return back()->with('success', 'Card added to deck!');
    }
}