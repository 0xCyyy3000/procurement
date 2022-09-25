<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Items;
use App\Models\Requisitions;
use Illuminate\Http\Request;

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
                'savedItems' => request()->user()->savedItems()->get()
            ]
        );
    }

    public function requisitions()
    {
        if (strtoupper(request()->user()->department) == 'ADMIN') {
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
                'section' => [
                    'page' => 'requisitions',
                    'title' => 'Requisitions',
                    'middle' => $update_status,
                    'bottom' => null
                ]
            ]
        );
    }
}
