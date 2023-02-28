<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inv_stores_transfer extends Model
{
    use HasFactory;
    protected $table="inv_stores_transfers";
    protected $fillable=[
        'auto_serial', 'transfer_from_store_id', 'transfer_to_store_id',
        'order_date', 'is_approved', 'come_code', 'notes', 'total_cost_items', 'added_by',
         'created_at', 'updated_at', 'updated_by', 'approved_by','items_counter'
];
}
