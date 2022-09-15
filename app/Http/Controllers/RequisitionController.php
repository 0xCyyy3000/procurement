<?php

namespace App\Http\Controllers;

use App\Models\Requisitions;
use App\Models\User;
use Illuminate\Http\Request;

class RequisitionController extends Controller
{
    public function index()
    {
        $requisitions = request()->user()->requisitions()->get();
        $update_status = null;
        if (strtoupper(request()->user()->department) == 'ADMIN') {
            $requisitions = Requisitions::latest()->get();
            $update_status = 'partials._update-status';
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
