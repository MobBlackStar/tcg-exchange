<?php

namespace App\Http\Controllers;

use App\Models\Deck;
use Illuminate\Http\Request;

class DeckController extends Controller
{
    // 1. Show the Deck Builder (The Yu-Gi-Oh Simulator UI)
   public function builder($deckId)
{
    // [GOD-TIER]: Explicitly load the pivot columns 'quantity' AND 'location'
    $deck = \App\Models\Deck::with(['cards' => function($query) {
        $query->withPivot('quantity', 'location');
    }, 'previewCard'])->findOrFail($deckId);
    
    $ownedCards = auth()->user()->collections()->with('card')->get();
    $ownedCounts = $ownedCards->groupBy('card.name')->map->count();

    return view('decks.builder', compact('deck', 'ownedCounts'));
}
    // 2. Add card to deck
  public function addCard(Request $request, $deckId)
{
    $request->validate([
        'card_id' => 'required|exists:cards,id',
        'location' => 'required|in:main,extra'
    ]);

    $deck = \App\Models\Deck::findOrFail($deckId);

    // Look for existing card IN THIS SPECIFIC LOCATION
    $existingCard = $deck->cards()
        ->where('card_id', $request->card_id)
        ->where('location', $request->location)
        ->first();

    if ($existingCard) {
        $deck->cards()->updateExistingPivot($request->card_id, [
            'quantity' => $existingCard->pivot->quantity + 1
        ]);
    } else {
        $deck->cards()->attach($request->card_id, [
            'quantity' => 1, 
            'location' => $request->location
        ]);
    }

    return back()->with('success', 'Card added!');
}
  public function index()
{
    // [GOD-TIER]: Eager load previewCard so we don't have "N+1" query issues
    $decks = auth()->user()->decks()->with('previewCard')->get(); 
    return view('decks.index', compact('decks'));
}

public function store(Request $request)
{
    $request->validate(['name' => 'required|string|max:255']);
    auth()->user()->decks()->create(['name' => $request->name]);
    return back()->with('success', 'Deck created!');
}
public function removeCard($deckId, $cardId)
{
    $deck = \App\Models\Deck::findOrFail($deckId);
    $deck->cards()->detach($cardId); // Removes the card from the pivot table
    return back()->with('success', 'Card removed from deck!');
}

public function setPreview($deckId, Request $request)
{
    $deck = \App\Models\Deck::findOrFail($deckId);
    // Assuming you add 'preview_card_id' to your decks table
    $deck->update(['preview_card_id' => $request->card_id]);
    return back()->with('success', 'Preview updated!');
}
}