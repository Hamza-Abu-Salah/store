<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admins_ShiftsRequest;
use App\Models\Admin_shift;
use App\Models\Admin_treasuries;
use App\Models\Treasuries;
use App\Models\User;
use Illuminate\Http\Request;

class Admin_ShiftsController extends Controller
{
    public function index()
    {
        $come_code = auth()->user()->come_code;
        $data = get_cols_where_p(new Admin_Shift(), array("*"), array("come_code" => $come_code), 'id', 'DESC', 10);
        if (!empty($data)) {
            foreach ($data as $info) {
                $info->admin_name = User::where('id', $info->user_id)->value('name');
                $info->treasuries_name = Treasuries::where('id', $info->treasuries_id)->value('name');
            }
        }
        $checkExistsOpenShift = get_cols_where_row(new Admin_Shift(), array("id"), array("come_code" => $come_code, "user_id" => auth()->user()->id, "is_finished" => 0));
        return view('admin.admins_shifts.index', ['data' => $data, 'checkExistsOpenShift' => $checkExistsOpenShift]);
    }
    public function create()
    {
        $come_code = auth()->user()->come_code;
        $admins_treasuries = get_cols_where(new Admin_treasuries(), array('treasuries_id'), array('come_code' => $come_code, 'active' => 1, 'user_id' => auth()->user()->id), 'id', 'DESC');
        if (!empty($admins_treasuries)) {
            foreach ($admins_treasuries as $info) {
                $info->treasuries_name = Treasuries::where('id', $info->treasuries_id)->value('name');
                $check_exsits_admins_shifts = get_cols_where_row(new Admin_Shift(), array("id"), array("treasuries_id" => $info->treasuries_id, 'come_code' => $come_code, 'is_finished' => 0));
                if (!empty($check_exsits_admins_shifts) and $check_exsits_admins_shifts != null) {
                    $info->avaliable = false;
                } else {
                    $info->avaliable = true;
                }
            }
        }
        return view('admin.admins_shifts.create', ['admins_treasuries' => $admins_treasuries]);
    }
    public function store(Admins_ShiftsRequest $request)
    {
        try {
            $come_code = auth()->user()->come_code;
            $user_id = auth()->user()->id;
            //check if not exsits open shift to current user
            $checkExistsOpenShift = get_cols_where_row(new Admin_Shift(), array("id"), array("come_code" => $come_code, "user_id" => $user_id, "is_finished" => 0));
            if ($checkExistsOpenShift != null and !empty($checkExistsOpenShift)) {
                return redirect()->route('admin.admin_shift.index')->with(['error' => 'عفوا هناك شفت مفتوح لديك بالفعل حاليا ولايمكن فتح شفت جديد الا بعد اغلاق الشفت الحالي']);
            }
            //check if not exsits open shift to current treasuries_id
            $checkExistsOpentreasuries = get_cols_where_row(new Admin_Shift(), array("id"), array("come_code" => $come_code, "treasuries_id" => $request->treasuries_id, "is_finished" => 0));
            if ($checkExistsOpentreasuries != null and !empty($checkExistsOpentreasuries)) {
                return redirect()->route('admin.admin_shift.index')->with(['error' => '  عفوا الخزنة المختاره بالفعل مستخدمه حاليا لدي شفت اخر ولايمكن استخدامها الا بعد انتهاء الشفت الاخر']);
            }
            //set Shift code
            $row = get_cols_where_row_orderby(new Admin_Shift(), array("shift_code"), array("come_code" => $come_code), 'id', 'DESC');
            if (!empty($row)) {
                $data_insert['shift_code'] = $row['shift_code'] + 1;
            } else {
                $data_insert['shift_code'] = 1;
            }
            $data_insert['user_id'] = $user_id;
            $data_insert['treasuries_id'] = $request->treasuries_id;
            $data_insert['start_date'] = date("Y-m-d H:i:s");
            $data_insert['created_at'] = date("Y-m-d H:i:s");
            $data_insert['added_by'] = auth()->user()->id;
            $data_insert['come_code'] = $come_code;
            $data_insert['date'] = date("Y-m-d");
            $flag = insert(new Admin_Shift(), $data_insert);
            if ($flag) {
                return redirect()->route('admin.admin_shift.index')->with(['success' => 'لقد تم اضافة البيانات بنجاح']);
            } else {
                return redirect()->route('admin.admin_shift.index')->with(['error' => 'عفوا لقد حدث خطأ ما من فضلك حاول مرة اخري']);
            }
        } catch (\Exception $ex) {
            return redirect()->back()
                ->with(['error' => 'عفوا حدث خطأ ما' . $ex->getMessage()])
                ->withInput();
        }
    }
}
