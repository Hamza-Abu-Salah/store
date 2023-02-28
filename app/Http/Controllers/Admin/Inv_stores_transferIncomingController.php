<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Inv_itemCard;
use App\Models\Inv_itemcard_batches;
use App\Models\Inv_itemcard_movements;
use App\Models\Inv_stores_transfer;
use App\Models\Inv_stores_transfer_details;
use App\Models\Inv_uom;
use App\Models\Store;
use App\Models\User;
use Illuminate\Http\Request;

class Inv_stores_transferIncomingController extends Controller
{
    public function index()
    {
        $come_code = auth()->user()->come_code;
        $data = get_cols_where_p(new inv_stores_transfer(), array("*"), array("come_code" => $come_code), 'id', 'DESC', 10);
        if (!empty($data)) {
            foreach ($data as $info) {
                $info->added_by_admin = User::where('id', $info->added_by)->value('name');
                $info->from_store_name = Store::where('id', $info->transfer_from_store_id)->value('name');
                $info->to_store_name = Store::where('id', $info->transfer_to_store_id)->value('name');
                if ($info->updated_by > 0 and $info->updated_by != null) {
                    $info->updated_by_admin = User::where('id', $info->updated_by)->value('name');
                }
            }
        }
        $stores = get_cols_where(new Store(), array('id', 'name'), array('come_code' => $come_code, 'active' => 1), 'id', 'ASC');
        return view('admin.inv_stores_transfer_incoming.index', ['data' => $data, 'stores' => $stores]);
    }
    public function show($id)
    {
        try {
            $come_code = auth()->user()->come_code;
            $data = get_cols_where_row(new inv_stores_transfer(), array("*"), array("id" => $id, "come_code" => $come_code));
            if (empty($data)) {
                return redirect()->route('admin.inv_stores_transfer.index')->with(['error' => 'عفوا غير قادر علي الوصول الي البيانات المطلوبة !!']);
            }
            $data['added_by_admin'] = User::where('id', $data['added_by'])->value('name');
            $data['from_store_name'] = Store::where('id', $data['transfer_from_store_id'])->value('name');
            $data['to_store_name'] = Store::where('id', $data['transfer_to_store_id'])->value('name');
            $data['store_name'] = Store::where('id', $data['store_id'])->value('name');
            if ($data['updated_by'] > 0 and $data['updated_by'] != null) {
                $data['updated_by_admin'] = User::where('id', $data['updated_by'])->value('name');
            }
            $details = get_cols_where(new inv_stores_transfer_details(), array("*"), array('inv_stores_transfer_auto_serial' => $data['auto_serial'],  'come_code' => $come_code), 'id', 'DESC');
            if (!empty($details)) {
                foreach ($details as $info) {
                    $info->item_card_name = Inv_itemCard::where('item_code', $info->item_code)->value('name');
                    $info->uom_name = get_field_value(new Inv_uom(), "name", array("id" => $info->uom_id));
                    $data['added_by_admin'] = User::where('id', $data['added_by'])->value('name');
                    if ($data['updated_by'] > 0 and $data['updated_by'] != null) {
                        $data['updated_by_admin'] = User::where('id', $data['updated_by'])->value('name');
                    }
                }
            }
            return view("admin.inv_stores_transfer_incoming.show", ['data' => $data, 'details' => $details]);
        } catch (\Exception $ex) {
            return redirect()->back()
                ->with(['error' => 'عفوا حدث خطأ ما' . $ex->getMessage()]);
        }
    }
    //اعتماد واستلام كمية صنف
    function approve_one_details($id, $id_parent, Request $request)
    {
        $come_code = auth()->user()->come_code;
        //check is not approved
        $data = get_cols_where_row(new inv_stores_transfer(), array("auto_serial", "transfer_from_store_id", "transfer_to_store_id", "is_approved"), array("id" => $id_parent, "come_code" => $come_code));
        if (empty($data)) {
            return redirect()->route("admin.inv_stores_transfer_incoming.index")->with(['error' => "عفوا غير قادر علي الوصول الي البيانات المطلوبة !!"]);
        }
        if ($data['is_approved'] == 1) {
            return redirect()->route("admin.suppliers_orders.show", $id_parent)->with(['error' => "عفوا لايمكن اعتماد فاتورة معتمده من قبل !!"]);
        }
        $dataDetails = get_cols_where_row(new inv_stores_transfer_details(), array("is_approved"), array("id" => $id, "come_code" => $come_code));
        if (empty($dataDetails)) {
            return redirect()->route("admin.inv_stores_transfer_incoming.index")->with(['error' => "عفوا غير قادر علي الوصول الي البيانات المطلوبة !!"]);
        }
        if ($dataDetails['is_approved'] == 1) {
            return redirect()->route("admin.inv_stores_transfer_incoming.show", $id_parent)->with(['error' => "عفوا لايمكن اعتماد صنف تم اعتمادة من   قبل !!"]);
        }
        //store move حركة مخزن الاستلام
        //first Get item card data جنجيب الاصناف بالكود الحالي  علي امر التحويل
        $items = get_cols_where(new inv_stores_transfer_details(), array("*"), array("inv_stores_transfer_auto_serial" => $data['auto_serial'], "come_code" => $come_code, "id" => $id), "id", "ASC");
        if (!empty($items)) {
            foreach ($items as $info) {
                //get itemCard Data
                $itemCard_Data = get_cols_where_row(new Inv_itemCard(), array("uom_id", "retail_uom_quntToParent", "retail_uom_id", "does_has_retailunit"), array("come_code" => $come_code, "item_code" => $info->item_code));
                if (!empty($itemCard_Data)) {
                    //get Quantity Befor any Action  حنجيب كيمة الصنف بكل المخازن قبل الحركة
                    $quantityBeforMove = get_sum_where(new Inv_itemcard_batches(), "quantity", array("item_code" => $info->item_code, "come_code" => $come_code));
                    //get Quantity Befor any Action  حنجيب كيمة الصنف  بمخزن الاستلام المشتريات الحالي قبل الحركة
                    $quantityBeforMoveCurrntStore = get_sum_where(new Inv_itemcard_batches(), "quantity", array("item_code" => $info->item_code, "come_code" => $come_code, 'store_id' => $data['transfer_to_store_id']));
                    $MainUomName = get_field_value(new Inv_uom(), "name", array("come_code" => $come_code, "id" => $itemCard_Data['uom_id']));
                    //if is parent Uom لو وحده اب
                    if ($info->isparentuom == 1) {
                        $quntity = $info->deliverd_quantity;
                        $unit_price = $info->unit_price;
                    } else {
                        // if is retail  لو كان بوحده الابن التجزئة
                        //التحويل من الاب للابن بنضرب   في النسبة بينهم - اما التحويل من الابن للاب بنقسم علي النسبه بينهما
                        $quntity = ($info->deliverd_quantity / $itemCard_Data['retail_uom_quntToParent']);
                        $unit_price = $info->unit_price * $itemCard_Data['retail_uom_quntToParent'];
                    }
                    //بندخل الكميات للمخزن بوحده القياس الاب  اجباري
                    $dataInsertBatch["store_id"] = $data['transfer_to_store_id'];
                    $dataInsertBatch["item_code"] = $info->item_code;
                    $dataInsertBatch["production_date"] = $info->production_date;
                    $dataInsertBatch["expired_date"] = $info->expire_date;
                    $dataInsertBatch["unit_cost_price"] = $unit_price;
                    $dataInsertBatch["inv_uoms_id"] = $itemCard_Data['uom_id'];
                    $OldBatchExsists = get_cols_where_row(new Inv_itemcard_batches(), array("quantity", "id", "unit_cost_price"), $dataInsertBatch);
                    if (!empty($OldBatchExsists)) {
                        //update current Batch تحديث علي الباتش القديمة
                        $dataUpdateOldBatch['quantity'] = $OldBatchExsists['quantity'] + $quntity;
                        $dataUpdateOldBatch['total_cost_price'] = $OldBatchExsists['unit_cost_price'] * $dataUpdateOldBatch['quantity'];
                        $dataUpdateOldBatch["updated_at"] = date("Y-m-d H:i:s");
                        $dataUpdateOldBatch["updated_by"] = auth()->user()->id;
                        $theBatchID = $OldBatchExsists['id'];
                        update(new Inv_itemcard_batches(), $dataUpdateOldBatch, array("id" => $OldBatchExsists['id'], "come_code" => $come_code));
                    } else {
                        //insert new Batch ادخال باتش جديده
                        $dataInsertBatch["quantity"] = $quntity;
                        $dataInsertBatch["total_cost_price"] = $info->total_price;
                        $dataInsertBatch["created_at"] = date("Y-m-d H:i:s");
                        $dataInsertBatch["added_by"] = auth()->user()->id;
                        $dataInsertBatch["come_code"] = $come_code;
                        $row = get_cols_where_row_orderby(new Inv_itemcard_batches(), array("auto_serial"), array("come_code" => $come_code), 'id', 'DESC');
                        if (!empty($row)) {
                            $dataInsertBatch['auto_serial'] = $row['auto_serial'] + 1;
                        } else {
                            $dataInsertBatch['auto_serial'] = 1;
                        }
                        insert(new Inv_itemcard_batches(), $dataInsertBatch);
                        $theBatchID = get_field_value(new Inv_itemcard_batches(), 'auto_serial', $dataInsertBatch);
                    }
                    $dataToUpdateDetails['is_approved'] = 1;
                    $dataToUpdateDetails['approved_by'] = auth()->user()->id;
                    $dataToUpdateDetails['approved_at'] = date("Y-m-d H:i:s");
                    update(new inv_stores_transfer_details(), $dataToUpdateDetails, array("id" => $id, "come_code" => $come_code));
                    //كمية الصنف بكل المخازن بعد اتمام حركة الباتشات وترحيلها
                    $quantityAfterMove = get_sum_where(new Inv_itemcard_batches(), "quantity", array("item_code" => $info->item_code, "come_code" => $come_code));
                    //كمية الصنف بمخزن فاتورة الشراء  بعد اتمام حركة الباتشات وترحيلها
                    $quantityAfterMoveCurrentStore = get_sum_where(new Inv_itemcard_batches(), "quantity", array("item_code" => $info->item_code, "come_code" => $come_code, 'store_id' => $data['transfer_to_store_id']));
                    $dataInsert_inv_itemcard_movements['inv_itemcard_movements_categories'] = 3;
                    $dataInsert_inv_itemcard_movements['items_movements_types'] = 22;
                    $dataInsert_inv_itemcard_movements['item_code'] = $info->item_code;
                    //كود الفاتورة الاب
                    $dataInsert_inv_itemcard_movements['FK_table'] = $data['auto_serial'];
                    //كود صف الابن بتفاصيل الفاتورة
                    $dataInsert_inv_itemcard_movements['FK_table_details'] = $info->id;
                    $from_store_name = Store::where('id', $data['transfer_from_store_id'])->value('name');
                    $dataInsert_inv_itemcard_movements['byan'] = "نظير اعتماد واستلام امر تحويل وارد من المخزن   " . " " . $from_store_name . " امر تحويل رقم" . " " . $data['auto_serial'];
                    //كمية الصنف بكل المخازن قبل الحركة
                    $dataInsert_inv_itemcard_movements['quantity_befor_movement'] = "عدد " . " " . ($quantityBeforMove * 1) . " " . $MainUomName;
                    // كمية الصنف بكل المخازن بعد  الحركة
                    $dataInsert_inv_itemcard_movements['quantity_after_move'] = "عدد " . " " . ($quantityAfterMove * 1) . " " . $MainUomName;
                    //كمية الصنف  المخزن الحالي قبل الحركة
                    $dataInsert_inv_itemcard_movements['quantity_befor_move_store'] = "عدد " . " " . ($quantityBeforMoveCurrntStore * 1) . " " . $MainUomName;
                    // كمية الصنف بالمخزن الحالي بعد الحركة الحركة
                    $dataInsert_inv_itemcard_movements['quantity_after_move_store'] = "عدد " . " " . ($quantityAfterMoveCurrentStore * 1) . " " . $MainUomName;
                    $dataInsert_inv_itemcard_movements["store_id"] = $data['transfer_to_store_id'];
                    $dataInsert_inv_itemcard_movements["created_at"] = date("Y-m-d H:i:s");
                    $dataInsert_inv_itemcard_movements["added_by"] = auth()->user()->id;
                    $dataInsert_inv_itemcard_movements["date"] = date("Y-m-d");
                    $dataInsert_inv_itemcard_movements["come_code"] = $come_code;
                    insert(new Inv_itemcard_movements(), $dataInsert_inv_itemcard_movements);
                    //item Move Card حركة الصنف
                }
                // update itemcard Quantity mirror  تحديث المرآه الرئيسية للصنف
                do_update_itemCardQuantity(new Inv_itemCard(), $info->item_code, new Inv_itemcard_batches(), $itemCard_Data['does_has_retailunit'], $itemCard_Data['retail_uom_quntToParent']);
            }
        }
        return redirect()->route("admin.inv_stores_transfer_incoming.show", $id_parent)->with(['success' => " تم اعتماد واستلام كمية الصنف  بنجاح  "]);
    }
    public function load_cancel_one_details(Request $request)
    {
        if ($request->ajax()) {
            $come_code = auth()->user()->come_code;
            $parent_pill_data = get_cols_where_row(new inv_stores_transfer(), array("is_approved", "id"), array("auto_serial" => $request->autoserailparent, "come_code" => $come_code, "is_approved" => 0));
            $data = get_cols_where_row(new inv_stores_transfer_details(), array("is_approved", "id"), array("id" => $request->id, "come_code" => $come_code, "is_approved" => 0, "is_canceld_receive" => 0));
            return view("admin.inv_stores_transfer_incoming.load_cancel_one_details", ['parent_pill_data' => $parent_pill_data, 'data' => $data]);
        }
    }
    //اعتماد وترحيل فاتورة المشتريات
    function do_cancel_one_details($id, $id_parent, Request $request)
    {
        $come_code = auth()->user()->come_code;
        //check is not approved
        $data = get_cols_where_row(new inv_stores_transfer(), array("auto_serial", "transfer_from_store_id", "transfer_to_store_id", "is_approved"), array("id" => $id_parent, "come_code" => $come_code));
        if (empty($data)) {
            return redirect()->route("admin.inv_stores_transfer_incoming.index")->with(['error' => "عفوا غير قادر علي الوصول الي البيانات المطلوبة !!"]);
        }
        if ($data['is_approved'] == 1) {
            return redirect()->route("admin.suppliers_orders.show", $id_parent)->with(['error' => "عفوا لايمكن اعتماد فاتورة معتمده من قبل !!"]);
        }
        $dataDetails = get_cols_where_row(new inv_stores_transfer_details(), array("is_approved"), array("id" => $id, "come_code" => $come_code));
        if (empty($dataDetails)) {
            return redirect()->route("admin.inv_stores_transfer_incoming.index")->with(['error' => "عفوا غير قادر علي الوصول الي البيانات المطلوبة !!"]);
        }
        if ($dataDetails['is_approved'] == 1) {
            return redirect()->route("admin.inv_stores_transfer_incoming.show", $id_parent)->with(['error' => "عفوا لايمكن اعتماد صنف تم اعتمادة من   قبل !!"]);
        }
        if ($dataDetails['is_canceld_receive'] == 1) {
            return redirect()->route("admin.inv_stores_transfer_incoming.show", $id_parent)->with(['error' => "عفوا لايمكن اعتماد صنف تم اعتمادة من   قبل !!"]);
        }
        $dataToUpdateDetails['canceld_cause'] = $request->canceld_cause;
        $dataToUpdateDetails['is_canceld_receive'] = 1;
        $dataToUpdateDetails['canceld_by'] = auth()->user()->id;
        $dataToUpdateDetails['canceld_at'] = date("Y-m-d H:i:s");
        update(new inv_stores_transfer_details(), $dataToUpdateDetails, array("id" => $id, "come_code" => $come_code));
        return redirect()->route("admin.inv_stores_transfer_incoming.show", $id_parent)->with(['success' => "لقد تم الالغاء  بنجاح"]);
    }
}
