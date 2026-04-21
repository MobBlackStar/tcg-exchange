<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ListingController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\NexusController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // [MOATAZ DOMAIN: INVENTORY]
    Route::get('/my-inventory', [ListingController::class, 'index'])->name('inventory.index');
    Route::post('/inventory/add', [ListingController::class, 'store'])->name('inventory.store');
    Route::delete('/inventory/{listing}', [ListingController::class, 'destroy'])->name('inventory.destroy');

    // [MOATAZ DOMAIN: CART]
    //[TECH LEAD DOMAIN: THE NEXUS]
    Route::post('/nexus/upload', [NexusController::class, 'uploadYdk'])->name('nexus.upload');
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
    Route::patch('/cart/update', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/cart/remove', [CartController::class, 'remove'])->name('cart.remove');

    // [MOATAZ DOMAIN: CHECKOUT & ORDERS]
    Route::post('/checkout', [OrderController::class, 'checkout'])->name('checkout.process');
    Route::get('/my-orders', [OrderController::class, 'index'])->name('orders.index');
    Route::patch('/order/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.updateStatus');

    // [MOATAZ DOMAIN: REVIEWS]
    Route::post('/review/store', [ReviewController::class, 'store'])->name('reviews.store');

    // [MOATAZ & SARAH DOMAIN: AJAX CHAT API]
    Route::post('/chat/send', [ChatController::class, 'sendMessage'])->name('chat.send');
    Route::get('/chat/fetch/{receiver_id}', [ChatController::class, 'fetchMessages'])->name('chat.fetch');
});

require __DIR__.'/auth.php';