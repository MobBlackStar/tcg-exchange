<?php

namespace App\Http\Controllers;

use App\Models\Deck;
use Illuminate\Http\Request;

class DeckController extends Controller
{
    public function index()
    {
        $decks = auth()->user()->decks()->with('previewCard')->get(); 
        return view('decks.index', compact('decks'));
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255']);
        auth()->user()->decks()->create(['name' => $request->name]);
        return back()->with('success', 'Deck created!');
    }

    public function builder($deckId)
    {
        $deck = \App\Models\Deck::with(['cards' => function($query) {
            $query->withPivot('quantity', 'location');
        }, 'previewCard'])->findOrFail($deckId);

        return view('decks.builder', compact('deck'));
    }

    public function addCard(Request $request, $deckId)
    {
        $request->validate([
            'card_id' => 'required|exists:cards,id',
            'location' => 'required|in:main,extra,side'
        ]);

        $deck = \App\Models\Deck::findOrFail($deckId);
        $cardToSummon = \App\Models\Card::findOrFail($request->card_id);
        $type = strtolower($cardToSummon->type);

        // [TECH LEAD FIX]: 1. STRICT CARD TYPE LOGIC GATES
        $isExtraDeckMonster = str_contains($type, 'fusion') || str_contains($type, 'synchro') || str_contains($type, 'xyz') || str_contains($type, 'link');
        
        if ($request->location === 'extra' && !$isExtraDeckMonster) {
            return back()->with('error', 'Only Fusion, Synchro, Xyz, and Link monsters can go in the Extra Deck!');
        }
        if ($request->location === 'main' && $isExtraDeckMonster) {
            return back()->with('error', 'Extra Deck monsters cannot be placed in the Main Deck!');
        }

        // [TECH LEAD FIX]: 2. STRICT CAPACITY LOGIC GATES
        $currentTotalInLocation = $deck->cards()->where('location', $request->location)->sum('quantity');
        
        if ($request->location === 'main' && $currentTotalInLocation >= 60) {
            return back()->with('error', 'Main Deck capacity reached (Max 60).');
        }
        if ($request->location === 'extra' && $currentTotalInLocation >= 15) {
            return back()->with('error', 'Extra Deck capacity reached (Max 15).');
        }
        if ($request->location === 'side' && $currentTotalInLocation >= 15) {
            return back()->with('error', 'Side Deck capacity reached (Max 15).');
        }

        // 3. EXISTENCE AND 3-COPY LIMIT GATES
        $existingCard = $deck->cards()->where('card_id', $request->card_id)->where('location', $request->location)->first();

        if ($existingCard) {
            if ($existingCard->pivot->quantity >= 3) {
                return back()->with('error', 'Limit Reached: Max 3 copies per deck.');
            }
            $deck->cards()->updateExistingPivot($request->card_id, ['quantity' => $existingCard->pivot->quantity + 1]);
        } else {
            $deck->cards()->attach($request->card_id,['quantity' => 1, 'location' => $request->location]);
        }

        return back()->with('success', 'Card deployed to ' . strtoupper($request->location) . '!');
    }

    public function removeCard($deckId, $cardId)
    {
        $deck = \App\Models\Deck::findOrFail($deckId);
        $existingCard = $deck->cards()->where('card_id', $cardId)->first();
        
        if ($existingCard) {
            if ($existingCard->pivot->quantity > 1) {
                $deck->cards()->updateExistingPivot($cardId,['quantity' => $existingCard->pivot->quantity - 1]);
            } else {
                $deck->cards()->detach($cardId);
            }
        }
        return back()->with('success', 'Card removed from deck!');
    }

    public function setPreview($deckId, Request $request)
    {
        $deck = \App\Models\Deck::findOrFail($deckId);
        $deck->update(['preview_card_id' => $request->card_id]);
        return back()->with('success', 'Preview updated!');
    }

    // ==========================================
    // [PROJECT OMEGA]: YDK EXPORT & IMPORT
    // ==========================================

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

        $filename = preg_replace('/[^A-Za-z0-9_]/', '_', $deck->name) . '.ydk';

        return response($content)
            ->header('Content-Type', 'text/plain')
            ->header('Content-Disposition', "attachment; filename=\"$filename\"");
    }

    // [PROJECT OMEGA]: Import via File Upload
    public function importYdk(Request $request, $deckId, \App\Services\YdkParserService $parser)
    {
        $request->validate(['ydk_file' => 'required|file|max:2048']);
        $deck = \App\Models\Deck::findOrFail($deckId);
        
        $contents = file_get_contents($request->file('ydk_file')->getRealPath());
        $cardsAdded = $parser->importToDeck($deck, $contents);

        return back()->with('success', "Archive Imported: $cardsAdded unique cards decoded!");
    }

    // [PROJECT OMEGA]: Import via Clipboard Text
    public function importText(Request $request, $deckId, \App\Services\YdkParserService $parser)
    {
        $request->validate(['clipboard_data' => 'required|string']);
        $deck = \App\Models\Deck::findOrFail($deckId);
        
        $cardsAdded = $parser->importToDeck($deck, $request->clipboard_data);

        return back()->with('success', "Clipboard Decoded: $cardsAdded unique cards injected!");
    }
}