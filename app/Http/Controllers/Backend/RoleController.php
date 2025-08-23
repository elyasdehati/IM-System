<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    public function AllPermission() {
        $permission = Permission::all();
        return view('admin.backend.pages.permission.all_permission', compact('permission'));
    }
    // End Method

    public function AddPermission() {
        return view('admin.backend.pages.permission.add_permission');
    }
    // End Method

    public function StorePermission(Request $request){
        Permission::create([
            'name' => $request->name,
            'group_name' => $request->group_name
            ]);

            $notification = array(
            'message' => 'Permission Inserted Successfully',
            'alert-type' => 'success'
        );
            return redirect()->route('all.permission')->with($notification);
    }
    // End Method
}
