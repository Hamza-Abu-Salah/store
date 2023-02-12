<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin_treasuries;
use App\Models\Treasuries;
use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index()
    {
        $come_code = auth()->user()->come_code;
        $data = get_cols_where_p(new User(), array("*"), array("come_code" => $come_code), 'id', 'DESC', 10);
        if (!empty($data)) {
            foreach ($data as $info) {
                $info->added_by_admin = User::where('id', $info->added_by)->value('name');
                if ($info->updated_by > 0 and $info->updated_by != null) {
                    $info->updated_by_admin = User::where('id', $info->updated_by)->value('name');
                }
            }
        }
        return view('admin.admins_accounts.index', ['data' => $data]);
    }
    public function details($id)
    {
        try {
            $come_code = auth()->user()->come_code;
            $data = get_cols_where_row(new User(), array("*"), array("id" => $id, 'come_code' => $come_code));
            if (empty($data)) {
                return redirect()->route('admin.admins_accounts.index')->with(['error' => 'عفوا غير قادر علي الوصول الي البيانات المطلوبة !!']);
            }
            $data['added_by_admin'] = User::where('id', $data['added_by'])->value('name');
            if ($data['updated_by'] > 0 and $data['updated_by'] != null) {
                $data['updated_by_admin'] = User::where('id', $data['updated_by'])->value('name');
            }
            $treasuries = get_cols_where(new Treasuries(), array("id", "name"), array("active" => 1, "come_code" => $come_code), 'id', 'ASC');
            $admins_treasuries = get_cols_where(new Admin_treasuries(), array("*"), array("user_id" => $id, 'come_code' => $come_code), 'id', 'DESC');
            if (!empty($admins_treasuries)) {
                foreach ($admins_treasuries as $info) {
                    $info->name = Treasuries::where('id', $info->treasuries_id)->value('name');
                    $info->added_by_admin = User::where('id', $info->added_by)->value('name');
                    if ($info->updated_by > 0 and $info->updated_by != null) {
                        $info->updated_by_admin = User::where('id', $info->updated_by)->value('name');
                    }
                }
            }
            return view("admin.admins_accounts.details", ['data' => $data, 'admins_treasuries' => $admins_treasuries, 'treasuries' => $treasuries]);
        } catch (\Exception $ex) {
            return redirect()->back()
                ->with(['error' => 'عفوا حدث خطأ ما' . $ex->getMessage()]);
        }
    }
    public function Add_treasuries_To_Admin($id, Request $request)
    {
        try {
            $come_code = auth()->user()->come_code;
            $data = get_cols_where_row(new User(), array("*"), array("id" => $id, 'come_code' => $come_code));
            if (empty($data)) {
                return redirect()->route('admin.admins_accounts.index')->with(['error' => 'عفوا غير قادر علي الوصول الي البيانات المطلوبة !!']);
            }
            //check if not exists
            $admins_treasuries_exsits = get_cols_where_row(new Admin_treasuries(), array("id"), array("user_id" => $id, "treasuries_id" => $request->treasuries_id, 'come_code' => $come_code));
            if (!empty($admins_treasuries_exsits)) {
                return redirect()->route('admin.admins_accounts.details', $id)->with(['error' => 'عفوا هذه الخزنة بالفعل مضافة من قبل لهذا المستخدم !!!']);
            }
            $data_insert['user_id'] = $id;
            $data_insert['treasuries_id'] = $request->treasuries_id;
            $data_insert['active'] = 1;
            $data_insert['created_at'] = date("Y-m-d H:i:s");
            $data_insert['added_by'] = auth()->user()->id;
            $data_insert['come_code'] = $come_code;
            $data_insert['date'] = date("Y-m-d");
            $flag = insert(new Admin_treasuries(), $data_insert);
            if ($flag) {
                return redirect()->route('admin.admins_accounts.details', $id)->with(['success' => 'لقد تم اضافة البيانات بنجاح']);
            } else {
                return redirect()->route('admin.admins_accounts.details', $id)->with(['error' => 'عفوا حدث خطأ ما من فضلك حاول مرة اخري !!!']);
            }
        } catch (\Exception $ex) {
            return redirect()->back()
                ->with(['error' => 'عفوا حدث خطأ ما' . $ex->getMessage()]);
        }
    }
}
