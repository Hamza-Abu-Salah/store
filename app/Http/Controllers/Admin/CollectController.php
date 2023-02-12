<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Collect_transactionRequest;
use App\Models\Account;
use App\Models\Account_type;
use App\Models\Admin_shift;
use App\Models\Customer;
use App\Models\Delegate;
use App\Models\Mov_type;
use App\Models\Sales_invoice;
use App\Models\SalesReturn;
use App\Models\Services_with_orders;
use App\Models\Supplier;
use App\Models\Supplier_with_order;
use App\Models\Treasuries;
use App\Models\Treasuries_transaction;
use App\Models\User;
use Illuminate\Http\Request;

class CollectController extends Controller
{
    public function index()
    {
        $come_code = auth()->user()->come_code;
        $data = get_cols_where2_p(new Treasuries_transaction(), array("*"), array("come_code" => $come_code), "money", ">", 0, 'id', 'DESC', 10);
        if (!empty($data)) {
            foreach ($data as $info) {
                $info->added_by_admin = User::where('id', $info->added_by)->value('name');
                $info->treasuries_name = Treasuries::where('id', $info->treasuries_id)->value('name');
                $info->mov_type_name = Mov_type::where('id', $info->mov_type)->value('name');
                if ($info->is_account == 1) {
                    $info->account_type = Account::where(["account_number" => $info->account_number, "come_code" => $come_code])->value("account_type");
                    $info->account_type_name = Account_type::where(["id" => $info->account_type])->value("name");
                    $info->account_name = Account::where(["account_number" => $info->account_number, "come_code" => $come_code])->value("name");
                }
            }
        }
        $checkExistsOpenShift = get_cols_where_row(new Admin_Shift(), array("treasuries_id", "shift_code"), array("come_code" => $come_code, "user_id" => auth()->user()->id, "is_finished" => 0));
        if (!empty($checkExistsOpenShift)) {
            $checkExistsOpenShift['treasuries_name'] = Treasuries::where('id', $checkExistsOpenShift['treasuries_id'])->value('name');
            //get Treasuries Balance
            $checkExistsOpenShift['treasuries_balance_now'] = get_sum_where(new Treasuries_transaction(), "money", array("come_code" => $come_code, "shift_code" => $checkExistsOpenShift['shift_code']));
        }
        $mov_type = get_cols_where(new Mov_type(), array("id", "name"), array("active" => 1, 'in_screen' => 2, 'is_private_internal' => 0), 'id', 'ASC');
        $accounts = get_cols_where(new Account(), array("name", "account_number", "account_type"), array("come_code" => $come_code, "active" => 1, "is_parent" => 0), 'id', 'DESC');
        if (!empty($accounts)) {
            foreach ($accounts as $info) {
                $info->account_type_name = Account_type::where(["id" => $info->account_type])->value("name");
            }
        }
        $accounts_search = get_cols_where(new Account(), array("name", "account_number", "account_type"), array("come_code" => $come_code, "is_parent" => 0), 'id', 'DESC');
        if (!empty($accounts_search)) {
            foreach ($accounts_search as $info) {
                $info->account_type_name = Account_type::where(["id" => $info->account_type])->value("name");
            }
        }
        $treasuries = get_cols_where(new Treasuries(), array("id", "name"), array("come_code" => $come_code), 'id', 'ASC');
        $admins = get_cols_where(new User(), array("id", "name"), array("come_code" => $come_code), 'id', 'ASC');
        return view('admin.collect_transactions.index', ['data' => $data, 'checkExistsOpenShift' => $checkExistsOpenShift, 'accounts' => $accounts, 'mov_type' => $mov_type, 'treasuries' => $treasuries, 'admins' => $admins, 'accounts_search' => $accounts_search]);
    }
    //for collect money
    public function store(Collect_transactionRequest $request)
    {
        try {
            $come_code = auth()->user()->come_code;
            //check if user has open shift or not
            $checkExistsOpenShift = get_cols_where_row(new Admin_Shift(), array("treasuries_id", "shift_code"), array("come_code" => $come_code, "user_id" => auth()->user()->id, "is_finished" => 0, "treasuries_id" => $request->treasuries_id));
            if (empty($checkExistsOpenShift)) {
                return redirect()->back()->with(['error' => "  عفوا لايوجد شفت خزنة مفتوح حاليا !!"])->withInput();
            }
            //first get isal number with treasuries
            $treasury_date = get_cols_where_row(new Treasuries(), array("last_isal_collect"), array("come_code" => $come_code, "id" => $request->treasuries_id));
            if (empty($treasury_date)) {
                return redirect()->back()->with(['error' => "  عفوا بيانات الخزنة المختارة غير موجوده !!"])->withInput();
            }
            $last_record_treasuries_transactions_record = get_cols_where_row_orderby(new Treasuries_transaction(), array("auto_serial"), array("come_code" => $come_code), "auto_serial", "DESC");
            if (!empty($last_record_treasuries_transactions_record)) {
                $dataInsert['auto_serial'] = $last_record_treasuries_transactions_record['auto_serial'] + 1;
            } else {
                $dataInsert['auto_serial'] = 1;
            }
            $dataInsert['isal_number'] = $treasury_date['last_isal_collect'] + 1;
            $dataInsert['shift_code'] = $checkExistsOpenShift['shift_code'];
            //debit مدين
            $dataInsert['money'] = $request->money;
            $dataInsert['treasuries_id'] = $request->treasuries_id;
            $dataInsert['mov_type'] = $request->mov_type;
            $dataInsert['move_date'] = $request->move_date;
            $dataInsert['account_number'] = $request->account_number;
            $dataInsert['is_account'] = 1;
            $dataInsert['is_approved'] = 1;
            //Credit دائن
            $dataInsert['money_for_account'] = $request->money * (-1);
            $dataInsert['byan'] = $request->byan;
            $dataInsert['created_at'] = date("Y-m-Y H:i:s");
            $dataInsert['added_by'] = auth()->user()->id;
            $dataInsert['come_code'] = $come_code;
            $flag = insert(new Treasuries_transaction(), $dataInsert);
            if ($flag) {
                //update Treasuries last_isal_collect
                $dataUpdateTreasuries['last_isal_collect'] = $dataInsert['isal_number'];
                update(new Treasuries(), $dataUpdateTreasuries, array("come_code" => $come_code, "id" => $request->treasuries_id));
                $account_type = Account::where(["account_number" => $request->account_number])->value("account_type");

                if ($account_type == 2) {
                    $the_final_Balance = refresh_account_blance_supplier($request->account_number, new Account(), new Supplier(), new Treasuries_transaction(), new Supplier_with_order(), new services_with_orders(), false);
                } elseif ($account_type == 3) {
                    $the_final_Balance = refresh_account_blance_customer($request->account_number, new Account(), new Customer(), new Treasuries_transaction(), new Sales_invoice(), new SalesReturn(), new services_with_orders(), false);
                } elseif ($account_type == 4) {
                    $the_final_Balance =  refresh_account_blance_delegate($request->account_number, new Account(), new Delegate(), new Treasuries_transaction(), new Sales_invoice(), new services_with_orders(), false);
                } else {
                    $the_final_Balance = refresh_account_blance_General($request->account_number, new Account(), new Treasuries_transaction(), new services_with_orders(), false);
                }





                return redirect()->route('admin.collect_transaction.index')->with(['success' => "لقد تم اضافة البيانات بنجاح "]);
            } else {
                return redirect()->back()->with(['error' => " عفوا حدث خطأ م من فضلك حاول مرة اخري !"])->withInput();
            }
        } catch (\Exception $ex) {
            return redirect()->back()->with(['error' => "عفوا حدث خطأما" . " " . $ex->getMessage()])->withInput();
        }
    }
    public function  get_account_blance(Request $request)
    {
        if ($request->ajax()) {
            $come_code = auth()->user()->come_code;
            $account_number = $request->account_number;
            $AccountData =  Account::select("account_type")->where(["come_code" => $come_code, "account_number" => $account_number])->first();
            if (!empty($AccountData)) {
                if ($AccountData['account_type'] == 2) {
                    $the_final_Balance = refresh_account_blance_supplier($account_number, new Account(), new Supplier(), new Treasuries_transaction(), new Supplier_with_order(), new services_with_orders(), true);
                    return view('admin.collect_transactions.get_account_blance', ['the_final_Balance' => $the_final_Balance]);
                } elseif ($AccountData['account_type'] == 3) {
                    $the_final_Balance = refresh_account_blance_customer($account_number, new Account(), new Customer(), new Treasuries_transaction(), new Sales_invoice(), new SalesReturn(), new services_with_orders(), true);
                    return view('admin.collect_transactions.get_account_blance', ['the_final_Balance' => $the_final_Balance]);
                } elseif ($AccountData['account_type'] == 4) {
                    $the_final_Balance =  refresh_account_blance_delegate($account_number, new Account(), new Delegate(), new Treasuries_transaction(), new Sales_invoice(), new services_with_orders(), true);
                    return view('admin.collect_transactions.get_account_blance', ['the_final_Balance' => $the_final_Balance]);
                } else {
                    $the_final_Balance = refresh_account_blance_General($account_number, new Account(), new Treasuries_transaction(), new services_with_orders(), true);
                    return view('admin.collect_transactions.get_account_blance', ['the_final_Balance' => $the_final_Balance]);
                }
            }
        }
    }
    public function ajax_search(Request $request)
    {
        if ($request->ajax()) {
            $come_code = auth()->user()->come_code;
            $account_number = $request->account_number;
            $mov_type = $request->mov_type;
            $treasuries = $request->treasuries;
            $admins = $request->admins;
            $from_date = $request->from_date;
            $to_date = $request->to_date;
            $searchbyradio = $request->searchbyradio;
            $search_by_text = $request->search_by_text;
            if ($account_number == 'all') {
                //دائما  true
                $field1 = "id";
                $operator1 = ">";
                $value1 = 0;
            } else {
                $field1 = "account_number";
                $operator1 = "=";
                $value1 = $account_number;
            }
            if ($mov_type == 'all') {
                //دائما  true
                $field2 = "id";
                $operator2 = ">";
                $value2 = 0;
            } else {
                $field2 = "mov_type";
                $operator2 = "=";
                $value2 = $mov_type;
            }
            if ($treasuries == 'all') {
                //دائما  true
                $field3 = "id";
                $operator3 = ">";
                $value3 = 0;
            } else {
                $field3 = "treasuries_id";
                $operator3 = "=";
                $value3 = $treasuries;
            }
            if ($admins == 'all') {
                //دائما  true
                $field4 = "id";
                $operator4 = ">";
                $value4 = 0;
            } else {
                $field4 = "added_by";
                $operator4 = "=";
                $value4 = $admins;
            }
            if ($from_date == '') {
                //دائما  true
                $field5 = "id";
                $operator5 = ">";
                $value5 = 0;
            } else {
                $field5 = "move_date";
                $operator5 = ">=";
                $value5 = $from_date;
            }
            if ($to_date == '') {
                //دائما  true
                $field6 = "id";
                $operator6 = ">";
                $value6 = 0;
            } else {
                $field6 = "move_date";
                $operator6 = "<=";
                $value6 = $to_date;
            }
            if ($search_by_text != '') {
                if ($searchbyradio == 'auto_serial') {
                    $field7 = "auto_serial";
                    $operator7 = "=";
                    $value7 = $search_by_text;
                } elseif ($searchbyradio == 'isal_number') {
                    $field7 = "isal_number";
                    $operator7 = "=";
                    $value7 = $search_by_text;
                } elseif ($searchbyradio == 'account_number') {
                    $field7 = "account_number";
                    $operator7 = "=";
                    $value7 = $search_by_text;
                } else {
                    $field7 = "shift_code";
                    $operator7 = "=";
                    $value7 = $search_by_text;
                }
            } else {
                //true
                $field7 = "id";
                $operator7 = ">";
                $value7 = 0;
            }
            $data = Treasuries_transaction::where($field1, $operator1, $value1)->where($field2, $operator2, $value2)->where($field3, $operator3, $value3)->where($field4, $operator4, $value4)->where($field5, $operator5, $value5)->where($field6, $operator6, $value6)
                ->where($field7, $operator7, $value7)->where("money", ">", 0)->orderBy('id', 'DESC')->paginate(10);
            if (!empty($data)) {
                foreach ($data as $info) {
                    $info->added_by_admin = User::where('id', $info->added_by)->value('name');
                    $info->treasuries_name = Treasuries::where('id', $info->treasuries_id)->value('name');
                    $info->mov_type_name = Mov_type::where('id', $info->mov_type)->value('name');
                    if ($info->is_account == 1) {
                        $info->account_type = Account::where(["account_number" => $info->account_number, "come_code" => $come_code])->value("account_type");
                        $info->account_type_name = Account_type::where(["id" => $info->account_type])->value("name");
                        $info->account_name = Account::where(["account_number" => $info->account_number, "come_code" => $come_code])->value("name");
                    }
                }
            }
            $totalCollectInSearch = Treasuries_transaction::where($field1, $operator1, $value1)->where($field2, $operator2, $value2)->where($field3, $operator3, $value3)->where($field4, $operator4, $value4)->where($field5, $operator5, $value5)->where($field6, $operator6, $value6)
                ->where($field7, $operator7, $value7)->where("money", ">", 0)->sum('money');
            return view('admin.collect_transactions.ajax_search', ['data' => $data, 'totalCollectInSearch' => $totalCollectInSearch]);
        }
    }
}
