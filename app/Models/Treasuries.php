<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Treasuries extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $table="treasuries";
    protected $fillable=[
        'name','is_master','last_isal_exhcange','last_isal_collect','address','phone','created_at','updated_at','added_by','updated_by','come_code','date'
        ];

}
