<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    protected $guarded = [];

    public function customer(){
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function warehouse(){
        return $this->belongsTo(warehouse::class, 'warehouse_id');
    }

    public function saleItem(){
        return $this->hasMany(SaleItem::class, 'sale_id');
    }
}
