<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inv_itemCard_category extends Model
{
    use HasFactory;
    protected $table = "inv_item_card_categories";
    protected $fillable = [
        'name','created_at','updated_at','added_by','updated_by','come_code','date','active'
    ];
}

