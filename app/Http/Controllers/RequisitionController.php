<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Items;
use App\Models\Units;
use App\Models\Suppliers;
use App\Models\SavedItems;
use App\Events\Requisition;
use App\Models\Inventories;
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
        if ($requisition) {
            $response['items'] = RequisitionItems::join('items', 'items.item_id', '=', 'requisition_items.item_id')
                ->join('units', 'units.unit_id', '=', 'requisition_items.unit_id')
                ->where('req_id', $request->req_id)
                ->get([
                    'items.item',
                    'units.unit_name',
                    'requisition_items.qty'
                ]);

            $response['evaluator'] = Requisitions::join('users', 'users.id', '=', 'requisitions.evaluator')
                ->join('departments', 'departments.id', '=', 'users.department')
                ->get(['users.name', 'users.email', 'departments.department']);
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
            Requisitions::where('req_id', $created->id)->delete();
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
                $formFields['stage'] = -1;
            } else {
                $formFields['stage'] = $requisition->stage + 1;
                switch ($requisition->stage + 1) {
                    case 1:
                        $newStatus = 'For approval';
                        $formFields['supplier'] = $request->supplier;
                        $formFields['delivery_address'] = $request->address;
                        $callback = $this->callback($requisition);
                        if ($callback == 500) {
                            return back()->with(
                                'alert',
                                "Some items are do not have prices in the Inventory, please add prices to all items and try again."
                            );
                        }
                        break;
                    case 2:
                        $newStatus = 'Partially Approved';
                        break;
                    case 3:
                        $newStatus = 'Approved';
                        $this->createOrder($requisition, $request->address);
                        break;
                }
            }

            $formFields['status'] = $newStatus;
            $formFields['evaluator'] = Auth::user()->id;

            $update = Requisitions::where('req_id', $request->requisition)
                ->update($formFields);

            event(new Requisition(
                $requisition->user_id,
                $requisition->maker,
                $request->decision . ' Requisition No.' . $request->requisition,
                ['name' => Auth::user()->name, 'id' => Auth::user()->id],
                'UPDATE REQ'
            ));

            RequisitionNotification::create([
                'requisition_id' => $request->requisition,
                'user_id' => Auth::user()->id,
                'context' => $request->decision . ' Requisition No.' . $request->requisition
            ]);
        }

        if ($update) {
            if ($newStatus == 'Approved') {
                return back()->with('alert', 'Requisition has been approved! Purchased Order has been created.');
            }
            return back()->with('alert', 'have updated a Requistion successfully!');
        } else {
            return back()->with('alert', 'Updating failed, please try again later.');
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
        if ($savedItems->count()) {
            return $this->submitItems($savedItems, $user_id, $req_id);
        } else return 500;
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
        event(new Requisition($user->id, $user->name, 'submitted a New Requisition.', null, 'CREATE REQ'));

        RequisitionNotification::create([
            'requisition_id' => $req_id,
            'user_id' => $user->id,
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
        $order = PurchasedOrders::create([
            'supplier' => $requisition->supplier,
            'delivery_address' => $address,
            'req_id' => $requisition->req_id,
            'payment' => 'Due'
        ]);

        $reqItems = RequisitionItems::where('req_id', $requisition->req_id)->get();
        $inventoryItems = Inventories::all();

        foreach ($reqItems as $item) {
            foreach ($inventoryItems as $inventory) {
                if ($item->item_id == $inventory->item_id && $item->unit_id == $inventory->unit_id) {
                    PurchasedOrderItems::create([
                        'po_id' => $order->id,
                        'item_id' => $item->item_id,
                        'unit_id' => $item->unit_id,
                        'qty' => $item->qty,
                        'amount' => $item->qty * $inventory->price
                    ]);
                    break;
                }
            }
        }

        $orderAmount = PurchasedOrderItems::where('po_id', $order->id)->sum('amount');
        PurchasedOrders::where('id', $order->id)->update(['order_amount' => $orderAmount]);

        // if ($transferred != null) {
        //     return true;
        // } else {
        //     PurchasedOrderItems::where('po_id', $order->id)->delete();
        //     PurchasedOrders::where('id', $order->id)->delete();
        //     return false;
        // }
    }

    public function callback($requisition)
    {
        $reqItems = RequisitionItems::where('req_id', $requisition->req_id)->get();
        $inventoryItems = Inventories::get();

        $response = 200;

        foreach ($reqItems as $item) {
            foreach ($inventoryItems as $invItem) {
                if ($item->item_id == $invItem->item_id and $item->unit_id == $invItem->unit_id) {
                    $invItem->price <= 0 ? $response = 500 : '';
                    break;
                }
            }
        }

        return $response;
    }
}
