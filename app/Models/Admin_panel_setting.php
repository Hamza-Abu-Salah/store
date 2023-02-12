<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Admin_panel_setting extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $table = "admin_panel_settings";
    protected $fillable = [
        'system_name', 'photo', 'active', 'general_alert', 'address',
        'phone', 'created_at', 'updated_at', 'added_by', 'updated_by',
        'come_code', 'customer_parent_account_number',
        'suppliers_parent_account_number', 'delegate_parent_account_number',
        'employees_parent_account_number','notes','production_lines_parent_account'
    ];
}
