<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Card;
use App\Models\Category;

class CatalogController extends Controller
{
    public function index(Request $request)
    {
        // 1. Start the query (Load categories to make it faster)
        $query = Card::with('category');

        // 2. SEARCH: Recherche par mot-clé
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        // 3. FILTER: Filtrage par catégorie (Constraint 3C)
        if ($request->filled('category')) {
            $query->whereHas('category', function($q) use ($request) {
                $q->where('name', $request->category);
            });
        }

        // 4. SORT: Tri (Constraint 3C)
        $sort = $request->input('sort', 'name_asc');
        if ($sort === 'name_asc') $query->orderBy('name', 'asc');
        if ($sort === 'name_desc') $query->orderBy('name', 'desc');

        // 5. PAGINATION: 12 cards per page (Dynamic navigation)
        $cards = $query->paginate(12)->withQueryString();
        $categories = Category::all();

        return view('catalog', compact('cards', 'categories'));
    }
}