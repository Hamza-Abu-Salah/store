<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\salesMatrialTypeRequest;
use App\Models\Sales_matrail_type;
use App\Models\User;
use Illuminate\Http\Request;

class Sales_matrial_typesController extends Controller
{
    public function index()
    {
        $data = Sales_matrail_type::select()->orderby('id', 'DESC')->paginate(10);
        if (!empty($data)) {
            foreach ($data as $info) {
                $info->added_by_admin = User::where('id', $info->added_by)->value('name');
                if ($info->updated_by > 0 and $info->updated_by != null) {
                    $info->updated_by_admin = User::where('id', $info->updated_by)->value('name');
                }
            }
        }
        return view('admin.sales_matrial_types.index', ['data' => $data]);
    }
    public function create()
    {
        return view('admin.sales_matrial_types.create');
    }
    public function store(salesMatrialTypeRequest $request)
    {
        try {
            $come_code = auth()->user()->come_code;
            //check if not exsits
            $checkExists = Sales_matrail_type::where(['name' => $request->name, 'come_code' => $come_code])->first();
            if ($checkExists == null) {
                $data['name'] = $request->name;
                $data['active'] = $request->active;
                $data['created_at'] = date("Y-m-d H:i:s");
                $data['added_by'] = auth()->user()->id;
                $data['come_code'] = $come_code;
                $data['date'] = date("Y-m-d");
                Sales_matrail_type::create($data);
                return redirect()->route('admin.sales_matrial_types.index')->with(['success' => 'لقد تم اضافة البيانات بنجاح']);
            } else {
                return redirect()->back()
                    ->with(['error' => 'عفوا اسم الفئة مسجل من قبل'])
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
        $data = Sales_matrail_type::select()->find($id);
        return view('admin.sales_matrial_types.edit', ['data' => $data]);
    }
    public function update($id, salesMatrialTypeRequest $request)
    {
        try {
            $come_code = auth()->user()->come_code;
            $data = Sales_matrail_type::select()->find($id);
            if (empty($data)) {
                return redirect()->route('admin.sales_matrial_types.index')->with(['error' => 'عفوا غير قادر علي الوصول الي البيانات المطلوبة !!']);
            }
            $checkExists = Sales_matrail_type::where(['name' => $request->name, 'come_code' => $come_code])->where('id', '!=', $id)->first();
            if ($checkExists != null) {
                return redirect()->back()
                    ->with(['error' => 'عفوا اسم الخزنة مسجل من قبل'])
                    ->withInput();
            }
            $data_to_update['name'] = $request->name;
            $data_to_update['active'] = $request->active;
            $data_to_update['updated_by'] = auth()->user()->id;
            $data_to_update['updated_at'] = date("Y-m-d H:i:s");
            Sales_matrail_type::where(['id' => $id, 'come_code' => $come_code])->update($data_to_update);
            return redirect()->route('admin.sales_matrial_types.index')->with(['success' => 'لقد تم تحديث البيانات بنجاح']);
        } catch (\Exception $ex) {
            return redirect()->back()
                ->with(['error' => 'عفوا حدث خطأ ما' . $ex->getMessage()])
                ->withInput();
        }
    }
    public function delete($id)
    {
        try {
            $Sales_matrial_types_row = Sales_matrail_type::find($id);
            if (!empty($Sales_matrial_types_row)) {
                $flag = $Sales_matrial_types_row->delete();
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
}
