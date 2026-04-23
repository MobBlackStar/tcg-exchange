<?php

namespace App\Http\Controllers;

use App\Models\Deck;
use Illuminate\Http\Request;

class DeckController extends Controller
{
    // 🧑‍🏫 Displays the list of decks the user has created
    public function index()
    {
        // [GOD-TIER]: Eager load previewCard so we don't have "N+1" query performance issues
        $decks = auth()->user()->decks()->with('previewCard')->get(); 
        return view('decks.index', compact('decks'));
    }

    // 🧑‍🏫 Creates a new, empty deck
    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255']);
        auth()->user()->decks()->create(['name' => $request->name]);
        return back()->with('success', 'Deck created!');
    }

    // 🧑‍🏫 Loads the visual Deck Builder UI
    public function builder($deckId)
    {
        // [GOD-TIER]: Explicitly load the pivot columns 'quantity' AND 'location'
        $deck = \App\Models\Deck::with(['cards' => function($query) {
            $query->withPivot('quantity', 'location');
        }, 'previewCard'])->findOrFail($deckId);

        return view('decks.builder', compact('deck'));
    }

    // 🧑‍🏫 Adds a card to either the Main or Extra deck
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
            // Yu-Gi-Oh Rule Enforcement: Max 3 copies per deck!
            if ($existingCard->pivot->quantity >= 3) {
                return back()->with('error', 'You cannot have more than 3 copies of a card.');
            }
            $deck->cards()->updateExistingPivot($request->card_id,[
                'quantity' => $existingCard->pivot->quantity + 1
            ]);
        } else {
            $deck->cards()->attach($request->card_id,[
                'quantity' => 1, 
                'location' => $request->location
            ]);
        }

        return back()->with('success', 'Card added!');
    }

    // 🧑‍🏫 [TECH LEAD FIX]: Reduce quantity by 1. Detach entirely only if it reaches 0.
    public function removeCard($deckId, $cardId)
    {
        $deck = \App\Models\Deck::findOrFail($deckId);
        
        $existingCard = $deck->cards()->where('card_id', $cardId)->first();
        
        if ($existingCard) {
            if ($existingCard->pivot->quantity > 1) {
                $deck->cards()->updateExistingPivot($cardId,[
                    'quantity' => $existingCard->pivot->quantity - 1
                ]);
            } else {
                $deck->cards()->detach($cardId);
            }
        }
        return back()->with('success', 'Card removed from deck!');
    }

    // 🧑‍🏫 Sets the "Box Art" image for the deck
    public function setPreview($deckId, Request $request)
    {
        $deck = \App\Models\Deck::findOrFail($deckId);
        $deck->update(['preview_card_id' => $request->card_id]);
        return back()->with('success', 'Preview updated!');
    }
}