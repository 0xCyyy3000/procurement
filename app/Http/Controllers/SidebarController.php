<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Items;
use App\Models\Units;
use App\Models\SavedItems;
use App\Models\Requisitions;
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
        $ids = SavedItems::where('user_id', auth()->user()->id)->get('unit_id')[0];
        $units = Units::get(['unit_id', 'unit_name']);
        $savedUnits = array();

        foreach ($ids as $id) {
            array_push($savedUnits, $units[$id]);
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
                'units' => $savedUnits
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
                'section' => [
                    'page' => 'requisitions',
                    'title' => 'Requisitions',
                    'middle' => $update_status,
                    'bottom' => null
                ]
            ]
        );
    }

    public function purchasedOrders()
    {
        return view('procurement.purchased-orders');
    }
}
