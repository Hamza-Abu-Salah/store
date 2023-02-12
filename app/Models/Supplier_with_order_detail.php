<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier_with_order_detail extends Model
//
{
    use HasFactory;

    protected $table = "supplier_with_order_details";
    protected $fillable = [
        'suppliers_with_orders_auto_serial', 'order_type', 'come_code', 'deliverd_quantity', 'inMain_or_retail_uom',
        'uom_id', 'isparentuom', 'unit_price', 'total_price', 'order_date', 'added_by',
        'created_at', 'updated_by', 'updated_at', 'item_code', 'batch_auto_serial', 'production_date', 'expire_date', 'item_card_type', 'approved_by'
    ];
}
