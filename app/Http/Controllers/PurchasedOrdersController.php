<?php

namespace App\Http\Controllers;

use App\Models\Items;
use App\Models\PurchasedOrderItems;
use App\Models\Suppliers;
use Illuminate\Http\Request;
use App\Models\SupplierItems;
use App\Models\PurchasedOrders;
use App\Models\Requisitions;
use App\Models\Units;
use App\Models\User;

class PurchasedOrdersController extends Controller
{
    public function select(Request $request)
    {
        $purchaseOrder = PurchasedOrders::find($request->po_id);
        $orderedItems = PurchasedOrderItems::where('po_id', $request->po_id)->get();

        $supplier = Suppliers::find($purchaseOrder->supplier);
        $supplierItems = SupplierItems::where('supplier_id', $purchaseOrder->supplier)
            ->get(['item_id', 'unit_id', 'price']);

        foreach ($orderedItems as $orderedItem) {
            foreach ($supplierItems as $supplierItem) {
                if ($supplierItem->item_id == $orderedItem->item_id and $supplierItem->unit_id == $orderedItem->unit_id) {
                    $orderedItem['price'] = $supplierItem->price;
                    $orderedItem['total'] = $supplierItem->price * $orderedItem->qty;
                    $orderedItem['unit_name'] = Units::where('unit_id', $orderedItem->unit_id)->get('unit_name')[0]->unit_name;
                    $orderedItem['item_name'] = Items::where('item_id', $orderedItem->item_id)->get('item')[0]->item;

                    $userId = Requisitions::where('req_id', $purchaseOrder->req_id)->get('user_id')[0]->user_id;
                    $purchaseOrder['maker'] = User::find($userId);

                    $purchaseOrder['evaluator'] = Requisitions::where('req_id', $purchaseOrder->req_id)->get('evaluator')[0]->evaluator;

                    $purchaseOrder['supplier_name'] = $supplier->company_name;
                    $purchaseOrder['supplier_address'] = $supplier->address;
                    $purchaseOrder['contact_name'] = $supplier->contact_person['name'];
                    $purchaseOrder['contact_email'] = $supplier->contact_person['email'];
                    $purchaseOrder['contact_phone'] = $supplier->contact_person['phone'];
                }
            }
        }
        return [
            'purchaseOrder' => $purchaseOrder,
            'orderedItems' => $orderedItems
        ];
    }
}
