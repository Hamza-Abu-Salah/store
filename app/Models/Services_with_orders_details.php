<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Services_with_orders_details extends Model
{
    use HasFactory;
    protected $table = "services_with_orders_details";
    protected $fillable = [
        'services_with_orders_auto_serial', 'service_id', 'order_type','notes', 'total', 'added_by', 'updated_by', 'created_at', 'updated_at', 'come_code', 'date'
    ];
}
