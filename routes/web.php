<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\OrderController;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::get('/dashboard', function () {
    return view('dashboard', [
        'categoryCount' => \App\Models\Category::count(),
        'productCount' => \App\Models\Product::count(),
        'customerCount' => \App\Models\Customer::count(),
        'orderCount' => \App\Models\Order::count(),
        'recentOrders' => \App\Models\Order::with('customer')
            ->latest()->take(5)->get(),
    ]);
})->name('dashboard');

Route::resource('categories', CategoryController::class);
Route::resource('products', ProductController::class);
Route::resource('customers', CustomerController::class);
Route::resource('orders', OrderController::class);