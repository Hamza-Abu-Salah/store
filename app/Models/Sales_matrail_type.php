<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sales_matrail_type extends Model
{
    use HasFactory;
    protected $table = "sales_matrail_types";
    protected $fillable = [
        'name', 'created_at', 'updated_at', 'added_by', 'updated_by', 'come_code', 'date', 'active'
    ];
}
