<?php

use App\Http\Controllers\OfferCsvImportController;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::post('/order/tracking', function (\App\Http\Requests\TrackingIdRequest $request) {
        Order::where('order_id', '=', $request->validated('order_id'))->update([
            'tracking_id' => $request->validated('tracking_id'),
        ]);
    });

    Route::post('/offers/stock', [OfferCsvImportController::class, 'store']);
});
