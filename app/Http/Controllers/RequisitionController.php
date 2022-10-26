<?php

namespace App\Http\Controllers;

use App\Models\RequisitionItems;
use App\Models\SavedItems;
use App\Models\Requisitions;
use Illuminate\Http\Request;
use App\Models\SubmittedItems;
use App\Models\UserSavedItems;

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

        $signatories = array(
            array('name' => 'School Director', 'approval' => 'Not yet'),
            array('name' => 'Branch Manager', 'approval' => 'Not yet')
        );

        $formFields['signatories'] = json_encode($signatories);

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


    public function update(Request $request)
    {
        $formFields = $request->validate([
            'req_id' => 'required',
            'signatories' => 'required',
            'message' => ''
        ]);

        $requisition = Requisitions::where('req_id', $request->req_id)->get();
        $approvalCount = $requisition[0]->approval_count;
        $signatories = $requisition[0]->signatories;

        foreach ($signatories as $signatory) {
            if (strtoupper($request->signatories) == 'BOTH' || $signatory->name == $request->signatories) {
                if ($approvalCount == 0 and strtoupper($request->approval) == 'APPROVED') {
                    $reqStatus = 'Partially Approved';
                } else if ($approvalCount >= 1 and strtoupper($request->approval) == 'APPROVED') {
                    $reqStatus = 'Approved';
                } else $reqStatus = 'Rejected';

                $signatory->approval = $request->approval;
                $approvalCount++;
            }
        }

        $formFields['evaluator'] = auth()->user()->name;
        $formFields['approval_count'] = $approvalCount;
        $formFields['status'] = $reqStatus;
        $formFields['signatories'] = $signatories;

        $affectedRows = Requisitions::where('req_id', $request->req_id)->update($formFields);

        if ($affectedRows) $response['status'] = 200;
        else $response['status'] = 500;

        return response()->json($response);
    }

    public function copy(Request $request)
    {
        // dd($requisition);
        $itemsToCopy = request()->user()->submittedItems()
            ->where('req_id', $request->req_id)
            ->get(['item_ids', 'items', 'units', 'qtys']);

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

    public function replicateSavedItems(Request $request)
    {
        $savedItems = UserSavedItems::where('id', $request->row)->get('items');

        $requisitionItems =  RequisitionItems::create([
            'req_id' => $request->req_id,
            'items' => $savedItems[0]->items
        ]);

        if ($requisitionItems) // If has been copied, delete the Saved Items
            return UserSavedItems::destroy($request->row);
        else
            return null;
    }

    public function showRequisitionItems(Request $request)
    {
        return RequisitionItems::where('req_id', $request->req_id)->get('items');
    }
}
