<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RequisitionController;
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

Route::group(['prefix' => 'v1/requisition'], function () {
    Route::get('/select', function () {
        return [
            'id' => 1,
            'requisition' => 'select'
        ];
    });

    Route::get('/update', function () {
        return [
            'id' => 1,
            'requisition' => 'update'
        ];
    });
});

Route::group(['prefix' => 'inventory-items'], function () {
    Route::post('/store', function () {

        return InventoryItems::create([
            'category_id' => 1,
            'item' => 'Item 1',
            'units' => [
                ['test', 'test2', 'test'],
                ['pcs', 'box', 'ream']
            ],
            'qtys' => [
                [
                    'test' => '3',
                    'test2' => '110',
                    'test3' => '9'
                ],
                [
                    'pcs' => '15',
                    'box' => '10',
                    'ream' => '2'
                ]
            ],
            'prices' => [
                [
                    'test' => '79',
                    'test2' => '254',
                    'test3' => '189'
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
