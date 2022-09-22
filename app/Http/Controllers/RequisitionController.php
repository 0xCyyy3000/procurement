<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\SavedItems;
use App\Models\Requisitions;
use Illuminate\Http\Request;
use App\Models\SubmittedItems;
use Illuminate\Support\Facades\Session;
use Requisition;

class RequisitionController extends Controller
{
    public function apiIndex()
    {
        if (strtoupper(auth()->user()->department) == 'ADMIN') {
            $requisitions = Requisitions::latest()->get();
            return response()->json($requisitions);
        } else return response()->json('empty');
    }

    public function index()
    {
        $requisitions = Requisitions::latest()->get();
        return response()->json($requisitions);
    }

    public function select(Request $request)
    {
        $requisition = Requisitions::where('req_id', $request->req_id)->get();
        $response['requisition'] = $requisition;

        if ($requisition) {
            $response['status'] = 200;

            $items = SubmittedItems::where('req_id', $request->req_id)->get();
            $response['items'] = $items;
        } else $response['status'] = 404;

        return response()->json($response);
    }

    public function getRequisition()
    {
    }

    public function store(Request $request)
    {
        $formFields = $request->validate([
            'description' => 'required',
            'priority' => 'required',
            'user_id' => 'required',
            'maker' => 'required',
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

        return "" . $strData . "";
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

        // dd($savedItems);
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
