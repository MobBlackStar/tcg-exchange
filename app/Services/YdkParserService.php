<?php

namespace App\Services;

use App\Models\Card;
use App\Models\Listing;
use Illuminate\Support\Facades\Session;

class YdkParserService
{
    /**
     * Parses a .ydk file and dynamically builds a multi-vendor cart.
     */
    public function buildCartFromYdk($fileContents)
    {
        // 1. Break the file into lines
        $lines = explode("\n", str_replace("\r", "", $fileContents));
        
        $requiredCards =[];

        // 2. Extract only the numeric passcodes and count how many of each we need
        foreach ($lines as $line) {
            $line = trim($line);
            // If it's a number, it's a card ID
            if (is_numeric($line)) {
                if (!isset($requiredCards[$line])) {
                    $requiredCards[$line] = 0;
                }
                $requiredCards[$line]++; // Example: Needs 3x Blue-Eyes (Passcode: 89631139)
            }
        }

        $cart = session()->get('cart',[]);
        $foundCount = 0;
        $missingCount = 0;

        // 3. The Algorithm: Find the cheapest listings for each required card
        foreach ($requiredCards as $passcode => $quantityNeeded) {
            // Find the actual card in our global DB
            $card = Card::where('passcode', $passcode)->first();

            if (!$card) {
                $missingCount += $quantityNeeded;
                continue; // Card doesn't exist in our DB
            }

            // Get active listings for this exact card, sorted by cheapest price
            $listings = Listing::with('card')
                ->where('card_id', $card->id)
                ->where('is_active', true)
                ->where('quantity', '>', 0)
                ->orderBy('price', 'asc')
                ->get();

            $quantityFulfilled = 0;

            // Go through the cheapest listings and grab them until we have the quantity we need
            foreach ($listings as $listing) {
                if ($quantityFulfilled >= $quantityNeeded) break;

                // How many can we take from this specific seller?
                $neededFromThisSeller = min($quantityNeeded - $quantityFulfilled, $listing->quantity);

                // Inject exactly into Moataz's Cart Format
                if (isset($cart[$listing->id])) {
                    $cart[$listing->id]['quantity'] += $neededFromThisSeller;
                } else {
                    $cart[$listing->id] =[
                        "listing_id" => $listing->id,
                        "name" => $card->name,
                        "condition" => $listing->condition,
                        "quantity" => $neededFromThisSeller,
                        "price" => $listing->price,
                        "image" => $card->image_url,
                        "seller_id" => $listing->seller_id
                    ];
                }

                $quantityFulfilled += $neededFromThisSeller;
                $foundCount += $neededFromThisSeller;
            }

            // If we couldn't find enough stock across all sellers, mark the remainder as missing
            if ($quantityFulfilled < $quantityNeeded) {
                $missingCount += ($quantityNeeded - $quantityFulfilled);
            }
        }

        // 4. Save the dynamically built cart back to the session
        session()->put('cart', $cart);

        return[
            'found' => $foundCount,
            'missing' => $missingCount
        ];
    }
}