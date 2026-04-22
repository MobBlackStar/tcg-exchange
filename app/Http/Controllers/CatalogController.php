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
        // [GOD-TIER]: withMin() finds the lowest active price among all sellers for the Market Value badge!
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
<<<<<<< HEAD
    public function show($id)
{
    // Find the card or throw a 404 error if it's missing
    $card = Card::with('category')->findOrFail($id);
    
    return view('show', compact('card'));
}
=======

    public function show(Card $card)
    {
        $listings = $card->listings()->where('is_active', true)->with('seller')->get();
        return view('cards.show', compact('card', 'listings'));
    }
>>>>>>> 3584ad022a9f50f9cb242447068e60e9f6c4e623
}