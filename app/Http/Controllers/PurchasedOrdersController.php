<?php

namespace App\Http\Controllers;

use App\Models\Items;
use App\Models\PurchasedOrderItems;
use App\Models\Suppliers;
use Illuminate\Http\Request;
use App\Models\SupplierItems;
use App\Models\PurchasedOrders;
use App\Models\Units;

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

                    $purchaseOrder['supplier_name'] = $supplier->company_name;
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
