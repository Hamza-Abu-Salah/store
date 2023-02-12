<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SupplierCategoriesRequest;
use App\Models\Supplier_category;
use App\Models\User;
use Illuminate\Http\Request;

class SupplierCotegoryController extends Controller
{
    public function index()
    {
        $come_code = auth()->user()->come_code;
        $data = get_cols_where_p(new Supplier_category(), array("*"), array("come_code" => $come_code), 'id', 'DESC', 10);
        if (!empty($data)) {
            foreach ($data as $info) {
                $info->added_by_admin = User::where('id', $info->added_by)->value('name');
                if ($info->updated_by > 0 and $info->updated_by != null) {
                    $info->updated_by_admin = User::where('id', $info->updated_by)->value('name');
                }
            }
        }
        return view('admin.suppliers_categories.index', ['data' => $data]);
    }
    public function create()
    {
        return view('admin.suppliers_categories.create');
    }
    public function store(SupplierCategoriesRequest $request)
    {
        try {
            $come_code = auth()->user()->come_code;
            //check if not exsits
            $checkExists = Supplier_category::where(['name' => $request->name, 'come_code' => $come_code])->first();
            if ($checkExists == null) {
                $data['name'] = $request->name;
                $data['active'] = $request->active;
                $data['created_at'] = date("Y-m-d H:i:s");
                $data['added_by'] = auth()->user()->id;
                $data['come_code'] = $come_code;
                $data['date'] = date("Y-m-d");
                Supplier_category::create($data);
                return redirect()->route('admin.suppliers_categories.index')->with(['success' => 'لقد تم اضافة البيانات بنجاح']);
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
        $data = Supplier_category::select()->find($id);
        return view('admin.suppliers_categories.edit', ['data' => $data]);
    }
    public function update($id, SupplierCategoriesRequest $request)
    {
        try {
            $come_code = auth()->user()->come_code;
            $data = Supplier_category::select()->find($id);
            if (empty($data)) {
                return redirect()->route('admin.sales_matrial_types.index')->with(['error' => 'عفوا غير قادر علي الوصول الي البيانات المطلوبة !!']);
            }
            $checkExists = Supplier_category::where(['name' => $request->name, 'come_code' => $come_code])->where('id', '!=', $id)->first();
            if ($checkExists != null) {
                return redirect()->back()
                    ->with(['error' => 'عفوا اسم الخزنة مسجل من قبل'])
                    ->withInput();
            }
            $data_to_update['name'] = $request->name;
            $data_to_update['active'] = $request->active;
            $data_to_update['updated_by'] = auth()->user()->id;
            $data_to_update['updated_at'] = date("Y-m-d H:i:s");
            Supplier_category::where(['id' => $id, 'come_code' => $come_code])->update($data_to_update);
            return redirect()->route('admin.suppliers_categories.index')->with(['success' => 'لقد تم تحديث البيانات بنجاح']);
        } catch (\Exception $ex) {
            return redirect()->back()
                ->with(['error' => 'عفوا حدث خطأ ما' . $ex->getMessage()])
                ->withInput();
        }
    }
    public function delete($id)
    {
        try {
            $Sales_matrial_types_row = Supplier_category::find($id);
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
