<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use App\Models\Product;
use App\Models\WareHouse;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\SaleReturn;
use App\Models\SaleReturnItem;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class SaleReturnController extends Controller
{
    public function AllSalesReturn() {
        $allData = SaleReturn::orderBy('id', 'desc')->get();
        return view('admin.backend.return-sale.all_return_sales', compact('allData'));
    }
    // End Method

     public function AddSalesReturn(){
        $customers = Customer::all();
        $warehouses = WareHouse::all();
        return view('admin.backend.return-sale.add_return_sales',compact('customers','warehouses'));
    }
     // End Method 

     public function StoreSalesReturn(Request $request){

        $request->validate([
            'date' => 'required|date',
            'status' => 'required', 
        ]);

    try {

        DB::beginTransaction();

        $grandTotal = 0;

        $sales = SaleReturn::create([
            'date' => $request->date,
            'warehouse_id' => $request->warehouse_id,
            'customer_id' => $request->customer_id,
            'discount' => $request->discount ?? 0,
            'shipping' => $request->shipping ?? 0,
            'status' => $request->status,
            'note' => $request->note,
            'grand_total' => 0,
            'paid_amount' => $request->paid_amount,
            'due_amount' => $request->due_amount, 

        ]);

        /// Store Sales Items & Update Stock 
    foreach($request->products as $productData){
        $product = Product::findOrFail($productData['id']);
        $netUnitCost = $productData['net_unit_cost'] ?? $product->price;

        if ($netUnitCost === null) {
            throw new \Exception("Net Unit cost is missing ofr the product id" . $productData['id']);
        }

        $subtotal = ($netUnitCost * $productData['quantity']) - ($productData['discount'] ?? 0);
        $grandTotal += $subtotal;

        SaleReturnItem::create([
            'sale_return_id' => $sales->id,
            'product_id' => $productData['id'],
            'net_unit_cost' => $netUnitCost,
            'stock' => $product->product_qty + $productData['quantity'],
            'quantity' => $productData['quantity'],
            'discount' => $productData['discount'] ?? 0,
            'subtotal' => $subtotal, 
        ]);

        $product->increment('product_qty', $productData['quantity']); 
    }

    $sales->update(['grand_total' => $grandTotal + $request->shipping - $request->discount]);

    DB::commit();

    $notification = array(
        'message' => 'Sales Return Stored Successfully',
        'alert-type' => 'success'
     ); 
     return redirect()->route('all.sale.return')->with($notification);  

    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json(['error' => $e->getMessage()], 500);
      } 
    }
    // End Method

    public function EditSalesReturn($id){
        $editData = SaleReturn::with('saleReturnItems.product')->findOrFail($id);
        $customers = Customer::all();
        $warehouses = WareHouse::all();
        return view('admin.backend.return-sale.edit_return_sales',compact('editData','customers','warehouses'));
    }
    // End Method 

    

    public function UpdateSalesReturn(Request $request, $id)
{
    $request->validate([
        'date' => 'required|date',
        'status' => 'required', 
    ]);

    $sales = SaleReturn::findOrFail($id);
    $sales->update([
        'date' => $request->date,
        'warehouse_id' => $request->warehouse_id,
        'customer_id' => $request->customer_id,
        'discount' => $request->discount ?? 0,
        'shipping' => $request->shipping ?? 0,
        'status' => $request->status,
        'note' => $request->note,
        'grand_total' => $request->grand_total,
        'paid_amount' => $request->paid_amount,
        'due_amount' => $request->due_amount,
        'full_paid' => $request->full_paid,   
    ]);

    // برگرداندن موجودی قبلی قبل از حذف آیتم‌ها
    $oldItems = SaleReturnItem::where('sale_return_id', $sales->id)->get();
    foreach ($oldItems as $old) {
        $product = Product::find($old->product_id);
        if ($product) {
            $product->product_qty -= $old->quantity; // کم کردن موجودی برگشتی قبلی
            $product->save();
        }
    }

    // حذف آیتم‌های قبلی
    SaleReturnItem::where('sale_return_id', $sales->id)->delete();

    // افزودن آیتم‌های جدید
    foreach ($request->products as $product_id => $product) {
        SaleReturnItem::create([
            'sale_return_id' => $sales->id,
            'product_id' => $product_id,
            'net_unit_cost' => $product['net_unit_cost'],
            'stock' => $product['stock'],
            'quantity' => $product['quantity'],
            'discount' => $product['discount'] ?? 0,
            'subtotal' => $product['subtotal'],  
        ]);

        // به‌روزرسانی موجودی با مقدار جدید
        $productModel = Product::find($product_id);
        if ($productModel) {
            $productModel->product_qty += $product['quantity'];
            $productModel->save();
        }  
    }

    $notification = array(
        'message' => 'Sale Return Updated Successfully',
        'alert-type' => 'success'
    ); 
    return redirect()->route('all.sale.return')->with($notification);  
}
    // End Method

    public function DetailsSalesReturn($id){
        $sales = SaleReturn::with(['customer','saleReturnItems.product'])->find($id);
        return view('admin.backend.return-sale.sales_return_details',compact('sales'));
    }
     // End Method

     public function DeleteSalesReturn($id){
        try {
          DB::beginTransaction();
          $sales = SaleReturn::findOrFail($id);
          $SalesItems = SaleReturnItem::where('sale_return_id',$id)->get();

          foreach($SalesItems as $item){
            $product = Product::find($item->product_id);
            if ($product) {
                $product->decrement('product_qty',$item->quantity);
            }
          }
          SaleReturnItem::where('sale_return_id',$id)->delete();
          $sales->delete();
          DB::commit();

          $notification = array(
            'message' => 'Sale Return Deleted Successfully',
            'alert-type' => 'success'
         ); 
         return redirect()->route('all.sale.return')->with($notification);  
            
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
          }  
    }
    // End Method

    public function InvoiceSalesReturn($id){
        $sales = SaleReturn::with(['customer','warehouse','saleReturnItems.product'])->find($id);

        $pdf = Pdf::loadView('admin.backend.return-sale.invoice_pdf',compact('sales'));
        return $pdf->download('return_sales_'.$id.'.pdf');
    }
    // End Method
}
