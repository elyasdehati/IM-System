<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\WareHouse;
use Illuminate\Http\Request;

class WareHouseController extends Controller
{
    public function AllWarehouse(){
        $warehouse = WareHouse::latest()->get();
        return view('admin.backend.warehouse.all_warehouse', compact('warehouse'));
    }
    // End Method

    public function AddWarehouse(){
        return view('admin.backend.warehouse.add_warehouse');
    }
    // End Method

    public function StoreWareHouse(Request $request) {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:ware_houses,email|max:255',
            'phone' => 'nullable|string|max:20',
            'city' => 'nullable|string|max:255'
        ]);

        WareHouse::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'city' => $validated['city'],
        ]);

        $notification = array(
            'message' => 'WareHouse Inserted Successfully',
            'alert-type' => 'success'
        );
            return redirect()->route('all.warehouse')->with($notification);

    }
    // End method

    public function EditWarehouse($id){
        $warehouse = WareHouse::find($id);
        return view('admin.backend.warehouse.edit_warehouse', compact('warehouse'));
    }
    // End Method

    public function UpdateWarehouse(Request $request) {

        $warehouse = $request->id;
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:ware_houses,email|max:255',
            'phone' => 'nullable|string|max:20',
            'city' => 'nullable|string|max:255'
        ]);

        WareHouse::find($warehouse)->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'city' => $validated['city'],
        ]);

        $notification = array(
            'message' => 'WareHouse Updated Successfully',
            'alert-type' => 'success'
        );
            return redirect()->route('all.warehouse')->with($notification);

    }
    // End method

    public function DeleteWarehouse($id) {
        WareHouse::find($id)->delete();
        $notification = array(
            'message' => 'WareHouse Deleted Successfully',
            'alert-type' => 'success'
        );
            return redirect()->back()->with($notification);
    }
    // End Method
}
