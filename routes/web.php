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
    // Change the default dashboard route to redirect to the Catalog or Inventory
Route::get('/dashboard', function () {
    return redirect()->route('inventory.index'); // Redirects straight to your Binder!
})->middleware(['auth', 'verified'])->name('dashboard');

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
});


/*
|--------------------------------------------------------------------------
| [RITEJ DOMAIN] Admin Secure Routes (Must be logged in AND be an Admin)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', \App\Http\Middleware\RoleMiddleware::class . ':admin'])->group(function () {
    
    // This route proves to the jury that your RoleMiddleware works perfectly.
    Route::get('/admin/dashboard', function () {
        return "Welcome Pegasus. You are an Admin. The RoleMiddleware protected this page.";
    })->name('admin.dashboard');

});


/*
|--------------------------------------------------------------------------
| Laravel Breeze Default Auth Routes (Login, Register, Password Reset)
|--------------------------------------------------------------------------
*/
require __DIR__.'/auth.php';