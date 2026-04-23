<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\YdkParserService;

class NexusController extends Controller
{
    protected $ydkService;

    // 🧑‍🏫 Dependency Injection: Laravel automatically loads the Service for us
    public function __construct(YdkParserService $ydkService)
    {
        $this->ydkService = $ydkService;
    }

    // 🧑‍🏫 The Nexus Gateway: Where users drop their .ydk or .txt files
    public function uploadYdk(Request $request)
    {
        // 1. Ensure a file was actually uploaded, max 2MB to prevent server overload
        $request->validate([
            'ydk_file' => 'required|file|max:2048'
        ]);

        $file = $request->file('ydk_file');

        // 2. Security Check: Only allow text-based deck files. No malicious executables.
        if ($file->getClientOriginalExtension() !== 'ydk' && $file->getClientOriginalExtension() !== 'txt') {
            return redirect()->back()->with('error', 'Only .ydk or .txt files are allowed.');
        }

        // 3. Read the physical text inside the file
        $contents = file_get_contents($file->getRealPath());

        // 4. Call the Tech Lead's God-Tier Algorithm to do the heavy lifting
        $result = $this->ydkService->buildCartFromYdk($contents);

        // 5. Logic Gate: Did the engine find any cards on the market?
        if ($result['found'] == 0) {
            return redirect()->route('cart.index')->with('error', 'No cards from your deck were found in stock.');
        }

        // 6. Dynamic Feedback: Tell the user exactly how many cards succeeded vs failed
        $message = "Nexus Engine matched {$result['found']} cards from active sellers!";
        if ($result['missing'] > 0) {
            $message .= " However, {$result['missing']} cards were out of stock on the marketplace.";
        }

        return redirect()->route('cart.index')->with('success', $message);
    }
}