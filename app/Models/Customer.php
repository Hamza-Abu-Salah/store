<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $table="customers";
    protected $fillable=[
        'name','account_number',
        'start_balance','current_balance','notes','customer_code','phones','address'
        ,'created_at','updated_at','added_by','updated_by','come_code','date','active','start_balance_status','other_table_FK','parent_account_number','is_parent'
        ];
}
