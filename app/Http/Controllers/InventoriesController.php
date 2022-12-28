<?php

namespace App\Http\Controllers;

use App\Models\Inventories;
use App\Models\PurchasedOrderItems;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InventoriesController extends Controller
{
    public function store(Request $request)
    {
        dd($request->all());
    }

    public function receive(Request $request)
    {
        // dd($request->all());
        foreach ($request->items as $item) {
            $existing = Inventories::where('item_id', $item['item_id'])->where('unit_id', $item['unit_id'])
                ->where('user_id', '<=', 3)->first();

            if ($existing) {
                Inventories::where('item_id', $item['item_id'])->update([
                    'stock' => $existing->stock + $item['qty']
                ]);
            } else {
                Inventories::create([
                    'user_id' => Auth::user()->id,
                    'item_id' => $item['item_id'],
                    'unit_id' => $item['unit_id'],
                    'stock' => $item['qty']
                ]);
            }


            # Removing Item Qtys when it has been collected to the inventory

            // $currentQty = PurchasedOrderItems::where('po_id', $request->po_id)->where('item_id', $item['item_id'])
            //     ->where('unit_id', $item['unit_id'])->first('qty');

            // PurchasedOrderItems::where('po_id', $request->po_id)->where('item_id', $item['item_id'])
            //     ->where('unit_id', $item['unit_id'])->update([
            //         'qty' => doubleVal($currentQty->qty) - doubleval($item['qty'])
            //     ]);
        }

        return response()->json(['status' => 200]);
    }
}
