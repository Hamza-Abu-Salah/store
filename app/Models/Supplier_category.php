<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier_category extends Model
{
    use HasFactory;
    protected $table = "supplier_categories";
    protected $fillable = [
        'name', 'created_at', 'updated_at', 'added_by', 'updated_by', 'come_code', 'date', 'active'
    ];
}
