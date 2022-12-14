<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Items;
use App\Models\Units;
use App\Models\Suppliers;
use App\Events\Requisition;
use App\Models\CollectedItem;
use App\Models\DeliveryAddress;
use App\Models\Inventories;
use App\Models\Requisitions;
use Illuminate\Http\Request;
use App\Models\SupplierItems;
use App\Models\PurchasedOrders;
use App\Models\PurchasedOrderItems;
use Illuminate\Support\Facades\Auth;

class PurchasedOrdersController extends Controller
{

    public function apiIndex()
    {
        $response['orders'] = PurchasedOrders::all();
        $response['suppliers'] = Suppliers::all();
        $response['addresses'] = DeliveryAddress::all();

        return response()->json($response);
    }

    public function select(Request $request)
    {
        $purchaseOrder = PurchasedOrders::find($request->po_id);

        $inventoryItems = Inventories::all();
        $orderedItems = PurchasedOrderItems::where('po_id', $request->po_id)->get();

        foreach ($orderedItems as $item) {
            foreach ($inventoryItems as $inventory) {
                if ($item->item_id == $inventory->item_id && $item->unit_id == $inventory->unit_id) {
                    $item['price'] = $inventory->price;
                    $item['total'] = $inventory->price * $item->qty;
                    $item['unit_name'] = Units::where('unit_id', $item->unit_id)->get('unit_name')[0]->unit_name;
                    $item['item_name'] = Items::where('item_id', $item->item_id)->get('item')[0]->item;
                }
            }
        }

        $supplier = Suppliers::find($purchaseOrder->supplier);

        $purchaseOrder['supplier_name'] = $supplier->company_name;
        $purchaseOrder['supplier_address'] = $supplier->address;
        $purchaseOrder['contact_name'] = $supplier->contact_person;
        $purchaseOrder['contact_email'] = $supplier->email;
        $purchaseOrder['contact_phone'] = $supplier->phone;

        $purchaseOrder['delivery_address'] = PurchasedOrders::join('delivery_addresses', 'delivery_addresses.id', '=', 'purchased_orders.delivery_address')
            ->where('purchased_orders.id', $purchaseOrder->id)
            ->first('delivery_addresses.address');

        $purchaseOrder['maker'] = Requisitions::join('users', 'users.id', '=', 'requisitions.user_id')
            ->join('departments', 'departments.id', '=', 'users.department')
            ->where('requisitions.req_id', $purchaseOrder->req_id)->first(['users.name', 'departments.department']);

        $purchaseOrder['evaluator'] = Requisitions::join('users', 'users.id', '=', 'requisitions.evaluator')
            ->join('departments', 'departments.id', '=', 'users.department')
            ->where('requisitions.req_id', $purchaseOrder->req_id)->first(['users.name', 'departments.department']);


        // $supplierItems = Inventories::where('supplier_id', $purchaseOrder->supplier)
        //     ->get(['item_id', 'unit_id', 'price']);

        // foreach ($orderedItems as $orderedItem) {
        //     foreach ($supplierItems as $supplierItem) {
        //         if ($supplierItem->item_id == $orderedItem->item_id and $supplierItem->unit_id == $orderedItem->unit_id) {
        //             $orderedItem['price'] = $supplierItem->price;
        //             $orderedItem['total'] = $supplierItem->price * $orderedItem->qty;
        //             $orderedItem['unit_name'] = Units::where('unit_id', $orderedItem->unit_id)->get('unit_name')[0]->unit_name;
        //             $orderedItem['item_name'] = Items::where('item_id', $orderedItem->item_id)->get('item')[0]->item;

        //             $purchaseOrder['delivery_address'] = PurchasedOrders::join('delivery_addresses', 'delivery_addresses.id', '=', 'purchased_orders.delivery_address')
        //                 ->where('purchased_orders.id', $purchaseOrder->id)
        //                 ->first('delivery_addresses.address');

        //             $purchaseOrder['maker'] = Requisitions::join('users', 'users.id', '=', 'requisitions.user_id')
        //                 ->join('departments', 'departments.id', '=', 'users.department')
        //                 ->where('requisitions.req_id', $purchaseOrder->req_id)->first(['users.name', 'departments.department']);

        //             $purchaseOrder['evaluator'] = Requisitions::join('users', 'users.id', '=', 'requisitions.evaluator')
        //                 ->join('departments', 'departments.id', '=', 'users.department')
        //                 ->where('requisitions.req_id', $purchaseOrder->req_id)->first(['users.name', 'departments.department']);

        //             $purchaseOrder['supplier_name'] = $supplier->company_name;
        //             $purchaseOrder['supplier_address'] = $supplier->address;
        //             $purchaseOrder['contact_name'] = $supplier->contact_person;
        //             $purchaseOrder['contact_email'] = $supplier->email;
        //             $purchaseOrder['contact_phone'] = $supplier->phone;
        //         }
        //     }
        // }
        return [
            'purchaseOrder' => $purchaseOrder,
            'orderedItems' => $orderedItems
        ];
    }

    public function update(Request $request)
    {
        $updatedOrder = PurchasedOrders::where('id', $request->po_id)
            ->update([
                'payment' => $request->payment,
                'status' => $request->status,
                'delivery_address' => $request->address,
                'supplier' => $request->supplier
            ]);

        if ($updatedOrder) {
            $response['status'] = 200;
            $response['message'] = 'Updated successfully!';
        } else {
            $response['status'] = 400;
            $response['message'] = 'There was an error, please try again.';
        }

        return response()->json($response);
    }

    public function selectItems(Request $request)
    {
        $orderedItems = PurchasedOrderItems::join('items', 'items.item_id', '=', 'purchased_order_items.item_id')
            ->join('units', 'units.unit_id', '=', 'purchased_order_items.unit_id')->where('purchased_order_items.po_id', $request->po_id)
            ->get(['items.item', 'items.item_id', 'units.unit_id', 'units.unit_name', 'purchased_order_items.qty', 'purchased_order_items.id as order_item_id']);

        $order = PurchasedOrders::where('id', $request->po_id)->first();
        if ($orderedItems->count()) {
            if ($order->collected == InventoriesController::COLLECTED) {
                foreach ($orderedItems as $orderedItem) {
                    $collectedItem = CollectedItem::where('purchased_order_item_id', $orderedItem->order_item_id)->first();
                    $item = Inventories::where('item_id', $orderedItem->item_id)->where('unit_id', $orderedItem->unit_id)->get();
                    if ($collectedItem && $item->count()) {
                        $orderedItem->qty -= $collectedItem->collected;
                    }
                }
            }

            return response()->json(
                [
                    'status' => 200,
                    'items' => $orderedItems
                ]
            );
        }

        return response()->json(['status' => 400]);
    }
}
