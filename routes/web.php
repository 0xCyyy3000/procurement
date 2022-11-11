<?php

use App\Http\Controllers\InventoryItemsController;
use App\Http\Controllers\ItemsController;
use App\Http\Controllers\PurchasedOrdersController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SidebarController;
use App\Http\Controllers\RequisitionController;
use App\Models\InventoryItems;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/login', [UserController::class, 'login'])->middleware('guest')->name('login');
Route::post('/logout', [UserController::class, 'logout'])->middleware('auth');
Route::post('/users/authenticate', [UserController::class, 'authenticate']);

Route::get('/', [SidebarController::class, 'dashboard'])->middleware('auth');
Route::get('/create_req', [SidebarController::class, 'createReq'])->middleware('auth');
Route::get('/requisitions', [SidebarController::class, 'requisitions'])->middleware('auth');
Route::get('/purchased_orders', [SidebarController::class, 'purchasedOrders'])->middleware('auth');


Route::get('/api/get/requisitions', [RequisitionController::class, 'apiIndex'])->middleware('auth');
Route::get('/requisitions/index', [RequisitionController::class, 'index'])->middleware('auth');

Route::post('/requisition/create', [RequisitionController::class, 'store'])->middleware('auth');
Route::post('/requisitions/{requisition}', [RequisitionController::class, 'select'])->middleware('auth');
Route::post('/requisitions/copy/{requisition}', [RequisitionController::class, 'copy'])->middleware('auth');
Route::post('/requisitions/update/{requisition}', [RequisitionController::class, 'update'])->middleware('auth');

Route::group(['prefix' => 'orders'], function () {
    Route::post('/select/{po_id}', [PurchasedOrdersController::class, 'select']);
    Route::post('/update/{po_id}', [PurchasedOrdersController::class, 'update']);
});


Route::get('/supplier', function () {
    return view(
        'procurement.supplier',
        [
            'section' => [
                'page' => 'supplier',
                'title' => 'Supplier',
                'middle' => null,
                'bottom' => null
            ]
        ]
    );
});

Route::get('/items', [ItemsController::class, 'index']);
Route::get('/items/{item}', [ItemsController::class, 'select']);
Route::post('/items/store/{item}', [ItemsController::class, 'store']);
Route::post('/items/units', [ItemsController::class, 'fetchUnits']);

Route::get('/savedItems', [ItemsController::class, 'indexSavedItems']);
Route::post('/savedItems/{item}', [ItemsController::class, 'selectSavedItems']);
Route::post('/savedItems/update/{item}', [ItemsController::class, 'updateSavedItem']);
Route::post('/savedItems/destroy/{item}', [ItemsController::class, 'destroySavedItem']);
Route::post('/savedItems/clear/{user}', [ItemsController::class, 'clearSavedItem']);
