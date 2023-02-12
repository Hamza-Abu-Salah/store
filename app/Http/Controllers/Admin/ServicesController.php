<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ServicesRequest;
use App\Models\Services;
use App\Models\User;
use Illuminate\Http\Request;

class ServicesController extends Controller
{


    public function index()
    {

        try {
            $come_code = auth()->user()->come_code;
            $data = get_cols_where_p(new Services(), array("*"), array("come_code" => $come_code), 'id', 'DESC');
            if (!empty($data)) {
                foreach ($data as $info) {
                    $info->added_by_admin = User::where('id', $info->added_by)->value('name');
                    if ($info->updated_by > 0 and $info->updated_by != null) {
                        $info->updated_by_admin = User::where('id', $info->updated_by)->value('name');
                    }
                }
            }

            return view('admin.services.index', ['data' => $data]);
        } catch (\Exception $ex) {
            return redirect()->back()->with(['error' => 'عفوا حدث خطأ ما']);
        }
    }

    public function create()
    {
        return view('admin.services.create');
    }
    public function store(ServicesRequest $request)
    {
        try {
            $come_code = auth()->user()->come_code;
            //check if not exsits
            $checkExists =Services::select("id")->where(['name' => $request->name, 'come_code' => $come_code])->first();
            if (empty($checkExists)) {
                $data['name'] = $request->name;
                $data['type'] = $request->type;
                $data['active'] = $request->active;
                $data['created_at'] = date("Y-m-d H:i:s");
                $data['added_by'] = auth()->user()->id;
                $data['come_code'] = $come_code;
                $data['date'] = date("Y-m-d");
                insert(new Services(), $data);
                return redirect()->route('admin.Services.index')->with(['success' => 'لقد تم اضافة البيانات بنجاح']);
            } else {
                return redirect()->back()
                    ->with(['error' => 'عفوا اسم الخدمة مسجل من قبل'])
                    ->withInput();
            }
        } catch (\Exception $ex) {
            return redirect()->back()
                ->with(['error' => 'عفوا حدث خطأ ما' . $ex->getMessage()])
                ->withInput();
        }
    }
    public function edit($id)
    {
        $come_code = auth()->user()->come_code;
        $data = get_cols_where_row(new Services(), array("*"), array("come_code" => $come_code, 'id' => $id));
        if (empty($data)) {
            return redirect()->back()
                ->with(['error' => 'عفوا غير قادر الي الوصول الي البيانات المطلوبة !']);
        }
        return view('admin.services.edit', ['data' => $data]);
    }
    public function update($id, ServicesRequest $request)
    {
        try {
            $come_code = auth()->user()->come_code;
            $data = get_cols_where_row(new Services(), array("id"), array("come_code" => $come_code, 'id' => $id));
            if (empty($data)) {
                return redirect()->back()
                    ->with(['error' => 'عفوا غير قادر الي الوصول الي البيانات المطلوبة !']);
            }
            $checkExists = Services::where(['name' => $request->name, 'come_code' => $come_code])->where('id', '!=', $id)->first();
            if ($checkExists != null) {
                return redirect()->back()
                    ->with(['error' => 'عفوا اسم الخدمة مسجل من قبل'])
                    ->withInput();
            }
            $data_to_update['name'] = $request->name;
            $data_to_update['active'] = $request->active;
            $data_to_update['updated_by'] = auth()->user()->id;
            $data_to_update['updated_at'] = date("Y-m-d H:i:s");
            update(new Services(), $data_to_update, array('id' => $id, 'come_code' => $come_code));
            return redirect()->route('admin.Services.index')->with(['success' => 'لقد تم تحديث البيانات بنجاح']);
        } catch (\Exception $ex) {
            return redirect()->back()
                ->with(['error' => 'عفوا حدث خطأ ما' . $ex->getMessage()])
                ->withInput();
        }
    }
    public function delete($id)
    {
        try {
            $item_row = Services::find($id);
            if (!empty($item_row)) {
                $flag = $item_row->delete();
                if ($flag) {
                    return redirect()->back()
                        ->with(['success' => '   تم حذف البيانات بنجاح']);
                } else {
                    return redirect()->back()
                        ->with(['error' => 'عفوا حدث خطأ ما']);
                }
            } else {
                return redirect()->back()
                    ->with(['error' => 'عفوا غير قادر الي الوصول للبيانات المطلوبة']);
            }
        } catch (\Exception $ex) {
            return redirect()->back()
                ->with(['error' => 'عفوا حدث خطأ ما' . $ex->getMessage()]);
        }
    }
    public function ajax_search(Request $request)
    {
        if ($request->ajax()) {
            $search_by_text = $request->search_by_text;
            $type_search = $request->type_search;
            if ($search_by_text == '') {
                $field1 = "id";
                $operator1 = ">";
                $value1 = 0;
            } else {
                $field1 = "name";
                $operator1 = "LIKE";
                $value1 = "%{$search_by_text}%";
            }
            if ($type_search == 'all') {
                $field2 = "id";
                $operator2 = ">";
                $value2 = 0;
            } else {
                $field2 = "type";
                $operator2 = "=";
                $value2 = $type_search;
            }
            $data = Services::where($field1, $operator1, $value1)->where($field2, $operator2, $value2)->orderBy('id', 'DESC')->paginate(10);
            if (!empty($data)) {
                foreach ($data as $info) {
                    $info->added_by_admin = User::where('id', $info->added_by)->value('name');
                    if ($info->updated_by > 0 and $info->updated_by != null) {
                        $info->updated_by_admin = User::where('id', $info->updated_by)->value('name');
                    }
                }
            }
            return view('admin.services.ajax_search', ['data' => $data]);
        }
    }
}
