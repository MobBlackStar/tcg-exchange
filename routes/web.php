<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ListingController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\NexusController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\WishlistController;
use App\Http\Controllers\DeckController;
use Illuminate\Http\Request;
use App\Models\Card;

Route::get('/', function () { return view('welcome'); });
Route::get('/catalog', [CatalogController::class, 'index'])->name('catalog');
Route::get('/catalog/{card}', [CatalogController::class, 'show'])->name('cards.show'); 

// [TECH LEAD FIX]: REAL-TIME API SEARCH FOR THE INVENTORY (Now with images!)
Route::get('/api/cards/search', function (Request $request) {
    if (!$request->filled('q')) return response()->json([]);
    // Added 'image_url' to the select statement
    return Card::where('name', 'like', '%' . $request->q . '%')->limit(20)->get(['id', 'name', 'passcode', 'image_url']);
})->name('api.cards.search');

// --- USERS MUST BE LOGGED IN (But maybe not verified yet, so they can access their profile to resend the email) ---
Route::middleware('auth')->group(function () {
    Route::get('/profile',[ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile',[ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile',[ProfileController::class, 'destroy'])->name('profile.destroy');
});


// ---[TECH LEAD FIX] USERS MUST BE LOGGED IN *AND* VERIFIED TO USE THE PLATFORM ---
Route::middleware(['auth', 'verified'])->group(function () {
    
    Route::get('/dashboard', function () { return view('dashboard'); })->name('dashboard');

    Route::get('/favorites',[WishlistController::class, 'index'])->name('wishlist.index');
    Route::post('/wishlist/toggle', [WishlistController::class, 'toggle'])->name('wishlist.toggle');

    Route::get('/my-inventory', [ListingController::class, 'index'])->name('inventory.index');
    Route::post('/inventory/add', [ListingController::class, 'store'])->name('inventory.store');
    Route::patch('/inventory/{listing}', [ListingController::class, 'update'])->name('inventory.update'); 
    Route::delete('/inventory/{listing}', [ListingController::class, 'destroy'])->name('inventory.destroy');

    Route::get('/cart',[CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add',[CartController::class, 'add'])->name('cart.add');
    Route::patch('/cart/update',[CartController::class, 'update'])->name('cart.update');
    Route::delete('/cart/remove',[CartController::class, 'remove'])->name('cart.remove');

    Route::post('/checkout', [OrderController::class, 'checkout'])->name('checkout.process');
    Route::get('/my-orders', [OrderController::class, 'index'])->name('orders.index');
    Route::patch('/order/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.updateStatus');
    Route::post('/review/store', [ReviewController::class, 'store'])->name('reviews.store');

    Route::get('/inbox',[ChatController::class, 'inbox'])->name('chat.inbox'); 
    Route::post('/chat/send', [ChatController::class, 'sendMessage'])->name('chat.send');
    Route::get('/chat/fetch/{receiver_id}',[ChatController::class, 'fetchMessages'])->name('chat.fetch');
    Route::get('/chat/unread',[ChatController::class, 'checkUnread'])->name('chat.unread'); 

    Route::get('/decks',[DeckController::class, 'index'])->name('decks.index');
    Route::post('/deck/create',[DeckController::class, 'store'])->name('deck.store');
    Route::get('/deck/{id}/builder', [DeckController::class, 'builder'])->name('deck.builder');
    Route::post('/deck/{id}/add', [DeckController::class, 'addCard'])->name('deck.addCard');
    Route::delete('/deck/{deckId}/remove/{cardId}',[DeckController::class, 'removeCard'])->name('deck.removeCard');
    Route::post('/deck/{id}/preview', [DeckController::class, 'setPreview'])->name('deck.setPreview');
    Route::get('/deck/{id}/export',[\App\Http\Controllers\DeckController::class, 'exportYdk'])->name('deck.export');
    Route::post('/deck/{id}/import',[\App\Http\Controllers\DeckController::class, 'importYdk'])->name('deck.import');
    Route::post('/nexus/upload', [NexusController::class, 'uploadYdk'])->name('nexus.upload');
});

/*
|--------------------------------------------------------------------------
| [RITEJ DOMAIN] Admin Secure Routes (Must be logged in AND be an Admin)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', \App\Http\Middleware\RoleMiddleware::class . ':admin'])->group(function () {
    
    // 1. The Admin Command Center
    Route::get('/admin/dashboard', function () {
        $users = \App\Models\User::all();
        $orders = \App\Models\Order::with('buyer')->latest()->get();
        return view('admin.dashboard', compact('users', 'orders'));
    })->name('admin.dashboard');

    // 2. [TECH LEAD FIX]: Force Verify Users (Bypasses Email!)
    Route::post('/admin/verify/{id}', function($id) {
        $user = \App\Models\User::findOrFail($id);
        $user->markEmailAsVerified();
        return back()->with('success', "User {$user->name} has been manually verified.");
    })->name('admin.verify');

    // 3. Delete Users
    Route::delete('/admin/user/{id}', function($id) {
        \App\Models\User::findOrFail($id)->delete();
        return back()->with('success', 'User purged from the database.');
    })->name('admin.delete_user');
});

require __DIR__.'/auth.php';

require __DIR__.'/auth.php';