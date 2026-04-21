<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Card;
use App\Models\Listing;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Create our Fake Seller (Seto Kaiba)
        $seller = User::firstOrCreate(
            ['email' => 'kaiba@corp.com'],[
                'name' => 'Seto Kaiba',
                'password' => bcrypt('password123'),
                'role' => 'duelist',
                'reputation_score' => 5.00
            ]
        );

        // 2. Find the Blue-Eyes White Dragon
        $blueEyes = Card::where('passcode', 89631139)->first();

        // 3. Create his Listing (only if the card actually exists in the DB)
        if ($blueEyes) {
            Listing::firstOrCreate([
                    'seller_id' => $seller->id,
                    'card_id' => $blueEyes->id,
                ],[
                    'condition' => 'Mint',
                    'price' => 150.00,
                    'quantity' => 5,
                    'is_active' => true
                ]
            );
        }
    }
}