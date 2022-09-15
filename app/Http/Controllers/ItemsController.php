<?php

namespace App\Http\Controllers;

use App\Models\Items;
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
                'item' => $request->item,
                'units' => $request->unit,
                'qty' => 0,
                'price' => 0,
                'worth' => 0
            ]);

            $newItem = Items::select('item_id')->latest('item_id')->first();
            $result['newItem'] = $newItem;

            $itemId = $newItem->item_id;

            $addedItem = SavedItems::create([
                'user_id' => auth()->id(),
                'item_id' => $itemId,
                'item' => $request->item,
                'unit' => $request->unit,
                'qty' => $request->qty
            ]);
        } else {

            $formFields = $request->validate([
                'item' => 'required',
                'unit' => 'required',
                'qty' => 'required | min:1',
                'item_id' => 'required'
            ]);

            $formFields['user_id'] = auth()->id();
            $addedItem = SavedItems::create($formFields);
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

    public function fetchUnits(Request $request)
    {
        $units = Items::where("item_id", $request->item_id)->get('units');
        return response()->json($units);
    }

    public function storeAddedItem(Request $request)
    {
        if ($request->isAdding == 'true') {
            Items::create([
                'item' => $request->item,
                'units' => $request->unit,
                'qty' => 0,
                'price' => 0,
                'worth' => 0
            ]);

            $result['newItem'] = Items::select('item_id')->latest('item_id')->first();
            // Items::where('item', $request->item)->latest('item_id')->first();
        }

        $formFields = $request->validate([
            'item' => 'required',
            'unit' => 'required',
            'qty' => 'required | min:1'
        ]);

        $formFields['user_id'] = auth()->id();
        $addedItem = SavedItems::create($formFields);

        if ($addedItem->user_id) $result['status'] = 200;
        else $result['status'] = 500;

        return response()->json($result);
    }

    public function destroyAddedItem(Request $request)
    {
        $deleted = SavedItems::where('created_at', $request->createdAt)->delete();
        if ($deleted) $result['status'] = 200;
        else $result['deleted'] = 500;

        return response()->json($result);
    }

    public function update(Request $request)
    {
        $formFields = $request->validate([
            'item' => 'required',
            'unit' => 'required',
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
