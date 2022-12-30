<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Inventories;
use App\Models\Distribution;
use Illuminate\Http\Request;
use App\Models\DeliveryAddress;
use App\Models\DistributionItem;

class DistributionController extends Controller
{
    public function updateAddress(Request $request)
    {
        if ($request->address == null) {
            $request->validate(['address_input' => 'required']);
            DeliveryAddress::create(['address' => $request->address_input]);
            return back()->with('alert', 'Address has been added!');
        } else {
            $request->validate([
                'address' => 'required',
                'address_input' => 'required'
            ]);

            DeliveryAddress::where('id', $request->address)->update([
                'address' => $request->address_input
            ]);

            return back()->with('alert', 'Changes has been saved!');
        }
    }

    public function create(Request $request)
    {
        $recentDistribution = Distribution::create([
            'recipient' => $request->recipient,
            'address' => $request->destination
        ]);

        foreach ($request->items as $item) {
            DistributionItem::create([
                'distribution_id' => $recentDistribution->id,
                'inventory_id' => $item['id'],
                'item_id' => $item['item_id'],
                'unit_id' => $item['unit_id'],
                'qty' => $item['stock']
            ]);

            $inventoryItem = Inventories::where('id', $item['id'])->first();
            Inventories::where('id', $item['id'])->update(['stock' => $inventoryItem->stock - $item['stock']]);
        }

        return response()->json(200);
    }

    public function select(Request $request)
    {
        $response['recipient'] = User::join('distributions', 'distributions.recipient', '=', 'users.id')
            ->join('departments', 'departments.id', '=', 'users.department')
            ->join('delivery_addresses', 'delivery_addresses.id', '=', 'distributions.address')
            ->where('distributions.id', $request->distribution_id)->get(['users.email', 'users.name', 'departments.*', 'delivery_addresses.*']);

        $response['distribution'] = Distribution::where('id', $request->distribution_id)->get();

        $response['items'] = DistributionItem::join('items', 'items.item_id', '=', 'distribution_items.item_id')
            ->join('units', 'units.unit_id', '=', 'distribution_items.unit_id')->where('distribution_id', $request->distribution_id)
            ->get(['items.*', 'units.*', 'distribution_items.*']);

        return response()->json($response);
    }

    public function edit(Request $request)
    {
        $response['recipient'] = User::join('distributions', 'distributions.recipient', '=', 'users.id')
            ->join('departments', 'departments.id', '=', 'users.department')
            ->join('delivery_addresses', 'delivery_addresses.id', '=', 'distributions.address')
            ->where('distributions.id', $request->distribution_id)
            ->get([
                'users.id as user_id', 'users.name',
                'departments.department',
                'delivery_addresses.id as address_id', 'delivery_addresses.address'
            ]);

        $response['distribution'] = Distribution::where('id', $request->distribution_id)->get();

        $response['items'] = DistributionItem::join('items', 'items.item_id', '=', 'distribution_items.item_id')
            ->join('units', 'units.unit_id', '=', 'distribution_items.unit_id')->where('distribution_id', $request->distribution_id)
            ->get(['items.*', 'units.*', 'distribution_items.*']);

        $response['inventoryItems'] = Inventories::join('items', 'items.item_id', '=', 'inventories.item_id')
            ->join('units', 'units.unit_id', '=', 'inventories.unit_id')->get(['items.*', 'units.*', 'inventories.id', 'inventories.stock']);

        return response()->json($response);
    }

    public function update(Request $request)
    {
        // dd($request->all());
        $status = 500;

        foreach ($request->inventory_items as $invItem) {
            $updated = Inventories::where('id', $invItem['inventory_id'])->update(['stock' => $invItem['qty']]);
            ($updated) ? $status = 200 : $status = 500;
        }

        DistributionItem::where('distribution_id', $request['distribution']['id'])->delete();
        foreach ($request->items as $item) {
            $created = DistributionItem::create([
                'distribution_id' => $request['distribution']['id'],
                'inventory_id' => $item['inventory_id'],
                'item_id' => $item['item_id'],
                'unit_id' => $item['unit_id'],
                'qty' => $item['qty']
            ]);

            ($created) ? $status = 200 : $status = 500;
        }

        Distribution::where('id', $request['distribution']['id'])->update([
            'recipient' => $request['distribution']['user_id'],
            'address' => $request['distribution']['address_id']
        ]);

        return response()->json($status);
    }
}
