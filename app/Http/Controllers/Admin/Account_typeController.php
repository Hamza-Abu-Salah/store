<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Account_type;
use Illuminate\Http\Request;

class Account_typeController extends Controller
{
    public function index()
    {
        $data=get_cols(new Account_type(),array("*"),'relatediternalaccounts','ASC');
        return view('admin.account_types.index', ['data' => $data]);
        }

}
