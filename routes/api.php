<?php

use Illuminate\Http\Request;
use App\Models\InventoryItems;
use App\Models\PurchasedOrders;
use App\Models\RequisitionItems;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RequisitionController;
use App\Http\Controllers\UserSavedItemsController;

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


Route::get('/v1/requisitions', function () {
    return InventoryItems::get();
});

Route::group(['prefix' => 'saved-items'], function () {
    // Get requests
    Route::get('/index/{row}', [UserSavedItemsController::class, 'index']);
    Route::get('/select/{row}', [UserSavedItemsController::class, 'select']);

    // Post requests
    Route::post('/update/row/{row}', [UserSavedItemsController::class, 'update']);
    Route::post('/remove/{row}', [UserSavedItemsController::class, 'removeItem']);
    Route::post('/destroy/{row}', [UserSavedItemsController::class, 'destroy']);
    Route::post('/create', [UserSavedItemsController::class, 'store']);
    Route::post('/add/{row}', [UserSavedItemsController::class, 'add']);
});

Route::group(['prefix' => 'inventory-items'], function () {
    Route::post('/store', function () {

        return InventoryItems::create([
            'category_id' => 1,
            'item' => 'Item 1',
            'units' => [
                ['unit 1', 'unit 2', 'unit 3'],
                ['pcs', 'box', 'ream']
            ],
            'qtys' => [
                [
                    'unit 1' => '3',
                    'unit 2' => '110',
                    'unit 3' => '9'
                ],
                [
                    'pcs' => '15',
                    'box' => '10',
                    'ream' => '2'
                ]
            ],
            'prices' => [
                [
                    'unit 1' => '79',
                    'unit 2' => '254',
                    'unit 3' => '189'
                ],
                [
                    'pcs' => '79',
                    'box' => '254',
                    'ream' => '189'
                ]
            ],

            'supplier' => 'Supplier 1',
            'worth' => 522
        ]);
    });

    Route::get('/index', function () {
        return InventoryItems::get();
    });

    Route::get('/select/{item}', function (Request $request) {
        return InventoryItems::where('id', $request->item)->get(['units', 'qtys', 'prices']);
    });
});

Route::group(['prefix' => 'requisition'], function () {
    Route::post('/store', [RequisitionController::class, 'replicateSavedItems']);
    Route::get('/select', [RequisitionController::class, 'showRequisitionItems']);
    Route::post('/update', function (Request $request) {
        $items = RequisitionItems::where('req_id', $request->req_id)->get('items');

        $purchasedItems = PurchasedOrders::create([
            'status' => 'Pending',
            'notes' => 'Lorem ipsum ...',
            'supplier' => 'Supplier 1',
            'delivery_address' => 'Delivery Address 1',
            'req_id' => $request->req_id,
            'purchased_items' => $items[0]->items,
            'order_total' => 750.89,
            'payment' => 'Paid'
        ]);

        if ($purchasedItems)
            RequisitionItems::where('req_id', $request->req_id)->delete();
    });
});
