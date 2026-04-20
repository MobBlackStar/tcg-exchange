<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\Card;
use App\Models\Category;

class SeedYgoCardsCommand extends Command
{
    protected $signature = 'ygo:seed-cards';

    protected $description = 'Summons all Yu-Gi-Oh! cards from the YGOPRODeck API into the database.';

    public function handle()
    {   //[GOD-TIER FIX] Give PHP infinite memory to handle the massive JSON payload
        ini_set('memory_limit', '-1');
        $this->info('Opening the Void... Connecting to YGOPRODeck API...');

        $monsterCat = Category::firstOrCreate(['name' => 'Monster']);
        $spellCat = Category::firstOrCreate(['name' => 'Spell']);
        $trapCat = Category::firstOrCreate(['name' => 'Trap']);

        $response = Http::timeout(120)->withoutVerifying()->get('https://db.ygoprodeck.com/api/v7/cardinfo.php');
        if ($response->failed()) {
            $this->error('The summoning failed. The API rejected the connection.');
            return;
        }

        $cards = $response->json('data');
        $totalCards = count($cards);
        
        $this->info("Success! Found {$totalCards} cards. Forging them into the database...");

        $bar = $this->output->createProgressBar($totalCards);
        $bar->start();

        foreach ($cards as $cardData) {
            $typeString = $cardData['type'];
            
            if (str_contains($typeString, 'Monster')) {
                $categoryId = $monsterCat->id;
            } elseif (str_contains($typeString, 'Spell')) {
                $categoryId = $spellCat->id;
            } elseif (str_contains($typeString, 'Trap')) {
                $categoryId = $trapCat->id;
            } else {
                $categoryId = $monsterCat->id;
            }

            $imageUrl = $cardData['card_images'][0]['image_url'] ?? null;

            // Perfectly structured Array without rogue comments
            Card::updateOrCreate(['passcode' => $cardData['id']],[
                    'category_id' => $categoryId,
                    'name' => $cardData['name'],
                    'type' => $cardData['type'],
                    'description' => $cardData['desc'],
                    'image_url' => $imageUrl,
                ]
            );

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info('The Summoning is complete. The cards are ready.');
    }
}