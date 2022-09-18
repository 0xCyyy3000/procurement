<?php

namespace App\Http\Controllers;

use App\Models\Requisitions;
use App\Models\SavedItems;
use App\Models\SubmittedItems;
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
            $reqId = Requisitions::select('req_id')->latest('req_id')->first();
            $result['status'] = $this->indexSavedItems($request->user_id, $reqId['req_id']);
        }

        if ($result['status'] != 200) {
            Requisitions::latest('req_id')->first()->delete();
        }

        return response()->json($result);
    }

    public function getPlainString($data, $key)
    {
        $itemData = array();
        for ($i = 0; $i < count($data); $i++) {
            array_push($itemData, $data[$i]->$key);
        }

        $strData = implode(',', $itemData);

        return "'" . $strData . "'";
    }

    public function indexSavedItems($userId, $reqId)
    {
        $item_ids = SavedItems::where('user_id', $userId)->get('item_id')->toJson();
        $item_names = SavedItems::where('user_id', $userId)->get('item')->toJson();
        $item_units = SavedItems::where('user_id', $userId)->get('unit')->toJson();
        $item_qtys = SavedItems::where('user_id', $userId)->get('qty')->toJson();

        $ids = $this->getPlainString(json_decode($item_ids), 'item_id');
        $items = $this->getPlainString(json_decode($item_names), 'item');
        $units = $this->getPlainString(json_decode($item_units), 'unit');
        $qtys = $this->getPlainString(json_decode($item_qtys), 'qty');

        $savedItems = array(
            'item_ids' => $ids,
            'items' => $items,
            'units' => $units,
            'qtys' => $qtys
        );

        dd($savedItems);
        return $this->submitItems($savedItems, $userId, $reqId);
    }

    public function submitItems($savedItems, $userId, $reqId)
    {
        SubmittedItems::create([
            'req_id' => $reqId,
            'user_id' => $userId,
            'item_ids' => $savedItems['item_ids'],
            'items' => $savedItems['items'],
            'units' => $savedItems['units'],
            'qtys' => $savedItems['qtys']
        ]);

        return $this->disposeSavedItems($userId);
    }

    // Disposing the submitted Saved Items
    public function disposeSavedItems($userId)
    {
        $affectedRows = SavedItems::where('user_id', $userId)->delete();
        if ($affectedRows > 0) return 200;
        else return 433;
    }
}
