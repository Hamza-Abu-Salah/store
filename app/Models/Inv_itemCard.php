<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inv_itemCard extends Model
{
    use HasFactory;
    protected $table="inv_item_cards";
    protected $fillable=[
    'item_type','name','inv_itemcard_categories_id','parent_inv_itemcard_id','does_has_retailunit','retail_uom','retail_uom_id','uom_id','retail_uom_quntToParent','created_at','updated_at','added_by','updated_by','come_code','active','date','item_code','barcode',
    'price','nos_gomla_price','gomla_price','price_retail','nos_gomla_price_retail','gomla_price_retail',
    'cost_price','cost_price_retail','has_fixced_price','QUENTITY','QUENTITY_Retail','QUENTITY_all_Retails','photo','All_QUENTITY'
    ];
}
