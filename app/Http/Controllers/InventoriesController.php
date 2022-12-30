<?php

namespace App\Http\Controllers;

use App\Models\Inventories;
use App\Models\Items;
use App\Models\PurchasedOrderItems;
use App\Models\PurchasedOrders;
use App\Models\Units;
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

    public function destroy(Request $request)
    {
        $response['status'] = 500;
        if (Auth::user()->department <= 3) {
            Inventories::where('id', $request->inventory_id)->delete();
            $response['status'] =  200;
        }

        return response()->json($response);
    }

    public function index()
    {
        $response['items'] = Inventories::join('items', 'items.item_id', '=', 'inventories.item_id')
            ->join('units', 'units.unit_id', '=', 'inventories.unit_id')->orderBy('inventories.created_at', 'desc')->get(['items.item', 'items.item_id', 'units.unit_id', 'units.unit_name', 'inventories.*']);
        return response()->json($response);
    }

    public function submitForm(Request $request)
    {
        // dd($request->all());

        $request->validate([
            'qty' => ['required', 'min:0', 'numeric'],
            'price' => ['required', 'numeric']
        ]);

        if ($request->decision == null or $request->decision == 'false') {
            // For Creating New Item
            Inventories::create([
                'user_id' => Auth::user()->id,
                'item_id' => $request->item[0],
                'unit_id' => $request->unit[0],
                'stock' => $request->qty,
                'price' => $request->price
            ]);
        } else {
            // Updating Purchased Order Items prices
            $inventoryItem = Inventories::where('id', $request->inventory_select)->first();
            if ($inventoryItem->count()) {
                $orders = PurchasedOrders::where('status', 0)->get();
                foreach ($orders as $order) {
                    $items = PurchasedOrderItems::where('po_id', $order->id)->get();
                    foreach ($items as $item) {
                        if ($item->item_id == $inventoryItem->item_id && $item->unit_id == $inventoryItem->unit_id) {
                            PurchasedOrderItems::where('po_id', $item->po_id)->update([
                                'amount' => $item->qty * $request->price
                            ]);
                        }
                    }
                    $order_amount = PurchasedOrderItems::where('po_id', $order->id)->sum('amount');
                    PurchasedOrders::where('id', $order->id)->update(['order_amount' => $order_amount]);
                }

                Inventories::where('id', $request->inventory_select)->update([
                    'stock' => $request->qty,
                    'price' => $request->price
                ]);
            }
        }
        return back()->with('alert', 'Successful!');
    }

    public function add(Request $request)
    {
        $response['status'] = 500;

        if (strtoupper($request->option) == 'UNIT') {
            Units::create(['unit_name' => $request->value]);
            $response['status'] = 200;
        } else {
            Items::create(['item' => $request->value]);
            $response['status'] = 200;
        }

        return response()->json($response);
    }
}
