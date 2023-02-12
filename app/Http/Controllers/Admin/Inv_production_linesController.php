<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Inv_production_linesRequest;
use App\Http\Requests\Inv_production_linesU_Request;
use App\Models\Account;
use App\Models\Admin_panel_setting;
use App\Models\Inv_production_lines;
use App\Models\User;
use Illuminate\Http\Request;

class Inv_production_linesController extends Controller
{
    public function index()
    {
        $come_code = auth()->user()->come_code;
        $data = get_cols_where_p(new Inv_production_lines(), array("*"), array("come_code" => $come_code), 'id', 'DESC', 10);
        if (!empty($data)) {
            foreach ($data as $info) {
                $info->added_by_admin = User::where('id', $info->added_by)->value('name');
                if ($info->updated_by > 0 and $info->updated_by != null) {
                    $info->updated_by_admin = User::where('id', $info->updated_by)->value('name');
                }
            }
        }
        return view('admin.inv_production_lines.index', ['data' => $data]);
    }



    public function create()
    {
        return view('admin.inv_production_lines.create');
    }


    public function store(Inv_production_linesRequest $request)
    {
        try {
            $come_code = auth()->user()->come_code;
            //check if not exsits for name
            $checkExists_name = get_cols_where_row(new Inv_production_lines(), array("id"), array('name' => $request->name, 'come_code' => $come_code));
            if (!empty($checkExists_name)) {
                return redirect()->back()
                    ->with(['error' => 'عفوا اسم خط الانتاج مسجل من قبل'])
                    ->withInput();
            }
            //set Inv_production_lines code
            $row = get_cols_where_row_orderby(new Inv_production_lines(), array("production_lines_code"), array("come_code" => $come_code), 'id', 'DESC');
            if (!empty($row)) {
                $data_insert['production_lines_code'] = $row['production_lines_code'] + 1;
            } else {
                $data_insert['production_lines_code'] = 1;
            }
            //set account number
            $row = get_cols_where_row_orderby(new Account(), array("account_number"), array("come_code" => $come_code), 'id', 'DESC');
            if (!empty($row)) {
                $data_insert['account_number'] = $row['account_number'] + 1;
            } else {
                $data_insert['account_number'] = 1;
            }
            $data_insert['name'] = $request->name;
            $data_insert['address'] = $request->address;
            $data_insert['start_balance_status'] = $request->start_balance_status;
            if ($data_insert['start_balance_status'] == 1) {
                //credit
                $data_insert['start_balance'] = $request->start_balance * (-1);
            } elseif ($data_insert['start_balance_status'] == 2) {
                //debit
                $data_insert['start_balance'] = $request->start_balance;
                if ($data_insert['start_balance'] < 0) {
                    $data_insert['start_balance'] = $data_insert['start_balance'] * (-1);
                }
            } elseif ($data_insert['start_balance_status'] == 3) {
                //balanced
                $data_insert['start_balance'] = 0;
            } else {
                $data_insert['start_balance_status'] = 3;
                $data_insert['start_balance'] = 0;
            }
            $data_insert['phones'] = $request->phones;
            $data_insert['current_balance'] = $data_insert['start_balance'];
            $data_insert['notes'] = $request->notes;
            $data_insert['active'] = $request->active;
            $data_insert['added_by'] = auth()->user()->id;
            $data_insert['created_at'] = date("Y-m-d H:i:s");
            $data_insert['date'] = date("Y-m-d");
            $data_insert['come_code'] = $come_code;
            $flag = insert(new Inv_production_lines(), $data_insert);
            if ($flag) {
                //insert into accounts
                $data_insert_account['name'] = $request->name;
                $data_insert_account['start_balance_status'] = $request->start_balance_status;
                if ($data_insert_account['start_balance_status'] == 1) {
                    //credit
                    $data_insert_account['start_balance'] = $request->start_balance * (-1);
                } elseif ($data_insert_account['start_balance_status'] == 2) {
                    //debit
                    $data_insert_account['start_balance'] = $request->start_balance;
                    if ($data_insert_account['start_balance'] < 0) {
                        $data_insert_account['start_balance'] = $data_insert_account['start_balance'] * (-1);
                    }
                } elseif ($data_insert_account['start_balance_status'] == 3) {
                    //balanced
                    $data_insert_account['start_balance'] = 0;
                } else {
                    $data_insert_account['start_balance_status'] = 3;
                    $data_insert_account['start_balance'] = 0;
                }
                $data_insert_account['current_balance'] = $data_insert_account['start_balance'];
                $production_lines_parent_account = get_field_value(new Admin_panel_setting(), "production_lines_parent_account", array('come_code' => $come_code));
                $data_insert_account['notes'] = $request->notes;
                $data_insert_account['parent_account_number'] = $production_lines_parent_account;
                $data_insert_account['is_parent'] = 0;
                $data_insert_account['account_number'] = $data_insert['account_number'];
                $data_insert_account['account_type'] = 5;
                $data_insert_account['active'] = $request->active;
                $data_insert_account['added_by'] = auth()->user()->id;
                $data_insert_account['created_at'] = date("Y-m-d H:i:s");
                $data_insert_account['date'] = date("Y-m-d");
                $data_insert_account['come_code'] = $come_code;
                $data_insert_account['other_table_FK'] = $data_insert['production_lines_code'];
                $flag = insert(new Account(), $data_insert_account);
            }
            return redirect()->route('admin.inv_production_lines.index')->with(['success' => 'لقد تم اضافة البيانات بنجاح']);
        } catch (\Exception $ex) {
            return redirect()->back()
                ->with(['error' => 'عفوا حدث خطأ ما' . $ex->getMessage()])
                ->withInput();
        }
    }

    public function edit($id)
    {
        $come_code = auth()->user()->come_code;
        $data = get_cols_where_row(new Inv_production_lines(), array("*"), array("id" => $id, "come_code" => $come_code));
        if (empty($data)) {
            return redirect()->route('admin.inv_production_lines.index')->with(['error' => 'عفوا غير قادر علي الوصول الي البيانات المطلوبة !!']);
        }
        return view('admin.inv_production_lines.edit', ['data' => $data]);
    }

    public function update($id, Inv_production_linesU_Request $request)
    {
        try {
            $come_code = auth()->user()->come_code;
            $data = get_cols_where_row(new Inv_production_lines(), array("id", "account_number", "production_lines_code"), array("id" => $id, "come_code" => $come_code));
            if (empty($data)) {
                return redirect()->route('admin.inv_production_lines.index')->with(['error' => 'عفوا غير قادر علي الوصول الي البيانات المطلوبة !!']);
            }
            $checkExists = Inv_production_lines::where(['name' => $request->name, 'come_code' => $come_code])->where('id', '!=', $id)->first();
            if ($checkExists != null) {
                return redirect()->back()
                    ->with(['error' => 'عفوا اسم خط الانتاج مسجل من قبل'])
                    ->withInput();
            }
            $data_to_update['name'] = $request->name;
            $data_to_update['phones'] = $request->phones;
            $data_to_update['address'] = $request->address;
            $data_to_update['notes'] = $request->notes;
            $data_to_update['active'] = $request->active;
            $data_to_update['updated_by'] = auth()->user()->id;
            $data_to_update['updated_at'] = date("Y-m-d H:i:s");
            $flag = update(new Inv_production_lines(), $data_to_update, array('id' => $id, 'come_code' => $come_code));
            if ($flag) {
                $data_to_update_account['name'] = $request->name;
                $data_to_update_account['updated_by'] = auth()->user()->id;
                $data_to_update_account['updated_at'] = date("Y-m-d H:i:s");
                $data_to_update_account['active'] = $request->active;
                $flag = update(new Account(), $data_to_update_account, array('account_number' => $data['account_number'], 'other_table_FK' => $data['production_lines_code'], 'come_code' => $come_code, 'account_type' => 5));
            }
            return redirect()->route('admin.inv_production_lines.index')->with(['success' => 'لقد تم تحديث البيانات بنجاح']);
        } catch (\Exception $ex) {
            return redirect()->back()
                ->with(['error' => 'عفوا حدث خطأ ما' . $ex->getMessage()])
                ->withInput();
        }
    }


    public function ajax_search(Request $request)
    {
        if ($request->ajax()) {

            $come_code = auth()->user()->come_code;
            $search_by_text = $request->search_by_text;
            $searchbyradio = $request->searchbyradio;
            $searchByBalanceStatus = $request->searchByBalanceStatus;
            $searchByactiveStatus = $request->searchByactiveStatus;
            $mirror['searchByBalanceStatus'] = $searchByBalanceStatus;

            if ($search_by_text != '') {
                if ($searchbyradio == 'production_lines_code') {
                    $field1 = "production_lines_code";
                    $operator1 = "=";
                    $value1 = $search_by_text;
                } elseif ($searchbyradio == 'account_number') {
                    $field1 = "account_number";
                    $operator1 = "=";
                    $value1 = $search_by_text;
                } else {
                    $field1 = "name";
                    $operator1 = "like";
                    $value1 = "%{$search_by_text}%";
                }
            } else {
                //true
                $field1 = "id";
                $operator1 = ">";
                $value1 = 0;
            }

            if ($searchByBalanceStatus == "all") {
                $field2 = "id";
                $operator2 = ">";
                $value2 = 0;
            } else {
                if ($searchByBalanceStatus == 1) {
                    $field2 = "current_balance";
                    $operator2 = "<";
                    $value2 = 0;
                } elseif ($searchByBalanceStatus == 2) {
                    $field2 = "current_balance";
                    $operator2 = ">";
                    $value2 = 0;
                } else {
                    $field2 = "current_balance";
                    $operator2 = "=";
                    $value2 = 0;
                }
            }

            if ($searchByactiveStatus == "all") {
                $field3 = "id";
                $operator3 = ">";
                $value3 = 0;
            } else {

                $field3 = "active";
                $operator3 = "=";
                $value3 = $searchByactiveStatus;
            }


            $data = Inv_production_lines::where($field1, $operator1, $value1)->where($field2, $operator2, $value2)->where($field3, $operator3, $value3)->where(['come_code' => $come_code])->orderBy('id', 'DESC')->paginate(10);
            if (!empty($data)) {
                foreach ($data as $info) {
                    $info->added_by_admin = User::where('id', $info->added_by)->value('name');
                    if ($info->updated_by > 0 and $info->updated_by != null) {
                        $info->updated_by_admin = User::where('id', $info->updated_by)->value('name');
                    }
                }
            }
            //General mirror
            $mirror['credit_sum'] = Inv_production_lines::where($field1, $operator1, $value1)->where($field2, $operator2, $value2)->where("current_balance", "<", 0)->where($field3, $operator3, $value3)->where(['come_code' => $come_code])->sum('current_balance');
            $mirror['debit_sum'] = Inv_production_lines::where($field1, $operator1, $value1)->where($field2, $operator2, $value2)->where("current_balance", ">", 0)->where($field3, $operator3, $value3)->where(['come_code' => $come_code])->sum('current_balance');
            $mirror['net'] = $mirror['credit_sum'] + $mirror['debit_sum'];
            return view('admin.inv_production_lines.ajax_search', ['data' => $data, 'mirror' => $mirror]);
        }
    }
}
