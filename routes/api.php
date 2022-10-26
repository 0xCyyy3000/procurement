<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RequisitionController;
use App\Http\Controllers\UserSavedItemsController;
use App\Models\InventoryItems;

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
});
