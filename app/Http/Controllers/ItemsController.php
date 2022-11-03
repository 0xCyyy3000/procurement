<?php

namespace App\Http\Controllers;

use App\Models\Inventories;
use App\Models\Items;
use App\Models\Units;
use App\Models\SavedItems;
use Illuminate\Http\Request;

class ItemsController extends Controller
{
    public function index()
    {
        $data = Items::all();
        return response()->json($data);
    }

    public function select(Request $request)
    {
        $item = Items::where("item_id", $request->item_id)->get();
        return response()->json($item);
    }

    public function store(Request $request)
    {

        if ($request->isAdding == 'true') {
            Items::create([
                'item' => $request->item
            ]);

            $unit = Units::where('unit_id', $request->unit_id)->get('unit_id');
            $item = Items::select(['item_id', 'item'])->latest('item_id')->first();

            Inventories::create([
                'user_id' => auth()->user()->id,
                'item_id' => $item['item_id'],
                'unit_id' => $unit[0]->unit_id,
                'stock' => 0
            ]);

            $addedItem = SavedItems::create([
                'user_id' => auth()->id(),
                'item_id' => $item->item_id,
                'item' => Items::where('item_id', $item['item_id'])->get('item')[0]->item,
                'unit_id' => $unit[0]->unit_id,
                'qty' => $request->qty
            ]);

            $result['item'] = $addedItem;
        } else {
            $formFields = $request->validate([
                'item' => 'required',
                'unit_id' => 'required',
                'qty' => 'required | min:1',
                'item_id' => 'required'
            ]);

            $formFields['user_id'] = auth()->id();
            $addedItem = SavedItems::create($formFields);
            $result['item'] = SavedItems::select(['item_id', 'item'])->latest('row')->first()[0];
        }

        if ($addedItem->user_id) $result['status'] = 200;
        else $result['status'] = 500;

        return response()->json($result);
    }

    public function indexSavedItems()
    {
        $data = request()->user()->savedItems()->get();
        return response()->json($data);
    }

    public function selectSavedItems(Request $request)
    {
        $savedItem = SavedItems::where("row", $request->row_id)->get();
        return response()->json($savedItem);
    }

    public function fetchUnits()
    {
        $units = Units::get(['unit_id', 'unit_name']);
        return response()->json($units);
    }

    // public function storeAddedItem(Request $request)
    // {
    //     if ($request->isAdding == 'true') {
    //         Items::create([
    //             'item' => $request->item,
    //             'units' => $request->unit,
    //             'qty' => 0,
    //             'price' => 0,
    //             'worth' => 0
    //         ]);

    //         $result['newItem'] = Items::select('item_id')->latest('item_id')->first();
    //         // Items::where('item', $request->item)->latest('item_id')->first();
    //     }

    //     $formFields = $request->validate([
    //         'item' => 'required',
    //         'unit' => 'required',
    //         'qty' => 'required | min:1'
    //     ]);

    //     $formFields['user_id'] = auth()->id();
    //     $addedItem = SavedItems::create($formFields);

    //     if ($addedItem->user_id) $result['status'] = 200;
    //     else $result['status'] = 500;

    //     return response()->json($result);
    // }

    public function destroySavedItem(Request $request)
    {
        $deleted = SavedItems::where('row', $request->row)->delete();
        $items = SavedItems::where('user_id', auth()->user()->id)->get()->count();
        $result['items'] = $items;
        if ($deleted) $result['status'] = 200;
        else $result['deleted'] = 500;

        return response()->json($result);
    }

    public function clearSavedItem(Request $request)
    {
        $affectedRows = SavedItems::where('user_id', $request->user_id)->delete();
        $result['rows'] = $affectedRows;
        if ($affectedRows) $result['status'] = 200;
        else $result['status'] = 500;

        return response()->json($result);
    }

    public function updateSavedItem(Request $request)
    {
        $formFields = $request->validate([
            'item' => 'required',
            'unit_id' => 'required',
            'qty' => 'required | min:1',
            'item_id' => 'required'
        ]);

        $updatedRow = SavedItems::where('row', $request->row)
            ->where('user_id', auth()->id())->update($formFields);

        if ($updatedRow)
            $result['update_status'] = 200;

        else
            $result['update_status'] = 403;

        return response()->json($result);
    }
}
