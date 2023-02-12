<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoresRequest;
use App\Models\Store;
use App\Models\User;
use Illuminate\Http\Request;

class StoresController extends Controller
{
    public function index()
    {
        $come_code = auth()->user()->come_code;
        $data = Store::select("*")->where(['come_code'=>$come_code])->orderby('id', 'DESC')->paginate(10);
        if (!empty($data)) {
            foreach ($data as $info) {
                $info->added_by_admin = User::where('id', $info->added_by)->value('name');

                if ($info->updated_by > 0 and $info->updated_by != null) {
                    $info->updated_by_admin = User::where('id', $info->updated_by)->value('name');
                }
            }
        }

        return view('admin.stores.index', compact('data'));
    }

    public function create()
    {
        return view('admin.stores.create');
    }

    public function store(StoresRequest $request)
    {
        try {
            $come_code = auth()->user()->come_code;
            $checkExists = Store::where(['name' => $request->name, 'come_code' => $come_code])->first();
            if ($checkExists == null) {
                $data['name'] = $request->name;
                $data['phone'] = $request->phone;
                $data['address'] = $request->address;
                $data['active'] = $request->active;
                $data['created_at'] = date("Y-m-d H:i:s");
                $data['added_by'] = auth()->user()->id;
                $data['come_code'] = $come_code;
                $data['date'] = date("Y-m-d");
                Store::create($data);

                return redirect()->route('admin.stores.index')->with(['success' => 'لقد تم اضافة البيانات بنجاح']);
            } else {
                return redirect()->back()->with(['error' => 'عفوا اسم الفئة مسجل من قبل'])->withInput();
            }
        } catch (\Exception $ex) {

            return redirect()->back()
                ->with(['error' => 'عفوا حدث خطأ ما' . $ex->getMessage()])
                ->withInput();
        }
    }
    public function edit($id)
    {
        $data = Store::select()->find($id);
        return view('admin.stores.edit',compact('data'));
    }

    public function update($id , StoresRequest $request )
    {
        try {
            $come_code = auth()->user()->come_code;
            $data = Store::select()->find($id);
            if (empty($data)) {
                return redirect()->route('admin.stores.index')->with(['error' => 'عفوا غير قادر علي الوصول الي البيانات المطلوبة !!']);
            }

            $checkExists = Store::where(['name' => $request->name, 'come_code' => $come_code])->where('id', '!=', $id)->first();
            if ($checkExists != null) {
                return redirect()->back()
                    ->with(['error' => 'عفوا اسم الخزنة مسجل من قبل'])
                    ->withInput();
            }

            if ($request->is_master == 1) {
                $checkExists_isMaster = Store::where(['is_master' => 1, 'come_code' => $come_code])->where('id', '!=', $id)->first();
                if ($checkExists_isMaster != null) {
                    return redirect()->back()
                        ->with(['error' => 'عفوا هناك خزنة رئيسية بالفعل مسجلة من قبل لايمكن ان يكون هناك اكثر من خزنة رئيسية'])
                        ->withInput();
                }
            }

            $data_to_update['name'] = $request->name;
            $data_to_update['phone'] = $request->phone;
            $data_to_update['address'] = $request->address;
            $data_to_update['active'] = $request->active;
            $data_to_update['updated_by'] = auth()->user()->id;
            $data_to_update['updated_at'] = date("Y-m-d H:i:s");
            Store::where(['id' => $id, 'come_code' => $come_code])->update($data_to_update);
            return redirect()->route('admin.stores.index')->with(['success' => 'لقد تم تحديث البيانات بنجاح']);

        } catch (\Exception $ex) {
            return redirect()->back()
            ->with(['error' => 'عفوا حدث خطأ ما' . $ex->getMessage()])
            ->withInput();
        }
    }

    public function delete($id)
    {
        try {
            $item_row = Store::find($id);
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
}
