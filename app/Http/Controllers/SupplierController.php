<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function suppliers(){
        $suppliers = Supplier::get();

        return view('modules.supplier.get', compact(['suppliers']));
    }

    public function edit($id){
        $supplier = Supplier::find($id);

        return view('modules.supplier.add', compact(['supplier']));
    }

    public function update(Request $request, $id){
        Supplier::where('id', $id)->update([
            'name' => $request->name,
            'supplier_code' => $request->supplier_code,
            'telephone' => $request->telephone
        ]);

        return redirect()->back()->with('message', 'Supplier has been updated.');
    }
}
