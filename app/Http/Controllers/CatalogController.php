<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Card;
use App\Models\Category;

class CatalogController extends Controller
{
    public function index(Request $request)
    {
        // 1. Start the query. 
        $query = Card::with('category')->withMin(['listings' => function($q) {
            $q->where('is_active', true)->where('quantity', '>', 0);
        }], 'price');

        // 2. SEARCH
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        // 3. FILTER
        if ($request->filled('category')) {
            $query->whereHas('category', function($q) use ($request) {
                $q->where('name', $request->category);
            });
        }

        // 4. SORT (Including the dynamic Price Sort!)
        $sort = $request->input('sort', 'name_asc');
        if ($sort === 'name_asc') $query->orderBy('name', 'asc');
        if ($sort === 'name_desc') $query->orderBy('name', 'desc');
        if ($sort === 'price_asc') $query->orderBy('listings_min_price', 'asc');
        if ($sort === 'price_desc') $query->orderBy('listings_min_price', 'desc');

        // 5. PAGINATION
        $cards = $query->paginate(12)->withQueryString();
        $categories = Category::all();

        return view('catalog', compact('cards', 'categories'));
    }

    // [TECH LEAD FIX]: The Foolproof Market Data Bridge
    public function show($id)
    {
        // We manually look up the ID to prevent Ghost Models
        $card = Card::with('category')->findOrFail($id);
        
        // Fetch active sellers with stock
        $listings = $card->listings()->where('is_active', true)->where('quantity', '>', 0)->with('seller')->get();
        
        // Send the real data to your view
        return view('cards.show', compact('card', 'listings'));
    }
}