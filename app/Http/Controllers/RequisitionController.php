<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Items;
use App\Models\Units;
use App\Models\SavedItems;
use App\Models\Requisitions;
use Illuminate\Http\Request;
use App\Models\PurchasedOrders;
use App\Models\RequisitionItems;
use App\Models\Suppliers;

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
        $response['department'] = User::where('id', $requisition[0]->user_id)->get('department');

        if ($requisition) {
            $response['status'] = 200;

            $requisitionItems = RequisitionItems::where('req_id', $request->req_id)->get();
            $response['requisitionItems'] = $requisitionItems;

            $availableItems = Items::get();
            $response['items'] = $availableItems;

            $units = Units::get();
            $response['units'] = $units;
            $response['supplier'] = Suppliers::where('id', $requisition[0]->supplier)->get('company_name');
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
        $formFields['supplier'] = null;
        $formFields['released'] = false;

        $created = Requisitions::create($formFields);

        if ($created) {
            $reqId = Requisitions::select('req_id')->latest('req_id')->first();
            $result['status'] = $this->indexSavedItems($request->user_id, $reqId['req_id']);
        }

        if ($result['status'] != 200) {
            Requisitions::find($reqId)->delete();
        }

        return response()->json($result);
    }

    public function update(Request $request)
    {
        $formFields = $request->validate([
            'req_id' => 'required',
            'signatories' => 'required'
        ]);

        $requisition = Requisitions::where('req_id', $request->req_id)->get();
        $approvalCount = $requisition[0]->approval_count;
        $signatories = $requisition[0]->signatories;

        foreach ($signatories as $signatory) {
            if (strtoupper($request->signatories) == 'BOTH' || $signatory->name == $request->signatories) {
                $request->supplier == 'default' ? $supplier = $requisition[0]->supplier : $supplier = $request->supplier;

                if ($approvalCount == 0 and strtoupper($request->approval) == 'APPROVED')
                    $reqStatus = 'Partially Approved';
                else if ($approvalCount >= 1 and strtoupper($request->approval) == 'APPROVED') {
                    $reqStatus = 'Approved';
                    $itemDetails = RequisitionItems::where('req_id', $request->req_id)
                        ->get(['item_id', 'unit_id']);

                    if ($request->supplier || $requisition[0]->supplier) {
                        $formFields['released'] = true;
                        PurchasedOrders::create([
                            'status' => 'Pending',
                            'supplier' => $supplier, // <- this should be dynamic, depending on the chocie of the admin
                            'delivery_address' => 'ACLC Tacloban Real Street Tacloban City', // <- Must be dynamic
                            'req_id' => $request->req_id,
                            'payment' => 'Due',
                            'order_total' => 15899
                        ]);
                    }
                } else
                    $reqStatus = 'Rejected';

                $signatory->approval = $request->approval;
                $approvalCount++;
            }
        }

        $formFields['evaluator'] = auth()->user()->name;
        $formFields['approval_count'] = $approvalCount;
        $formFields['status'] = $reqStatus;
        $formFields['signatories'] = $signatories;
        $formFields['supplier'] = $supplier;

        $hasAffectedRows = Requisitions::where('req_id', $request->req_id)->update($formFields);

        if ($hasAffectedRows) $response['status'] = 200;
        else $response['status'] = 500;

        return response()->json($response);
    }

    public function copy(Request $request)
    {
        $itemsToCopy = RequisitionItems::where('req_id', $request->req_id)
            ->get(['item_id', 'unit_id', 'qty']);

        foreach ($itemsToCopy as $itemToCopy) {
            $created = SavedItems::create([
                'user_id' => auth()->user()->id,
                'item_id' => $itemToCopy->item_id,
                'item' => Items::where('item_id', $itemToCopy->item_id)->get('item')[0]->item,
                'unit_id' => $itemToCopy->unit_id,
                'qty' => $itemToCopy->qty
            ]);
        }

        if ($created) $response['status'] = 200;
        else $response['status'] = 500;

        return response()->json($response);
    }

    public function indexSavedItems($user_id, $req_id)
    {
        $savedItems = request()->user()->savedItems()->get();
        return $this->submitItems($savedItems, $user_id, $req_id);
    }

    public function submitItems($savedItems, $user_id, $req_id)
    {
        foreach ($savedItems as $item) {
            RequisitionItems::create([
                'req_id' => $req_id,
                'item_id' => $item->item_id,
                'unit_id' => $item->unit_id,
                'qty' => $item->qty
            ]);
        }

        return $this->disposeSavedItems($user_id);
    }

    // Disposing the submitted Saved Items
    public function disposeSavedItems($userId)
    {
        $affectedRows = SavedItems::where('user_id', $userId)->delete();
        if ($affectedRows > 0) return 200;
        else return 433;
    }
}
