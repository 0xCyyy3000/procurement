<?php

use App\Http\Controllers\InventoryItemsController;
use App\Http\Controllers\ItemsController;
use App\Http\Controllers\PurchasedOrdersController;
use Illuminate\Http\Request;
use App\Models\InventoryItems;
use App\Models\PurchasedOrders;
use App\Models\RequisitionItems;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RequisitionController;
use App\Http\Controllers\UserSavedItemsController;
use App\Models\RequisitionNotification;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['prefix' => 'test'], function () {
    Route::get('/orders/index', [PurchasedOrdersController::class, 'apiIndex']);
    Route::get('/reqs', [RequisitionController::class, 'apiRequistions']);
});
