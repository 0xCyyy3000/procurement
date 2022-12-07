<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Items;
use App\Models\Units;
use App\Models\Suppliers;
use App\Models\SavedItems;
use App\Models\Requisitions;
use App\Models\PurchasedOrders;
use Illuminate\Support\Facades\Auth;

class SidebarController extends Controller
{

    public function getDepartment()
    {
        $user = User::join('departments', 'departments.id', '=', 'users.department')
            ->where('users.id', Auth::id())
            ->get(['departments.department', 'users.name']);

        return $user;
    }

    public function dashboard()
    {
        $user = $this->getDepartment();

        return view(
            'procurement.dashboard',
            [
                'section' => [
                    'user' => $user[0],
                    'page' => 'dashboard',
                    'title' => 'Dashboard',
                    'middle' => 'partials._updates',
                    'bottom' => 'partials._analytics'
                ]
            ]
        );
    }

    public function notifications()
    {
        $user = $this->getDepartment();
        return view(
            'procurement.notifications',
            [
                'section' => [
                    'user' => $user[0],
                    'page' => 'notifications',
                    'title' => 'Notifications',
                    'middle' => null,
                    'bottom' => null
                ]
            ]
        );
    }

    public function createReq()
    {
        $user = $this->getDepartment();

        $ids = SavedItems::where('user_id', auth()->user()->id)->get('unit_id');
        $units = array();

        foreach ($ids as $id) {
            array_push(
                $units,
                Units::where('unit_id', $id->unit_id)->get('unit_name')[0]->unit_name
            );
        }

        return view(
            'procurement.create_req',
            [
                'section' => [
                    'user' => $user[0],
                    'page' => 'create_req',
                    'title' => 'Create Requisition',
                    'middle' => 'partials._priority',
                    'bottom' => null
                ],
                'items' => Items::all(),
                'savedItems' => request()->user()->savedItems()->get(),
                'units' => $units
            ]
        );
    }

    public function requisitions()
    {
        if (strtoupper(auth()->user()->department) <= 2) {
            $requisitions = Requisitions::latest()->get();
            $update_status = 'partials._update-status';
            $suppliers =  Suppliers::get();
        } else {
            $requisitions = request()->user()->requisitions()->latest()->get();
            $update_status = null;
            $suppliers = null;
        }

        $user = $this->getDepartment();

        return view(
            'procurement.requisitions',
            [
                'requisitions' => $requisitions,
                'userId' => auth()->user()->id,
                'suppliers' => $suppliers,
                'section' =>
                [
                    'user' => $user[0],
                    'page' => 'requisitions',
                    'title' => 'Requisitions',
                    'middle' => $update_status,
                    'bottom' => null,
                    'userDepartmant' => auth()->user()->department
                ]
            ]
        );
    }

    public function purchasedOrders()
    {
        $middle = null;
        $bottom = null;
        $purchasedOrders = null;

        if (strtoupper(auth()->user()->department) == 'ADMIN') {
            $purchasedOrders = PurchasedOrders::latest()->get();
            $middle = 'partials._update-order';
            $bottom = 'partials._update-order-info';
        }

        foreach ($purchasedOrders as $purchasedOrder) {
            $purchasedOrder['supplier_name'] = Suppliers::find($purchasedOrder->supplier);
        }

        $user = $this->getDepartment();

        return view(
            'procurement.purchased-orders',
            [
                'purchasedOrders' => $purchasedOrders,
                'section' =>
                [
                    'user' => $user[0],
                    'page' => 'purchased_orders',
                    'title' => 'Purchased Orders',
                    'middle' => $middle,
                    'bottom' => $bottom
                ]
            ]
        );
    }
}
