<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;
    protected $table="suppliers";
    protected $fillable=[
        'name','account_number','parent_account_number','other_table_FK',
        'start_balance','current_balance','notes','supplier_code','phones','address','suppliers_categories_id'
        ,'created_at','updated_at','added_by','updated_by','come_code','date','active','start_balance_status','is_parent'
        ];
}
