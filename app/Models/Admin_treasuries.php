<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Admin_treasuries extends Model
{
    use HasFactory;
    protected $table="admin_treasuries";
    protected $fillable=[
        'user_id','treasuries_id','created_at','updated_at','added_by','updated_by','come_code','date','active'
        ];
}
