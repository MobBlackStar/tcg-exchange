<?php

namespace App\Services;

use App\Models\Card;
use App\Models\Deck;
use Illuminate\Support\Facades\Session;

class YdkParserService
{
    /**
     * Parses ANY format (Base64, YDK, Text) and directly injects it into a User's Deck.
     */
    public function importToDeck(Deck $deck, $rawContent)
    {
        $rawContent = trim($rawContent);
        $cardsToAdd =[]; // ['passcode' => ['quantity' => X, 'location' => 'main/extra/side']]

        // FORMAT 1: BASE64 OMEGA CODE (Usually a single long string with no spaces)
        if (!str_contains($rawContent, "\n") && !str_contains($rawContent, " ") && strlen($rawContent) > 50) {
            $decoded = base64_decode($rawContent);
            if ($decoded && str_contains($decoded, '#main')) {
                $rawContent = $decoded; // Overwrite with decoded YDK and let Format 2 handle it
            }
        }

        // FORMAT 2: STANDARD YDK (#main, #extra, !side followed by passcodes)
        if (str_contains($rawContent, '#main')) {
            $lines = explode("\n", str_replace("\r", "", $rawContent));
            $currentLocation = 'main';

            foreach ($lines as $line) {
                $line = trim($line);
                if (empty($line) || str_starts_with($line, '#Created')) continue;
                
                if ($line === '#main') { $currentLocation = 'main'; continue; }
                if ($line === '#extra') { $currentLocation = 'extra'; continue; }
                if ($line === '!side') { $currentLocation = 'side'; continue; }

                if (is_numeric($line)) {
                    if (!isset($cardsToAdd[$line])) {
                        $cardsToAdd[$line] =['quantity' => 0, 'location' => $currentLocation];
                    }
                    $cardsToAdd[$line]['quantity']++;
                }
            }
        } 
        // FORMAT 3: TEXT RECIPE (e.g., "2 Spirit of Yubel" or "Spell")
        else if (str_contains($rawContent, 'Monster') || str_contains(strtolower($rawContent), 'extra')) {
            $lines = explode("\n", str_replace("\r", "", $rawContent));
            $currentLocation = 'main';

            foreach ($lines as $line) {
                $line = trim($line);
                if (empty($line)) continue;

                $lowerLine = strtolower($line);
                if ($lowerLine === 'monster' || $lowerLine === 'spell' || $lowerLine === 'trap') { $currentLocation = 'main'; continue; }
                if ($lowerLine === 'extra') { $currentLocation = 'extra'; continue; }
                if ($lowerLine === 'side') { $currentLocation = 'side'; continue; }

                // Regex to find "Quantity Name" (e.g., "3 Ash Blossom & Joyous Spring")
                if (preg_match('/^(\d+)\x20+(.+)$/', $line, $matches)) {
                    $qty = (int)$matches[1];
                    $name = $matches[2];

                    // Find the card by exact name
                    $card = Card::where('name', $name)->first();
                    if ($card) {
                        $cardsToAdd[$card->passcode] =['quantity' => $qty, 'location' => $currentLocation];
                    }
                }
            }
        }

        // NOW: INJECT INTO THE DATABASE
        $cardsAdded = 0;
        foreach ($cardsToAdd as $passcode => $data) {
            $card = Card::where('passcode', $passcode)->first();
            if ($card) {
                $existing = $deck->cards()->where('card_id', $card->id)->where('location', $data['location'])->first();
                
                // Enforce max 3 copies
                $finalQty = $existing ? min(3, $existing->pivot->quantity + $data['quantity']) : min(3, $data['quantity']);

                if ($existing) {
                    $deck->cards()->updateExistingPivot($card->id, ['quantity' => $finalQty]);
                } else {
                    $deck->cards()->attach($card->id, ['quantity' => $finalQty, 'location' => $data['location']]);
                }
                $cardsAdded++;
            }
        }

        return $cardsAdded;
    }
}