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

    public function store(Request $request)
    {
        $formFields = $request->validate([
            'description' => 'required',
            'priority' => 'required',
            'user_id' => 'required',
            'status' => 'required'
        ]);

        $created = Requisitions::create($formFields);

        if ($created) {
            $result['status'] = 200;
        } else $result['status'] = 500;

        return response()->json($result);
    }
}
