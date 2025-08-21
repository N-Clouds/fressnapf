<?php

use App\Models\Order;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

//Route::get('dashboard', function () {
//    return Inertia::render('Dashboard');
//})->middleware(['auth', 'verified'])->name('dashboard');

Route::group(['middleware' => 'auth', 'verified'], function () {
    Route::get('dashboard', function () {
        $orders = Order::whereHas('billingAddress')->with(['lines', 'billingAddress', 'shippingAddress'])
            ->whereNull('exported_at')
            ->orderBy('id')
            ->get();

        return Inertia::render('Dashboard', [
            'orders' => $orders,
        ]);
    })->name('dashboard');

    Route::get('orders/export', [\App\Http\Controllers\ExportOrderController::class, 'exportOrder'])->name('orders.export');
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
