<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Items;
use App\Models\Units;
use App\Models\Suppliers;
use App\Models\SavedItems;
use App\Events\Requisition;
use App\Models\Requisitions;
use Illuminate\Http\Request;
use App\Models\SupplierItems;
use App\Models\PurchasedOrders;
use App\Models\RequisitionItems;
use App\Models\PurchasedOrderItems;
use App\Models\RequisitionNotification;
use Illuminate\Support\Facades\Auth;

class RequisitionController extends Controller
{
    public function apiIndex()
    {
        if (Auth::user()->department <= 3) {
            $requisitions = Requisitions::latest()->get('requisitions.*');

            foreach ($requisitions as $requisition) {
                if ($requisition->evaluator != null) {
                    $requisition['evaluator'] = User::join('departments', 'departments.id', '=', 'users.department')
                        ->where('users.id', $requisition->evaluator)->first(['departments.department', 'users.name']);
                }
            }

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
        $requisition = Requisitions::where('req_id', $request->req_id)->first();
        // $response['requisition'] = $requisition;
        // $response['department'] = User::where('id', $requisition[0]->user_id)->get('department');
        if ($requisition) {
            $response['items'] = RequisitionItems::join('items', 'items.item_id', '=', 'requisition_items.item_id')
                ->join('units', 'units.unit_id', '=', 'requisition_items.unit_id')
                ->where('req_id', $request->req_id)
                ->get([
                    'items.item',
                    'units.unit_name',
                    'requisition_items.qty'
                ]);

            if ($requisition->supplier) {
                $response['requisition'] = Requisitions::join('suppliers', 'suppliers.id', '=', 'requisitions.supplier')
                    ->join('users', 'users.id', '=', 'requisitions.user_id')
                    ->join('departments', 'departments.id', '=', 'users.department')
                    ->where('requisitions.req_id', $requisition->req_id)
                    ->get([
                        'suppliers.company_name',
                        'requisitions.*',
                        'departments.department'
                    ]);
            } else {
                $response['requisition'] = Requisitions::join('users', 'users.id', '=', 'requisitions.user_id')
                    ->join('departments', 'departments.id', '=', 'users.department')
                    ->where('requisitions.req_id', $requisition->req_id)
                    ->get([
                        'requisitions.*',
                        'departments.department'
                    ]);
            }
            $response['status'] = 200;
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
        ]);

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
        $request->validate([
            'requisition' => 'required',
            'address' => 'required',
            'supplier' => 'required',
            'decision' => 'required'
        ]);

        $requisition = Requisitions::where('req_id', $request->requisition)->first();
        $newStatus = '';

        if ($requisition) {
            if (strtoupper($request->decision) == 'REJECTED') {
                $newStatus = 'Rejected';
            } else {
                switch ($requisition->stage + 1) {
                    case 1:
                        $newStatus = 'For approval';
                        $formFields['supplier'] = $request->supplier;
                        $formFields['delivery_address'] = $request->address;
                        break;
                    case 2:
                        $newStatus = 'Releasing of voucher';
                        break;
                    case 3:
                        $newStatus = 'Approved';
                        $this->createOrder($requisition, $request->address);
                        break;
                }
            }

            $formFields['stage'] = $requisition->stage + 1;
            $formFields['status'] = $newStatus;
            $formFields['evaluator'] = Auth::user()->id;

            $update = Requisitions::where('req_id', $request->requisition)
                ->update($formFields);

            event(new Requisition(
                Auth::user()->name,
                'has ' . $request->decision . ' Requisition No.' . $request->requisition
            ));

            RequisitionNotification::create([
                'requisition_id' => $request->requisition,
                'user_id' => auth()->user()->id,
                'context' => 'has ' . $request->decision . ' Requisition No.' . $request->requisition
            ]);
        }

        if ($update) {
            return back()->with('sucess', 'Requisition has been updated!');
        } else {
            return back()->with('error', 'Updating failed, please try again later.');
        }
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

        $user = User::find(auth()->user()->id);
        event(new Requisition($user->name, 'submitted a new requisition'));

        RequisitionNotification::create([
            'requisition_id' => $req_id,
            'user_id' => auth()->user()->id,
            'context' => 'submitted a new requisition'
        ]);

        return $this->disposeSavedItems($user_id);
    }

    // Disposing the submitted Saved Items
    public function disposeSavedItems($userId)
    {
        $affectedRows = SavedItems::where('user_id', $userId)->delete();
        if ($affectedRows > 0) return 200;
        else return 433;
    }

    public function createOrder($requisition, $address)
    {
        $items = RequisitionItems::join('supplier_items', function ($join) {
            $join->on('requisition_items.item_id', '=', 'supplier_items.item_id')->on('requisition_items.unit_id', '=', 'supplier_items.unit_id');
        })->where('req_id', $requisition->req_id)->get(['supplier_items.item_id', 'supplier_items.unit_id', 'supplier_items.price', 'requisition_items.qty']);

        $order = PurchasedOrders::create([
            'supplier' => $requisition->supplier,
            'delivery_address' => $address,
            'req_id' => $requisition->req_id,
            'payment' => 'Due'
        ]);

        foreach ($items as $item) {
            PurchasedOrderItems::create([
                'po_id' => $order->id,
                'item_id' => $item->item_id,
                'unit_id' => $item->unit_id,
                'qty' => $item->qty,
                'amount' => $item->qty * $item->price
            ]);
        }

        PurchasedOrders::where('id', $order->id)->update(['order_amount' => PurchasedOrderItems::sum('amount')]);
    }
}
