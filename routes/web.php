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

/*
|--------------------------------------------------------------------------
| Public Routes (Anyone can visit these)
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return view('welcome'); // Sarah's cool landing page
});

// [SARAH DOMAIN: CATALOG] - Public route so anyone can browse cards!
Route::get('/catalog', [CatalogController::class, 'index'])->name('catalog');
Route::get('/catalog/{card}', [CatalogController::class, 'show'])->name('cards.show');                                                                            


/*
|--------------------------------------------------------------------------
| Authenticated Routes (Must be logged in to visit these)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    
    // [RITEJ DOMAIN: PROFILE MANAGEMENT]
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // [MOATAZ DOMAIN: INVENTORY]
    Route::get('/my-inventory',[ListingController::class, 'index'])->name('inventory.index');
    Route::post('/inventory/add',[ListingController::class, 'store'])->name('inventory.store');
    Route::delete('/inventory/{listing}',[ListingController::class, 'destroy'])->name('inventory.destroy');

    // [MOATAZ DOMAIN: CART]
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
    Route::patch('/cart/update', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/cart/remove', [CartController::class, 'remove'])->name('cart.remove');

    // [MOATAZ DOMAIN: CHECKOUT & ORDERS]
    Route::post('/checkout', [OrderController::class, 'checkout'])->name('checkout.process');
    Route::get('/my-orders', [OrderController::class, 'index'])->name('orders.index');
    Route::patch('/order/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.updateStatus');

    // [MOATAZ DOMAIN: REVIEWS]
    Route::post('/review/store',[ReviewController::class, 'store'])->name('reviews.store');

    // [MOATAZ & SARAH DOMAIN: AJAX CHAT API]
    Route::post('/chat/send',[ChatController::class, 'sendMessage'])->name('chat.send');
    Route::get('/chat/fetch/{receiver_id}',[ChatController::class, 'fetchMessages'])->name('chat.fetch');

    // [TECH LEAD DOMAIN: THE NEXUS]
    Route::post('/nexus/upload', [NexusController::class, 'uploadYdk'])->name('nexus.upload');
    
    //[MOATAZ DOMAIN: DECK BUILDER]
    Route::get('/decks',[\App\Http\Controllers\DeckController::class, 'index'])->name('decks.index');
    Route::post('/deck/create',[\App\Http\Controllers\DeckController::class, 'store'])->name('deck.store');
    Route::get('/deck/{id}/builder', [\App\Http\Controllers\DeckController::class, 'builder'])->name('deck.builder');
    Route::post('/deck/{id}/add',[\App\Http\Controllers\DeckController::class, 'addCard'])->name('deck.addCard');
    Route::delete('/deck/{deckId}/remove/{cardId}', [\App\Http\Controllers\DeckController::class, 'removeCard'])->name('deck.removeCard');
    Route::post('/deck/{id}/preview',[\App\Http\Controllers\DeckController::class, 'setPreview'])->name('deck.setPreview');

    // [SARAH DOMAIN: WISHLIST]
    Route::post('/wishlist/toggle',[\App\Http\Controllers\WishlistController::class, 'toggle'])->name('wishlist.toggle');
});


/*
|--------------------------------------------------------------------------
| [RITEJ DOMAIN] Admin Secure Routes (Must be logged in AND be an Admin)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', \App\Http\Middleware\RoleMiddleware::class . ':admin'])->group(function () {
    Route::get('/admin/dashboard', function () {
        return view('admin.dashboard');
    })->name('admin.dashboard');
});


/*
|--------------------------------------------------------------------------
| Laravel Breeze Default Auth Routes (Login, Register, Password Reset)
|--------------------------------------------------------------------------
*/
require __DIR__.'/auth.php';