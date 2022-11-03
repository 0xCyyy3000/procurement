<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Items;
use App\Models\Units;
use App\Models\SavedItems;
use App\Models\Requisitions;
use App\Models\Suppliers;
use Illuminate\Http\Request;
use SebastianBergmann\CodeCoverage\Report\Xml\Unit;

class SidebarController extends Controller
{
    public function dashboard()
    {
        return view(
            'procurement.dashboard',
            [
                'section' => [
                    'page' => 'dashboard',
                    'title' => 'Dashboard',
                    'middle' => 'partials._updates',
                    'bottom' => 'partials._analytics'
                ]
            ]
        );
    }

    public function createReq()
    {
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
        if (strtoupper(auth()->user()->department) == 'ADMIN') {
            $requisitions = Requisitions::latest()->get();
            $update_status = 'partials._update-status';
        } else {
            $requisitions = request()->user()->requisitions()->latest()->get();
            $update_status = null;
        }

        return view(
            'procurement.requisitions',
            [
                'requisitions' => $requisitions,
                'userId' => auth()->user()->id,
                'section' =>
                [
                    'page' => 'requisitions',
                    'title' => 'Requisitions',
                    'middle' => $update_status,
                    'bottom' => null
                ],
                'suppliers' => Suppliers::get()
            ]
        );
    }

    public function purchasedOrders()
    {
        return view('procurement.purchased-orders');
    }
}
