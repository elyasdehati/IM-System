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
use App\Models\Transfer;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class TransferConrtoller extends Controller
{
    public function Transfer() {
        $allData = Transfer::with(['transferItems.product'])->orderBy('id', 'desc')->get();
        return view('admin.backend.transfer.all_transfer', compact('allData'));
    }
    // End Method
}
