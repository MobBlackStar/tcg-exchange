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
        //[GOD-TIER]: withMin() finds the lowest active price among all sellers for the Market Value badge!
        $query = Card::with('category')->withMin(['listings' => function($q) {
            $q->where('is_active', true)->where('quantity', '>', 0);
        }], 'price');

        // 2. SEARCH: Fixed Eloquent OR Trap
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

        // 4. SORT
        $sort = $request->input('sort', 'name_asc');
        if ($sort === 'name_asc') $query->orderBy('name', 'asc');
        if ($sort === 'name_desc') $query->orderBy('name', 'desc');

        // 5. PAGINATION
        $cards = $query->paginate(12)->withQueryString();
        $categories = Category::all();

        return view('catalog', compact('cards', 'categories'));
    }

    public function show($id)
    {
        // [PERFECT SYNTHESIS]
        // Sarah's view logic meets Fedi's listing fetch logic.
        $card = Card::with('category')->findOrFail($id);
        
        // Fetch the sellers (Moataz's domain) to display on Sarah's UI
        $listings = $card->listings()->where('is_active', true)->with('seller')->get();
        
        // We pass BOTH the card and the listings to Sarah's 'show' view
        return view('show', compact('card', 'listings'));
    }
}