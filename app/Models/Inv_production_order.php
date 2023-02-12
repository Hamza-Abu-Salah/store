<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inv_production_order extends Model
{
    use HasFactory;
    protected $table = "inv_production_orders";
    protected $fillable = ['auto_serial','production_plane','state','is_approved','added_by','updated_by','come_code','approved_by','approved_at','is_closed','closed_by','closed_at','date','production_plan_date'];
}
