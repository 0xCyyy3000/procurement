<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Items;
use App\Models\Units;
use App\Models\Suppliers;
use App\Models\SavedItems;
use App\Models\Inventories;
use App\Models\Distribution;
use App\Models\Requisitions;
use App\Models\DeliveryAddress;
use App\Models\Department;
use App\Models\PurchasedOrders;
use App\Models\DistributionItem;
use Illuminate\Support\Facades\Auth;

class SidebarController extends Controller
{

    public function getDepartment()
    {
        $user = User::join('departments', 'departments.id', '=', 'users.department')
            ->where('users.id', Auth::id())
            ->get(['departments.id', 'departments.department', 'users.name']);

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
        if (strtoupper(auth()->user()->department) <= 3) {
            $requisitions = Requisitions::latest()->get();
            $update_status = 'partials._update-status';
            $suppliers =  Suppliers::get();
            $delivery_address = DeliveryAddress::all();
        } else {
            $requisitions = request()->user()->requisitions()->latest()->get();
            $update_status = null;
            $suppliers = null;
            $delivery_address = null;
        }

        $user = $this->getDepartment();

        return view(
            'procurement.requisitions',
            [
                'requisitions' => $requisitions,
                'delivery_address' => $delivery_address,
                'userId' => auth()->user()->id,
                'suppliers' => $suppliers,
                'section' =>
                [
                    'user' => $user[0],
                    'page' => 'requisitions',
                    'title' => 'Requisitions',
                    'middle' => $update_status,
                    'bottom' => null,
                    'requisitions' => $requisitions
                ]
            ]
        );
    }

    public function purchasedOrders()
    {
        $middle = null;
        $bottom = null;
        $purchasedOrders = null;

        if (auth()->user()->department <= 3) {
            $purchasedOrders = PurchasedOrders::latest()->get();
            foreach ($purchasedOrders as $order) {
                $order['supplier'] = PurchasedOrders::join('suppliers', 'suppliers.id', '=', 'purchased_orders.supplier')
                    ->where('purchased_orders.id', $order->id)
                    ->first(['suppliers.company_name', 'suppliers.id', 'suppliers.contact_person', 'suppliers.address']);
            }

            $middle = 'partials._update-order';
            $bottom = 'partials._update-order-info';
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

    public function suppliers()
    {
        $user = $this->getDepartment();
        $middle = null;
        $bottom = null;
        $suppliers = null;

        if (auth()->user()->department <= 3) {
            $suppliers = Suppliers::latest()->get();
            $middle = 'partials._create-supplier';
        }
        return view(
            'procurement.suppliers',
            [
                'suppliers' => $suppliers,
                'section' =>
                [
                    'user' => $user[0],
                    'page' => 'suppliers',
                    'title' => 'Suppliers',
                    'middle' => $middle,
                    'bottom' => $bottom
                ]
            ]
        );
    }

    public function inventory()
    {
        $user = $this->getDepartment();
        $middle = null;
        $bottom = null;
        $inventoryItems = null;

        if (auth()->user()->department <= 3) {
            $inventoryItems = Inventories::join('items', 'items.item_id', '=', 'inventories.item_id')
                ->join('units', 'units.unit_id', '=', 'inventories.unit_id')->orderBy('inventories.created_at', 'desc')->get(['units.*', 'items.*', 'inventories.*']);

            $middle = 'partials._create-inventory';
        }
        return view(
            'procurement.inventory',
            [
                'inventoryItems' => $inventoryItems,
                'units' => Units::all(),
                'items' => Items::get(['item_id', 'item']),
                'section' =>
                [
                    'user' => $user[0],
                    'page' => 'inventory',
                    'title' => 'Inventory',
                    'middle' => $middle,
                    'bottom' => $bottom
                ]
            ]
        );
    }

    public function distributions()
    {
        $user = $this->getDepartment();
        $middle = null;
        $bottom = null;
        $distributions = null;

        if (auth()->user()->department <= 3) {
            $distributions = Distribution::join('users', 'users.id', '=', 'distributions.recipient')
                ->join('delivery_addresses', 'delivery_addresses.id', '=', 'distributions.address')
                ->join('departments', 'departments.id', '=', 'users.department')
                ->get(['distributions.*', 'users.email', 'users.name', 'departments.department', 'delivery_addresses.address']);
            // dd($distributions);

            $recipients = PurchasedOrders::join('requisitions', 'requisitions.req_id', '=', 'purchased_orders.req_id')
                ->join('users', 'users.id', '=', 'requisitions.user_id')->join('departments', 'departments.id', '=', 'users.department')
                ->where('purchased_orders.status', '>', 0)->get(['users.*', 'departments.department']);

            $addresses = DeliveryAddress::all();

            $middle = 'partials._distribution';
        }
        return view(
            'procurement.distributions',
            [
                'distributions' => $distributions,
                'recipients' => $recipients,
                'addresses' => $addresses,
                'section' =>
                [
                    'user' => $user[0],
                    'page' => 'distributions',
                    'title' => 'Distributions',
                    'middle' => $middle,
                    'bottom' => $bottom
                ]
            ]
        );
    }

    public function settings()
    {
        $user = $this->getDepartment();
        $middle = null;
        $bottom = null;
        return view(
            'procurement.settings',
            [
                'departments' => Department::get(['id', 'department']),
                'section' =>
                [
                    'user' => $user[0],
                    'page' => 'settings',
                    'title' => 'Account Settings',
                    'middle' => $middle,
                    'bottom' => $bottom
                ]
            ]
        );
    }
}
