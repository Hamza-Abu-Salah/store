<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\InvUomUpdateRequest;
use App\Http\Requests\UomsRequest;
use App\Models\Inv_uom;
use App\Models\Sales_invoices_detail;
use App\Models\Supplier_with_order_detail;
use App\Models\User;
use Illuminate\Http\Request;

class UomsController extends Controller
{
    public function index()
    {
        $data = Inv_uom::select()->orderby('id', 'DESC')->paginate(10);
        if (!empty($data)) {
            foreach ($data as $info) {
                $info->added_by_admin = User::where('id', $info->added_by)->value('name');
                if ($info->updated_by > 0 and $info->updated_by != null) {
                    $info->updated_by_admin = User::where('id', $info->updated_by)->value('name');
                }
            }
        }
        return view('admin.inv_uoms.index', ['data' => $data]);
    }
    public function create()
    {
        return view('admin.inv_uoms.create');
    }
    public function store(UomsRequest $request)
    {
        try {
            $come_code = auth()->user()->come_code;
            //check if not exsits
            $checkExists = Inv_uom::where(['name' => $request->name, 'come_code' => $come_code])->first();
            if ($checkExists == null) {
                $data['name'] = $request->name;
                $data['is_master'] = $request->is_master;
                $data['active'] = $request->active;
                $data['created_at'] = date("Y-m-d H:i:s");
                $data['added_by'] = auth()->user()->id;
                $data['come_code'] = $come_code;
                $data['date'] = date("Y-m-d");
                Inv_uom::create($data);
                return redirect()->route('admin.uoms.index')->with(['success' => 'لقد تم اضافة البيانات بنجاح']);
            } else {
                return redirect()->back()
                    ->with(['error' => 'عفوا اسم الوحدة مسجل من قبل'])
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
        $data = Inv_uom::select()->find($id);
        //check if this uom used befor  نتحقق من الوحده هل تم استخدامها بالفعل ام ليس بعد
        //check in suppliers_with_orders_details نتحقق من المشتريات
        $suppliers_with_orders_detailsCount = get_count_where(new Supplier_with_order_detail(), array('come_code' => $come_code, 'uom_id' => $data['id']));
        //check in Sales_invoices_details نتحقق من المبيعات
        $sales_invoices_detailsCount = get_count_where(new Sales_invoices_detail(), array('come_code' => $come_code, 'uom_id' => $data['id']));
        $total_counter_used = $suppliers_with_orders_detailsCount + $sales_invoices_detailsCount;
        return view('admin.inv_uoms.edit', ['data' => $data, 'total_counter_used' => $total_counter_used]);
    }
    public function update($id, InvUomUpdateRequest $request)
    {
        try {
            $come_code = auth()->user()->come_code;
            $data = Inv_uom::select()->find($id);
            if (empty($data)) {
                return redirect()->route('admin.uoms.index')->with(['error' => 'عفوا غير قادر علي الوصول الي البيانات المطلوبة !!']);
            }
            $checkExists = Inv_uom::where(['name' => $request->name, 'come_code' => $come_code])->where('id', '!=', $id)->first();
            if ($checkExists != null) {
                return redirect()->back()
                    ->with(['error' => 'عفوا اسم الوحدة مسجل من قبل'])
                    ->withInput();
            }
            if ($request->has('is_master')) {
                if ($request->is_master == "") {
                    return redirect()->back()
                        ->with(['error' => '  عفوا من فضلك اختر نوع الوحدة '])
                        ->withInput();
                }
                //check if this uom used befor  نتحقق من الوحده هل تم استخدامها بالفعل ام ليس بعد
                //check in suppliers_with_orders_details نتحقق من المشتريات
                $suppliers_with_orders_detailsCount = get_count_where(new Supplier_with_order_detail(), array('come_code' => $come_code, 'uom_id' => $data['id']));
                //check in Sales_invoices_details نتحقق من المبيعات
                $sales_invoices_detailsCount = get_count_where(new Sales_invoices_detail(), array('come_code' => $come_code, 'uom_id' => $data['id']));
                $total_counter_used = $suppliers_with_orders_detailsCount + $sales_invoices_detailsCount;
                if ($total_counter_used == 0) {
                    $data_to_update['is_master'] = $request->is_master;
                }
            }
            $data_to_update['name'] = $request->name;
            $data_to_update['active'] = $request->active;
            $data_to_update['updated_by'] = auth()->user()->id;
            $data_to_update['updated_at'] = date("Y-m-d H:i:s");
            Inv_uom::where(['id' => $id, 'come_code' => $come_code])->update($data_to_update);
            return redirect()->route('admin.uoms.index')->with(['success' => 'لقد تم تحديث البيانات بنجاح']);
        } catch (\Exception $ex) {
            return redirect()->back()
                ->with(['error' => 'عفوا حدث خطأ ما' . $ex->getMessage()])
                ->withInput();
        }
    }

    public function delete($id)
    {
        try {
            $item_row = Inv_uom::find($id);
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
            $is_master_search = $request->is_master_search;
            if ($search_by_text == '') {
                $field1 = "id";
                $operator1 = ">";
                $value1 = 0;
            } else {
                $field1 = "name";
                $operator1 = "LIKE";
                $value1 = "%{$search_by_text}%";
            }
            if ($is_master_search == 'all') {
                $field2 = "id";
                $operator2 = ">";
                $value2 = 0;
            } else {
                $field2 = "is_master";
                $operator2 = "=";
                $value2 = $is_master_search;
            }
            $data = Inv_uom::where($field1, $operator1, $value1)->where($field2, $operator2, $value2)->orderBy('id', 'DESC')->paginate(10);
            if (!empty($data)) {
                foreach ($data as $info) {
                    $info->added_by_admin = User::where('id', $info->added_by)->value('name');
                    if ($info->updated_by > 0 and $info->updated_by != null) {
                        $info->updated_by_admin = User::where('id', $info->updated_by)->value('name');
                    }
                }
            }
            return view('admin.inv_uoms.ajax_search', ['data' => $data]);
        }
    }
}
