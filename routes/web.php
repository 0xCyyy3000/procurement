<?php

use App\Events\Requisition;
use App\Models\InventoryItems;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Models\RequisitionNotification;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ItemsController;
use App\Http\Controllers\SidebarController;
use App\Http\Controllers\RequisitionController;
use App\Http\Controllers\InventoryItemsController;
use App\Http\Controllers\PurchasedOrdersController;
use App\Http\Controllers\RequisitionNotificationController;

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

// Route::get('/login', [UserController::class, 'login'])->middleware('guest')->name('login');
// Route::post('/logout', [UserController::class, 'logout'])->middleware('auth');
// Route::post('/users/authenticate', [UserController::class, 'authenticate']);

Auth::routes();
//Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');


Route::get('/api/get/requisitions', [RequisitionController::class, 'apiIndex'])->middleware('auth');
Route::get('/requisitions/index', [RequisitionController::class, 'index'])->middleware('auth');

Route::group(['middleware' => 'auth', 'prefix' => '/'], function () {
    Route::get('/', [SidebarController::class, 'dashboard']);
    Route::get('/notifications', [SidebarController::class, 'notifications']);
    Route::get('/create_req', [SidebarController::class, 'createReq']);
    Route::get('/requisitions', [SidebarController::class, 'requisitions']);
    Route::get('/purchased_orders', [SidebarController::class, 'purchasedOrders']);
});

Route::group(['middleware' => 'auth', 'prefix' => '/requisitions'], function () {
    Route::post('/create', [RequisitionController::class, 'store']);
    Route::post('/{requisition}', [RequisitionController::class, 'select']);
    Route::post('/copy/{requisition}', [RequisitionController::class, 'copy']);
    Route::post('/update/{requisition}', [RequisitionController::class, 'update']);
});

Route::group(['middleware' => 'auth', 'prefix' => '/orders'], function () {
    Route::post('/select/{po_id}', [PurchasedOrdersController::class, 'select']);
    Route::post('/update/{po_id}', [PurchasedOrdersController::class, 'update']);
});

Route::get('/supplier', function () {
    return back();
})->middleware('auth');

Route::group(['middleware' => 'auth', 'prefix' => '/items'], function () {
    Route::get('/', [ItemsController::class, 'index']);
    Route::get('/{item}', [ItemsController::class, 'select']);
    Route::post('/store/{item}', [ItemsController::class, 'store']);
    Route::post('/units', [ItemsController::class, 'fetchUnits']);
});

Route::group(['middleware' => 'auth', 'prefix' => '/savedItems'], function () {
    Route::get('/', [ItemsController::class, 'indexSavedItems']);
    Route::post('/{item}', [ItemsController::class, 'selectSavedItems']);
    Route::post('/update/{item}', [ItemsController::class, 'updateSavedItem']);
    Route::post('/destroy/{item}', [ItemsController::class, 'destroySavedItem']);
    Route::post('/clear/{user}', [ItemsController::class, 'clearSavedItem']);
});

// Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::get('/test/pusher', function () {
    // dd(request()->data);
    $name = request()->name;
    event(new Requisition($name, 'test'));
})->name('test.pusher');


Route::get('/test/listen', function () {
    return view('partials._pusher');
});

Route::get('/api/test/notification', function () {
    if (Auth::id() <= 3) {
        $notifs = RequisitionNotification::latest('id')->get();
    } else {
        $notifs = RequisitionNotification::where('user_id', Auth::id())->get();
    }
    return response()->json($notifs);
});

Route::group(['middleware' => 'auth', 'prefix' => '/api/test/notification'], function () {
    Route::get('/index', [RequisitionNotificationController::class, 'index']);
    Route::get('/select/{id}', [RequisitionNotificationController::class, 'select']);
});
