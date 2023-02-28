<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inv_stores_transfer_details extends Model
{
    use HasFactory;
    protected $table="inv_stores_transfer_details";
    protected $fillable=[
        'inv_stores_transfer_id', 'inv_stores_transfer_auto_serial',
        'come_code', 'deliverd_quantity', 'uom_id', 'isparentuom', 'unit_price',
         'total_price', 'order_date','approved_at', 'added_by', 'created_at', 'updated_by',
         'updated_at', 'item_code', 'production_date', 'expire_date', 'item_card_type',
        'transfer_from_batch_id', 'transfer_to_batch_id','is_approved','approved_by'
        ];
}
