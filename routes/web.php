<?php

use App\Http\Controllers\ItemsController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SidebarController;
use App\Http\Controllers\RequisitionController;

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
// Route::get('/dashboard', [SidebarController::class, 'dashboard'])->middleware('auth');
// Route::get('/admin/dashboard', [SidebarController::class, 'dashboard'])->middleware('auth');
Route::get('/create_req', [SidebarController::class, 'createReq'])->middleware('auth');
Route::get('/requisitions', [SidebarController::class, 'requisitions'])->middleware('auth');


Route::get('/requisitions/index', [RequisitionController::class, 'index'])->middleware('auth');
Route::get('/api/get/requisitions', [RequisitionController::class, 'apiIndex'])->middleware('auth');
Route::post('/requisitions/{requisition}', [RequisitionController::class, 'select'])->middleware('auth');

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
Route::post('/items/units/{item}', [ItemsController::class, 'fetchUnits']);

Route::get('/savedItems', [ItemsController::class, 'indexSavedItems']);
Route::post('/savedItems/{item}', [ItemsController::class, 'selectSavedItems']);
Route::post('/savedItems/update/{item}', [ItemsController::class, 'updateSavedItem']);
Route::post('/savedItems/destroy/{item}', [ItemsController::class, 'destroySavedItem']);

Route::post('/submit/requisition', [RequisitionController::class, 'store']);
