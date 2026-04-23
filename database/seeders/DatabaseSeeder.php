<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Card;
use App\Models\Listing;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Forging the Living Market...');

        // 1. Create the Tech Lead
        User::firstOrCreate(['email' => 'admin@tcg.com'],[
            'name' => 'Tech Lead', 'password' => bcrypt('password123'), 'role' => 'admin', 'reputation_score' => 5.00
        ]);

        // 2. Create the Legendary Duelists
        $duelists =['Seto Kaiba', 'Yugi Muto', 'Joey Wheeler', 'Mai Valentine', 'Maximillion Pegasus', 'Marik Ishtar', 'Bakura', 'Chazz Princeton'];
        
        $vendors =[];
        foreach ($duelists as $name) {
            $vendors[] = User::firstOrCreate(['email' => strtolower(explode(' ', $name)[0]) . '@duel.com'],['name' => $name, 'password' => bcrypt('password123'), 'role' => 'duelist', 'reputation_score' => rand(35, 50) / 10]
            );
        }

        // 3. The Absolute Market: Every single card gets a vendor.
        $cards = Card::all();
        $totalCards = $cards->count();
        
        $this->command->info("Distributing ALL {$totalCards} artifacts to vendors...");
        
        $bar = $this->command->getOutput()->createProgressBar($totalCards);
        $bar->start();

        $conditions =['Mint', 'Near Mint', 'Lightly Played', 'Damaged'];

        foreach ($cards as $card) {
            $randomVendor = $vendors[array_rand($vendors)];

            // Perfectly structured arrays, no rogue comments
            Listing::firstOrCreate(['card_id' => $card->id],[
                    'seller_id' => $randomVendor->id,
                    'condition' => $conditions[array_rand($conditions)],
                    'price' => rand(10, 999) + 0.99, 
                    'quantity' => rand(1, 5),
                    'is_active' => true
                ]
            );

            $bar->advance();
        }

        $bar->finish();
        $this->command->newLine();
        $this->command->info('The Absolute Living Market is online. Every card is now available.');
    }
}