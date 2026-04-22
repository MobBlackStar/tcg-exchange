<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\YdkParserService;

class NexusController extends Controller
{
    protected $ydkService;

    // Dependency Injection: Laravel automatically loads the Service for us
    public function __construct(YdkParserService $ydkService)
    {
        $this->ydkService = $ydkService;
    }

    public function uploadYdk(Request $request)
    {
        $request->validate([
            'ydk_file' => 'required|file|max:2048' // Max 2MB
        ]);

        $file = $request->file('ydk_file');

        // Security: Ensure it's a text-based file
        if ($file->getClientOriginalExtension() !== 'ydk' && $file->getClientOriginalExtension() !== 'txt') {
            return redirect()->back()->with('error', 'Only .ydk or .txt files are allowed.');
        }

        // Read the file physically
        $contents = file_get_contents($file->getRealPath());

        // Call your God-Tier Algorithm
        $result = $this->ydkService->buildCartFromYdk($contents);

        if ($result['found'] == 0) {
            return redirect()->route('cart.index')->with('error', 'No cards from your deck were found in stock.');
        }

        $message = "Nexus Engine matched {$result['found']} cards from active sellers!";
        if ($result['missing'] > 0) {
            $message .= " However, {$result['missing']} cards were out of stock on the marketplace.";
        }

        return redirect()->route('cart.index')->with('success', $message);
    }
    public function deckBuilder($deckId)
{
    $deck = Deck::with('cards')->findOrFail($deckId);
    
    // Get ALL cards the user owns (from completed orders)
    $ownedCards = auth()->user()->collections()->with('card')->get();

    // Grouping by name so you can count "How many Blue-Eyes do I have?"
    $ownedCounts = $ownedCards->groupBy('card.name')->map->count();
    
    // Grouping by deck content
    $deckCounts = $deck->cards->groupBy('name')->map->count();

    return view('decks.builder', compact('deck', 'ownedCounts', 'deckCounts'));
}
    }