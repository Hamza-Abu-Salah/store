<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Admin_shift extends Model
{
    use HasFactory;
    protected $table="admin_shifts";
    protected $fillable=['user_id', 'treasuries_id', 'treasuries_balnce_in_shift_start', 'start_date', 'end_date', 'is_finished', 'is_delivered_and_review', 'delivered_to_admin_id', 'delivered_to_admin_sift_id', 'delivered_to_treasuries_id', 'money_should_deviled', 'what_realy_delivered', 'money_state', 'money_state_value', 'receive_type', 'review_receive_date', 'treasuries_transactions_id', 'added_by', 'created_at', 'notes', 'come_code'
    ,'updated_by','updated_at','date','shift_code'
    ];
}
//
