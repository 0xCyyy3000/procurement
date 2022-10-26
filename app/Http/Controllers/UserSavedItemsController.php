<?php

namespace App\Http\Controllers;

use App\Models\UserSavedItems;
use Illuminate\Http\Request;

class UserSavedItemsController extends Controller
{
    public function index(Request $request)
    {
        return UserSavedItems::where('id', $request->row)->get();
    }

    public function update(Request $request) //, UserSavedItems $userSavedItems)
    {
        // if (auth()->user()->id != $userSavedItems->user_id) {
        //     return 'Unauthorized';
        // }

        $savedItems = UserSavedItems::where('id', $request->row)->get('items');
        $items = array();

        foreach ($savedItems[0]->items as $item) {
            if ($item['item_id'] == $request->id) {
                $item['item_name'] = $request->item;
                $item['item_unit'] = $request->unit;
                $item['item_qty'] = $request->qty;
            }
            array_push($items, $item);
        }

        return UserSavedItems::where('id', $request->row)
            ->update(['items' => $items]);
    }

    public function select(Request $request)
    {
        $savedItems = UserSavedItems::where('id', $request->row)->get('items');
        $selectedItem = array();

        foreach ($savedItems[0]->items as $item) {
            if ($item['item_id'] == $request->item)
                array_push($selectedItem, $item);
        }

        return $selectedItem;
    }

    public function removeItem(Request $request)
    {
        $savedItems = UserSavedItems::where('id', $request->row)->get('items');
        $newItems = array();

        foreach ($savedItems[0]->items as $item) {
            if ($item['item_id'] != $request->item_id) {
                array_push($newItems, $item);
            }
        }

        return UserSavedItems::where('id', $request->row)
            ->update(['items' => $newItems]);
    }

    public function destroy(Request $request)
    {
        return UserSavedItems::destroy($request->row);
    }

    public function store(Request $request)
    {
        $formFields['user_id'] = $request->user_id;
        $formFields['items'] = [
            [
                'item_id' => $request->item_id,
                'item_name' => $request->item_name,
                'item_unit' => $request->item_unit,
                'item_qty' => $request->item_qty
            ]
        ];

        return UserSavedItems::create($formFields);
    }

    public function add(Request $request)
    {
        $savedItems = UserSavedItems::where('id', $request->row)->get('items');

        $items = $savedItems[0]->items;
        $item = [
            'item_id' => $request->item_id,
            'item_name' => $request->item_name,
            'item_unit' => $request->item_unit,
            'item_qty' => $request->item_qty,
        ];

        array_push($items, $item);

        return UserSavedItems::where('id', $request->row)
            ->update(['items' => $items]);
    }
}
