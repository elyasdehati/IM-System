<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Supplier;
use App\Models\WareHouse;
use App\Models\Sale;
use App\Models\SaleItem;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class SaleController extends Controller
{
    public function AllSales() {
        $allData = Sale::orderBy('id', 'desc')->get();
        return view('admin.backend.sales.all_sales', compact('allData'));
    }
    // End Method

    public function AddSales(){
        $customers = Customer::all();
        $warehouses = WareHouse::all();
        return view('admin.backend.sales.add_sales', compact('customers','warehouses'));
    }
    // End Method

    public function StoreSales(Request $request) {

        $request->validate([
            'date' => 'required|date',
            'status' => 'required',
            'customer_id' => 'required',
        ]);

        try {
            DB::beginTransaction();

            $grandTotal = 0;

            $sales = Sale::create([
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

            // Store Sales Items & Update Stock
            foreach ($request->products as $productData) {
                $product = Product::findOrFail($productData['id']);
                $netUnitCost = $productData['net_unit_cost'] ?? $product->price;

                if ($netUnitCost === null) {
                    throw new \Exception("Net Unit Cost is missing after the product id" . $productData['id']);
                }

                $subtotal = ($netUnitCost * $productData['quantity']) - ($productData['discount'] ?? 0);
                $grandTotal += $subtotal;

                SaleItem::create([
                    'sale_id' => $sales->id,
                    'product_id' => $productData['id'],
                    'net_unit_cost' => $netUnitCost,
                    'stock' => $product->product_qty + $productData['quantity'],
                    'quantity' => $productData['quantity'],
                    'discount' => $productData['discount'] ?? 0,
                    'subtotal' => $subtotal,
                ]);

                $product->decrement('product_qty', $productData['quantity']);
            }

            $sales->update(['grand_total' => $grandTotal + $request->shipping - $request->discount]);

            DB::commit();

            $notification = array(
            'message' => 'Sales Stored Successfully',
            'alert-type' => 'success'
        );
            return redirect()->route('all.sale')->with($notification);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }

    }
    // End Method
}
