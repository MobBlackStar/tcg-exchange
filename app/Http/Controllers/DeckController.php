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
    // [GOD-TIER]: Export a built deck to a .ydk file instantly
    public function exportYdk($deckId)
    {
        $deck = \App\Models\Deck::with('cards')->findOrFail($deckId);
        
        $content = "#created by TCG_EXCHANGE\n#main\n";
        foreach ($deck->cards->where('pivot.location', 'main') as $card) {
            for ($i = 0; $i < $card->pivot->quantity; $i++) {
                $content .= $card->passcode . "\n";
            }
        }
        
        $content .= "#extra\n";
        foreach ($deck->cards->where('pivot.location', 'extra') as $card) {
            for ($i = 0; $i < $card->pivot->quantity; $i++) {
                $content .= $card->passcode . "\n";
            }
        }
        $content .= "!side\n";

        // Clean the filename
        $filename = preg_replace('/[^A-Za-z0-9_]/', '_', $deck->name) . '.ydk';

        return response($content)
            ->header('Content-Type', 'text/plain')
            ->header('Content-Disposition', "attachment; filename=\"$filename\"");
    }

    // [GOD-TIER]: Import a .ydk file to instantly populate a deck
    public function importYdk(Request $request, $deckId)
    {
        $request->validate(['ydk_file' => 'required|file|max:2048']);
        $deck = \App\Models\Deck::findOrFail($deckId);
        
        $contents = file_get_contents($request->file('ydk_file')->getRealPath());
        $lines = explode("\n", str_replace("\r", "", $contents));
        
        $location = 'main';
        $cardsAdded = 0;

        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '#main') { $location = 'main'; continue; }
            if ($line === '#extra') { $location = 'extra'; continue; }
            if ($line === '!side') { $location = 'side'; continue; }
            
            if (is_numeric($line) && in_array($location, ['main', 'extra'])) {
                $card = \App\Models\Card::where('passcode', $line)->first();
                if ($card) {
                    $existing = $deck->cards()->where('card_id', $card->id)->where('location', $location)->first();
                    if ($existing) {
                        if ($existing->pivot->quantity < 3) {
                            $deck->cards()->updateExistingPivot($card->id,['quantity' => $existing->pivot->quantity + 1]);
                        }
                    } else {
                        $deck->cards()->attach($card->id, ['quantity' => 1, 'location' => $location]);
                    }
                    $cardsAdded++;
                }
            }
        }

        return back()->with('success', "YDK Imported: $cardsAdded cards decoded and added to your deck!");
    }
}