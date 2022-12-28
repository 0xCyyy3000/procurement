<?php

namespace App\Http\Controllers;

use App\Models\Suppliers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SupplierController extends Controller
{
    public function select(Request $request)
    {
        $response['supplier'] = Suppliers::where('id', $request->supplier_id)->first();
        return response()->json($response);
    }

    public function update(Request $request)
    {
        if (Auth::user()->department <= 3) {
            $updated = Suppliers::where('id', $request->supplier_id)->update([
                'company_name' => $request->company_name,
                'contact_person' => $request->contact_person,
                'email' => $request->email,
                'phone' => $request->phone,
                'address' => $request->address
            ]);
        }

        if ($updated != null)
            return response()->json(['status' => 200]);
    }

    public function destroy(Request $request)
    {
        if (Auth::user()->department <= 3) {
            Suppliers::where('id', $request->supplier_id)->delete();
            return response()->json(['status' => 200]);
        }
        return response()->json(['status' => 500]);
    }

    public function create(Request $request)
    {
        if (Auth::user()->department > 3) {
            return abort(403, 'Action Restricted!');
        }

        $request->validate([
            'supplier' => 'required',
            'email' => ['required', 'email'],
            'address' => 'required',
            'contact_person' => 'required',
            'phone' => ['required', 'digit:11', 'numeric'],
        ]);

        Suppliers::create([
            'company_name' => $request->supplier,
            'email' => $request->email,
            'contact_person' => $request->contact_person,
            'phone' => $request->phone,
            'address' => $request->address,

        ]);

        return redirect()->back()->with('alert', 'Added Successfully!');
    }
}
