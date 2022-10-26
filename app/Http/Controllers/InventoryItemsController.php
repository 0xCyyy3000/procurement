<?php

namespace App\Http\Controllers;

use App\Models\InventoryItems;
use Illuminate\Http\Request;

class InventoryItemsController extends Controller
{
    public function store(Request $request)
    {
        return InventoryItems::create($request);
    }
}
