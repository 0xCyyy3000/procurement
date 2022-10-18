<?php

namespace App\Http\Controllers;

use App\Models\SavedItems;
use App\Models\Requisitions;
use Illuminate\Http\Request;
use App\Models\SubmittedItems;

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

        // to access a specific item in a collection
        // $collection[index]->key 

        // print('===');
        // print($requisition[0]->maker);
        // print('\n');

        // Anoter example
        // $itemsTest = collect([
        //     'item' => ['item1', 'item2', 'item3'],
        //     'units' => [['box', 'rim', 'pcs'], ['kg', 'pounds'], 'sack'],
        //     'prices' => [[130, 97, 13], [45, 21], 200]
        // ]);
        // print($itemsTest['units'][0][0]);

        return response()->json($response);
    }

    public function store(Request $request)
    {
        $formFields = $request->validate([
            'description' => 'required',
            'priority' => 'required',
            'user_id' => 'required',
            'maker' => 'required',
            'status' => 'required',
            'approval_count' => ''
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

    public function copy(Request $request)
    {
        // dd($requisition);
        $itemsToCopy = request()->user()->submittedItems()->where('req_id', $request->req_id)->get(['item_ids', 'items', 'units', 'qtys']);

        $item_ids = explode(',', $itemsToCopy[0]->item_ids);
        $items = explode(',', $itemsToCopy[0]->items);
        $item_units = explode(',', $itemsToCopy[0]->units);
        $item_qtys = explode(',', $itemsToCopy[0]->qtys);

        for ($index = 0; $index < sizeof($item_ids); $index++) {
            $saved = SavedItems::create([
                'user_id' => auth()->user()->id,
                'item_id' => $item_ids[$index],
                'item' => $items[$index],
                'unit' => $item_units[$index],
                'qty' => $item_qtys[$index]
            ]);
            if ($saved)
                $response['status'] = 200;
            else $response['status'] = 500;
        }
        return response()->json($response);
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

    public function update(Request $request)
    {
        $formFields = $request->validate([
            'req_id' => 'required',
            'signatory' => 'required',
            'approval' => 'required',
            'message' => ''
        ]);

        $requisition = Requisitions::where('req_id', $request->req_id)->get();
        $approvalCount = $requisition[0]->approval_count;

        if (strtoupper($request->approval) == 'SIGNED') {
            if ($approvalCount == 0) {
                $reqStatus = 'Partially Approved';
                $signatories = $request->signatory;
            } else {
                $reqStatus = 'Approved';
                $signatories = 'Both';
            }
            $approvalCount += 1;
        } else {
            $reqStatus = 'Rejected';
        }

        $formFields['evaluator'] = auth()->id();
        $formFields['approval_count'] = $approvalCount;
        $formFields['status'] = $reqStatus;
        $formFields['signatory'] = $signatories;

        $affectedRows = Requisitions::where('req_id', $request->req_id)->update($formFields);

        if ($affectedRows) $response['status'] = 200;
        else $response['status'] = 500;

        return response()->json($response);
    }
}
