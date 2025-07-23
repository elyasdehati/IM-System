<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function AllSupplier(){
        $supplier = Supplier::latest()->get();
        return view('admin.backend.supplier.all_supplier', compact('supplier'));
    }
    // End Method

    public function AddSupplier(){
        return view('admin.backend.supplier.add_supplier');
    }
    // End Method

    public function StoreSupplier(Request $request) {
        Supplier::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
        ]);

        $notification = array(
            'message' => 'Supplier Inserted Successfully',
            'alert-type' => 'success'
        );
            return redirect()->route('all.supplier')->with($notification);

    }
    // End method

    public function EditSupplier($id){
        $supplier = Supplier::find($id);
        return view('admin.backend.supplier.edit_supplier', compact('supplier'));
    }
    // End Method

    public function UpdateSupplier(Request $request) {
        $sup_id = $request->id;
        Supplier::find($sup_id)->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
        ]);

        $notification = array(
            'message' => 'Supplier Updated Successfully',
            'alert-type' => 'success'
        );
            return redirect()->route('all.supplier')->with($notification);

    }
    // End method

    public function DeleteSupplier($id) {
        Supplier::find($id)->delete();

        $notification = array(
            'message' => 'Supplier Deleted Successfully',
            'alert-type' => 'success'
        );
            return redirect()->back()->with($notification);
    }
    // End Method
}
