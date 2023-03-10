<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Inv_stores_transferRequest;
use App\Http\Requests\Inv_stores_transferRequestUpdate;
use App\Models\Inv_itemCard;
use App\Models\Inv_itemcard_batches;
use App\Models\Inv_itemcard_movements;
use App\Models\Inv_stores_transfer;
use App\Models\Inv_stores_transfer_details;
use App\Models\Inv_uom;
use App\Models\Store;
use App\Models\User;
use Illuminate\Http\Request;

class Inv_stores_transferController extends Controller
{
    public function index()
    {
        $come_code = auth()->user()->come_code;
        $data = get_cols_where_p(new Inv_stores_transfer(), array("*"), array("come_code" => $come_code), 'id', 'DESC', 10);
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
        return view('admin.inv_stores_transfer.index', ['data' => $data, 'stores' => $stores]);
    }

    public function create()
    {
        $come_code = auth()->user()->come_code;
        $stores = Store::select('id', 'name')->where(['come_code' => $come_code, 'active' => 1])->orderby('id', 'ASC')->get();
        return view('admin.inv_stores_transfer.create', ['stores' => $stores]);
    }
    public function store(Inv_stores_transferRequest $request)
    {
        try {
            $come_code = auth()->user()->come_code;

            if ($request->transfer_from_store_id == $request->transfer_to_store_id) {
                return redirect()->back()
                    ->with(['error' => '????????  ???????????? ???? ???????? ???????? ?????????? ???? ???????? ???????? ????????????????  !!'])
                    ->withInput();
            }

            $CheckOpenOrder = get_cols_where_row(new inv_stores_transfer(), array("auto_serial"), array("come_code" => $come_code, 'transfer_from_store_id' => $request->transfer_from_store_id, "transfer_to_store_id" => $request->transfer_to_store_id, 'is_approved' => 0));
            if (!empty($CheckOpenOrder)) {
                return redirect()->back()
                    ->with(['error' => '???????? ???????? ???????????? ?????? ?????????? ?????????? ?????????? ?????? ????????????????   !!'])
                    ->withInput();
            }


            $row = get_cols_where_row_orderby(new inv_stores_transfer(), array("auto_serial"), array("come_code" => $come_code), 'id', 'DESC');
            if (!empty($row)) {
                $data_insert['auto_serial'] = $row['auto_serial'] + 1;
            } else {
                $data_insert['auto_serial'] = 1;
            }
            $data_insert['order_date'] = $request->order_date;
            $data_insert['transfer_from_store_id'] = $request->transfer_from_store_id;
            $data_insert['transfer_to_store_id'] = $request->transfer_to_store_id;
            $data_insert['added_by'] = auth()->user()->id;
            $data_insert['created_at'] = date("Y-m-d H:i:s");
            $data_insert['date'] = date("Y-m-d");
            $data_insert['come_code'] = $come_code;
            insert(new inv_stores_transfer(), $data_insert);
            return redirect()->route("admin.inv_stores_transfer.index")->with(['success' => '?????? ???? ?????????? ???????????????? ??????????']);
        } catch (\Exception $ex) {
            return redirect()->back()
                ->with(['error' => '???????? ?????? ?????? ????' . $ex->getMessage()])
                ->withInput();
        }
    }

    public function show($id)
    {
        try {
            $come_code = auth()->user()->come_code;
            $data = get_cols_where_row(new inv_stores_transfer(), array("*"), array("id" => $id, "come_code" => $come_code));
            if (empty($data)) {
                return redirect()->route('admin.inv_stores_transfer.index')->with(['error' => '???????? ?????? ???????? ?????? ???????????? ?????? ???????????????? ???????????????? !!']);
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
            return view("admin.inv_stores_transfer.show", ['data' => $data, 'details' => $details]);
        } catch (\Exception $ex) {
            return redirect()->back()
                ->with(['error' => '???????? ?????? ?????? ????' . $ex->getMessage()]);
        }
    }


    public function edit($id)
    {
        $come_code = auth()->user()->come_code;
        $data = get_cols_where_row(new inv_stores_transfer(), array("*"), array("id" => $id, "come_code" => $come_code));
        if (empty($data)) {
            return redirect()->route('admin.inv_stores_transfer.index')->with(['error' => '???????? ?????? ???????? ?????? ???????????? ?????? ???????????????? ???????????????? !!']);
        }
        if ($data['is_approved'] == 1) {
            return redirect()->route('admin.inv_stores_transfer.index')->with(['error' => '???????? ???????????? ?????????????? ?????? ???????????? ???????????? ??????????????']);
        }
        $stores = get_cols_where(new Store(), array('id', 'name'), array('come_code' => $come_code), 'id', 'DESC');
        $added_counter_details = get_count_where(new inv_stores_transfer_details(), array("come_code" => $come_code, "inv_stores_transfer_auto_serial" => $data['auto_serial']));
        return view('admin.inv_stores_transfer.edit', ['data' => $data, 'stores' => $stores, 'added_counter_details' => $added_counter_details]);
    }


    public function update($id, Inv_stores_transferRequestUpdate $request)
    {
        try {
            $come_code = auth()->user()->come_code;
            $data = get_cols_where_row(new inv_stores_transfer(), array("is_approved", "auto_serial"), array("id" => $id, "come_code" => $come_code));
            if (empty($data)) {
                return redirect()->route('admin.inv_stores_transfer.index')->with(['error' => '???????? ?????? ???????? ?????? ???????????? ?????? ???????????????? ???????????????? !!']);
            }
            if ($data['is_approved'] == 1) {
                return redirect()->route('admin.inv_stores_transfer.index')->with(['error' => '???????? ???????????? ?????????????? ?????? ???????????? ???????????? ??????????????']);
            }
            $added_counter_details = get_count_where(new inv_stores_transfer_details(), array("come_code" => $come_code, "inv_stores_transfer_auto_serial" => $data['auto_serial']));
            if ($added_counter_details == 0) {
                $data_to_update['transfer_from_store_id'] = $request->transfer_from_store_id;
                $data_to_update['transfer_to_store_id'] = $request->transfer_to_store_id;
            }
            $data_to_update['order_date'] = $request->order_date;
            $data_to_update['notes'] = $request->notes;
            $data_to_update['updated_by'] = auth()->user()->id;
            $data_to_update['updated_at'] = date("Y-m-d H:i:s");
            update(new inv_stores_transfer(), $data_to_update, array("id" => $id, "come_code" => $come_code));
            return redirect()->route('admin.inv_stores_transfer.index')->with(['success' => '?????? ???? ?????????? ???????????????? ??????????']);
        } catch (\Exception $ex) {
            return redirect()->back()
                ->with(['error' => '???????? ?????? ?????? ????' . $ex->getMessage()])
                ->withInput();
        }
    }


    public function delete($id)
    {
        try {
            $come_code = auth()->user()->come_code;
            $parent_pill_data = get_cols_where_row(new inv_stores_transfer(), array("is_approved", "auto_serial"), array("id" => $id, "come_code" => $come_code));
            if (empty($parent_pill_data)) {
                return redirect()->back()
                    ->with(['error' => '???????? ?????? ?????? ????']);
            }
            if ($parent_pill_data['is_approved'] == 1) {
                if (empty($parent_pill_data)) {
                    return redirect()->back()
                        ->with(['error' => '????????  ???????????? ?????????? ?????????????? ???????????? ???????????? ??????????????']);
                }
            }
            $added_counter_details = get_count_where(new inv_stores_transfer_details(), array("come_code" => $come_code, "inv_stores_transfer_auto_serial" => $parent_pill_data['auto_serial']));
            if ($added_counter_details > 0) {
                return redirect()->back()
                    ->with(['error' => '????????  ???????????? ?????????? ???????????? ?????????? ???????????? ?????????? ?????? ?????? ??????????????!   ']);
            }
            //?????????? ???????????????? ????????
            $flag = delete(new inv_stores_transfer(), array("id" => $id, "come_code" => $come_code));
            if ($flag) {
                delete(new inv_stores_transfer_details(), array("inv_stores_transfer_auto_serial" => $parent_pill_data['auto_serail'], "come_code" => $come_code));
            }
            return redirect()->route('admin.inv_stores_transfer.index')->with(['success' => '?????? ???? ??????  ???????????????? ??????????']);
        } catch (\Exception $ex) {
            return redirect()->back()
                ->with(['error' => '???????? ?????? ?????? ????' . $ex->getMessage()]);
        }
    }
    public function load_modal_add_details(Request $request)
    {
        if ($request->ajax()) {
            $come_code = auth()->user()->come_code;
            $parent_pill_data = get_cols_where_row(new inv_stores_transfer(), array("is_approved", "transfer_from_store_id"), array("auto_serial" => $request->autoserailparent, "come_code" => $come_code));
            if (!empty($parent_pill_data)) {
                if ($parent_pill_data['is_approved'] == 0) {
                    $item_cards = get_cols_where(new Inv_itemCard(), array("name", "item_code", "item_type"), array('active' => 1, 'come_code' => $come_code), 'id', 'DESC');
                    $stores = get_cols_where(new Store(), array('id', 'name'), array('come_code' => $come_code, 'id' => $parent_pill_data['transfer_from_store_id']), 'id', 'DESC');
                    return view("admin.inv_stores_transfer.load_add_new_itemdetails", ['parent_pill_data' => $parent_pill_data, 'item_cards' => $item_cards, 'stores' => $stores]);
                }
            }
        }
    }
    public function get_item_uoms(Request $request)
    {
        if ($request->ajax()) {
            $come_code = auth()->user()->come_code;
            $item_code = $request->item_code;
            $item_card_Data = get_cols_where_row(new Inv_itemCard(), array("does_has_retailunit", "retail_uom_id", "uom_id"), array("item_code" => $item_code, "come_code" => $come_code));
            if (!empty($item_card_Data)) {
                if ($item_card_Data['does_has_retailunit'] == 1) {
                    $item_card_Data['parent_uom_name'] = get_field_value(new Inv_uom(), "name", array("id" => $item_card_Data['uom_id']));
                    $item_card_Data['retial_uom_name'] = get_field_value(new Inv_uom(), "name", array("id" => $item_card_Data['retail_uom_id']));
                } else {
                    $item_card_Data['parent_uom_name'] = get_field_value(new Inv_uom(), "name", array("id" => $item_card_Data['uom_id']));
                }
            }
            return view("admin.inv_stores_transfer.get_item_uoms", ['item_card_Data' => $item_card_Data]);
        }
    }
    public function get_item_batches(Request $request)
    {
        $come_code = auth()->user()->come_code;
        if ($request->ajax()) {
            $item_card_Data = get_cols_where_row(new Inv_itemCard(), array("item_type", "uom_id", "retail_uom_quntToParent"), array("come_code" => $come_code, "item_code" => $request->item_code));
            if (!empty($item_card_Data)) {
                $requesed['uom_id'] = $request->uom_id;
                $requesed['store_id'] = $request->store_id;
                $requesed['item_code'] = $request->item_code;
                $parent_uom = $item_card_Data['uom_id'];
                $uom_Data = get_cols_where_row(new Inv_uom(), array("name", "is_master"), array("come_code" => $come_code, "id" => $requesed['uom_id']));
                if (!empty($uom_Data)) {
                    //???? ?????? ?????????? ???????? ???????? ??????????????????
                    if ($item_card_Data['item_type'] == 2) {
                        $inv_itemcard_batches = get_cols_where(
                            new Inv_itemcard_batches(),
                            array("unit_cost_price", "quantity", "production_date", "expired_date", "auto_serial"),
                            array("come_code" => $come_code, "store_id" => $requesed['store_id'], "item_code" => $requesed['item_code'], "inv_uoms_id" => $parent_uom),
                            'production_date',
                            'ASC'
                        );
                    } else {
                        $inv_itemcard_batches = get_cols_where(
                            new Inv_itemcard_batches(),
                            array("unit_cost_price", "quantity", "auto_serial"),
                            array("come_code" => $come_code, "store_id" => $requesed['store_id'], "item_code" => $requesed['item_code'], "inv_uoms_id" => $parent_uom),
                            'id',
                            'ASC'
                        );
                    }
                    return view("admin.inv_stores_transfer.get_item_batches", ['item_card_Data' => $item_card_Data, 'requesed' => $requesed, 'uom_Data' => $uom_Data, 'inv_itemcard_batches' => $inv_itemcard_batches]);
                }
            }
        }
    }
    public function Add_item_to_invoice(Request $request)
    {
        try {
            if ($request->ajax()) {
                $come_code = auth()->user()->come_code;
                $invoice_data = get_cols_where_row(new inv_stores_transfer(), array("is_approved", "order_date", "id", "transfer_from_store_id", "transfer_to_store_id"), array("come_code" => $come_code, "auto_serial" => $request->autoserailparent));
                if (!empty($invoice_data)) {
                    if ($invoice_data['is_approved'] == 0) {
                        $batch_data = get_cols_where_row(new Inv_itemcard_batches(), array("quantity", "unit_cost_price", "id", "production_date", "expired_date"), array("come_code" => $come_code, "auto_serial" => $request->inv_itemcard_batches_autoserial, 'store_id' => $request->store_id, 'item_code' => $request->item_code));
                        if (!empty($batch_data)) {
                            if ($batch_data['quantity'] >= $request->item_quantity) {
                                $itemCard_Data = get_cols_where_row(new Inv_itemCard(), array("uom_id", "retail_uom_quntToParent", "retail_uom_id", "does_has_retailunit", "item_type"), array("come_code" => $come_code, "item_code" => $request->item_code));
                                if (!empty($itemCard_Data)) {
                                    $MainUomName = get_field_value(new Inv_uom(), "name", array("come_code" => $come_code, "id" => $itemCard_Data['uom_id']));
                                    $datainsert_items['inv_stores_transfer_auto_serial'] = $request->autoserailparent;
                                    $datainsert_items['inv_stores_transfer_id'] = $invoice_data['id'];
                                    $datainsert_items['order_date'] = $invoice_data['order_date'];
                                    $datainsert_items['item_code'] = $request->item_code;
                                    $datainsert_items['uom_id'] = $request->uom_id;
                                    $datainsert_items['transfer_from_batch_id'] = $request->inv_itemcard_batches_autoserial;
                                    $datainsert_items['deliverd_quantity'] = $request->item_quantity;
                                    $datainsert_items['unit_price'] = $request->item_price;
                                    $datainsert_items['total_price'] = $request->item_total;
                                    $datainsert_items['isparentuom'] = $request->isparentuom;
                                    $datainsert_items['item_card_type'] = $itemCard_Data['item_type'];
                                    $datainsert_items['production_date'] = $batch_data['production_date'];
                                    $datainsert_items['expire_date'] = $batch_data['expired_date'];
                                    $datainsert_items['added_by'] = auth()->user()->id;
                                    $datainsert_items['created_at'] = date("Y-m-d H:i:s");
                                    $datainsert_items['come_code'] = $come_code;
                                    $flag_datainsert_items = insert(new inv_stores_transfer_details(), $datainsert_items, true);
                                    if (!empty($flag_datainsert_items)) {
                                        $this->recalclate_parent_invoice($request->autoserailparent);
                                        //?????? ???????????? ???? ????????????
                                        //???????? ?????????? ?????? ?????????????? ?????? ????????????
                                        $quantityBeforMove = get_sum_where(
                                            new Inv_itemcard_batches(),
                                            "quantity",
                                            array(
                                                "item_code" => $request->item_code,
                                                "come_code" => $come_code
                                            )
                                        );
                                        //get Quantity Befor any Action  ?????????? ???????? ??????????  ?????????????? ???????????? ??????   ???????????? ?????? ????????????
                                        $quantityBeforMoveCurrntStore = get_sum_where(
                                            new Inv_itemcard_batches(),
                                            "quantity",
                                            array(
                                                "item_code" => $request->item_code, "come_code" => $come_code,
                                                'store_id' => $request->store_id
                                            )
                                        );
                                        //?????? ???????? ???????????? ?????????? ???? ???????? ??????????
                                        //update current Batch ?????????? ?????? ???????????? ??????????????
                                        if ($request->isparentuom == 1) {
                                            //???????? ???????? ?????????? ???????? ???????? ???????? ???????????? ????????
                                            $dataUpdateOldBatch['quantity'] = $batch_data['quantity'] - $request->item_quantity;
                                        } else {
                                            //???????? ?????????????? ?????????? ?????????????? ?????????? ???????????? ?????? ???????? ?????? ?????????? ?????????? !!
                                            $item_quantityByParentUom = $request->item_quantity / $itemCard_Data['retail_uom_quntToParent'];
                                            $dataUpdateOldBatch['quantity'] = $batch_data['quantity'] - $item_quantityByParentUom;
                                        }
                                        $dataUpdateOldBatch['total_cost_price'] = $batch_data['unit_cost_price'] * $dataUpdateOldBatch['quantity'];
                                        $dataUpdateOldBatch["updated_at"] = date("Y-m-d H:i:s");
                                        $dataUpdateOldBatch["updated_by"] = auth()->user()->id;
                                        $flag = update(new Inv_itemcard_batches(), $dataUpdateOldBatch, array("id" => $batch_data['id'], "come_code" => $come_code));
                                        if ($flag) {
                                            $quantityAfterMove = get_sum_where(
                                                new Inv_itemcard_batches(),
                                                "quantity",
                                                array(
                                                    "item_code" => $request->item_code,
                                                    "come_code" => $come_code
                                                )
                                            );
                                            //get Quantity Befor any Action  ?????????? ???????? ??????????  ?????????????? ???????????? ??????   ???????????? ?????? ????????????
                                            $quantityAfterMoveCurrentStore = get_sum_where(
                                                new Inv_itemcard_batches(),
                                                "quantity",
                                                array("item_code" => $request->item_code, "come_code" => $come_code, 'store_id' => $request->store_id)
                                            );
                                            //?????????????? ???? ???????? ???????? ??????????
                                            $dataInsert_inv_itemcard_movements['inv_itemcard_movements_categories'] = 3;
                                            $dataInsert_inv_itemcard_movements['items_movements_types'] = 20;
                                            $dataInsert_inv_itemcard_movements['item_code'] = $request->item_code;
                                            //?????? ???????????????? ????????
                                            $dataInsert_inv_itemcard_movements['FK_table'] = $request->autoserailparent;
                                            //?????? ???? ?????????? ?????????????? ????????????????
                                            $dataInsert_inv_itemcard_movements['FK_table_details'] = $flag_datainsert_items['id'];
                                            $to_store_name = Store::where('id', $invoice_data['transfer_to_store_id'])->value('name');
                                            $dataInsert_inv_itemcard_movements['byan'] = " ???????? ?????? ??????????  ?????? ???????? ???????????????? " . " " . $to_store_name . " ?????? ?????????? ??????" . " " . $request->autoserailparent;
                                            //???????? ?????????? ?????? ?????????????? ?????? ????????????
                                            $dataInsert_inv_itemcard_movements['quantity_befor_movement'] = "?????? " . " " . ($quantityBeforMove * 1) . " " . $MainUomName;
                                            // ???????? ?????????? ?????? ?????????????? ??????  ????????????
                                            $dataInsert_inv_itemcard_movements['quantity_after_move'] = "?????? " . " " . ($quantityAfterMove * 1) . " " . $MainUomName;
                                            //???????? ??????????  ???????????? ???????????? ?????? ????????????
                                            $dataInsert_inv_itemcard_movements['quantity_befor_move_store'] = "?????? " . " " . ($quantityBeforMoveCurrntStore * 1) . " " . $MainUomName;
                                            // ???????? ?????????? ?????????????? ???????????? ?????? ???????????? ????????????
                                            $dataInsert_inv_itemcard_movements['quantity_after_move_store'] = "?????? " . " " . ($quantityAfterMoveCurrentStore * 1) . " " . $MainUomName;
                                            $dataInsert_inv_itemcard_movements["store_id"] = $request->store_id;
                                            $dataInsert_inv_itemcard_movements["created_at"] = date("Y-m-d H:i:s");
                                            $dataInsert_inv_itemcard_movements["added_by"] = auth()->user()->id;
                                            $dataInsert_inv_itemcard_movements["date"] = date("Y-m-d");
                                            $dataInsert_inv_itemcard_movements["come_code"] = $come_code;
                                            $flag = insert(new Inv_itemcard_movements(), $dataInsert_inv_itemcard_movements);
                                            if ($flag) {
                                                //update itemcard Quantity mirror  ?????????? ???????????? ???????????????? ??????????
                                                do_update_itemCardQuantity(
                                                    new Inv_itemCard(),
                                                    $request->item_code,
                                                    new Inv_itemcard_batches(),
                                                    $itemCard_Data['does_has_retailunit'],
                                                    $itemCard_Data['retail_uom_quntToParent']
                                                );
                                                echo  json_encode("done");
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        } catch (\Exception $ex) {
            echo "there is error " . $ex->getMessage();
        }
    }
    function recalclate_parent_invoice($auto_serial)
    {
        $come_code = auth()->user()->come_code;
        $invoice_data = get_cols_where_row(new inv_stores_transfer(), array("auto_serial"), array("come_code" => $come_code, "auto_serial" => $auto_serial));
        if (!empty($invoice_data)) {
            //first get sum of details
            $dataUpdateParent['total_cost_items'] = get_sum_where(new inv_stores_transfer_details(), "total_price", array("come_code" => $come_code, "inv_stores_transfer_auto_serial" => $auto_serial));
            $dataUpdateParent['items_counter'] = get_sum_where(new inv_stores_transfer_details(), "deliverd_quantity", array("come_code" => $come_code, "inv_stores_transfer_auto_serial" => $auto_serial));
            $dataUpdateParent['updated_at'] = date("Y-m-d H:i:s");
            $dataUpdateParent['updated_by'] = auth()->user()->come_code;
            update(new inv_stores_transfer(), $dataUpdateParent, array("come_code" => $come_code, "auto_serial" => $auto_serial));
        }
    }
    public function reload_parent_pill(Request $request)
    {
        if ($request->ajax()) {
            $come_code = auth()->user()->come_code;
            $data = get_cols_where_row(new inv_stores_transfer(), array("*"), array("auto_serial" => $request->autoserailparent, "come_code" => $come_code));
            if (!empty($data)) {
                $data['added_by_admin'] = User::where('id', $data['added_by'])->value('name');
                $data['from_store_name'] = Store::where('id', $data['transfer_from_store_id'])->value('name');
                $data['to_store_name'] = Store::where('id', $data['transfer_to_store_id'])->value('name');
                $data['store_name'] = Store::where('id', $data['store_id'])->value('name');
                if ($data['updated_by'] > 0 and $data['updated_by'] != null) {
                    $data['updated_by_admin'] = User::where('id', $data['updated_by'])->value('name');
                }
                return view("admin.inv_stores_transfer.reload_parent_pill", ['data' => $data]);
            }
        }
    }
    public function reload_itemsdetials(Request $request)
    {
        if ($request->ajax()) {
            $come_code = auth()->user()->come_code;
            $auto_serial = $request->autoserailparent;
            $data = get_cols_where_row(new inv_stores_transfer(), array("is_approved", "id"), array("auto_serial" => $auto_serial, "come_code" => $come_code));
            $details = get_cols_where(new inv_stores_transfer_details(), array("*"), array('inv_stores_transfer_auto_serial' => $auto_serial, 'come_code' => $come_code), 'id', 'DESC');
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
            return view("admin.inv_stores_transfer.reload_itemsdetials", ['data' => $data, 'details' => $details]);
        }
    }
    public function delete_details($id, $parent_id)
    {
        try {
            $come_code = auth()->user()->come_code;
            $parent_pill_data = get_cols_where_row(new inv_stores_transfer(), array("is_approved", "auto_serial", "transfer_from_store_id", "transfer_to_store_id"), array("id" => $parent_id, "come_code" => $come_code));
            if (empty($parent_pill_data)) {
                return redirect()->back()
                    ->with(['error' => ' ???????? ?????? ?????? ????']);
            }
            if ($parent_pill_data['is_approved'] == 1) {
                if (empty($parent_pill_data)) {
                    return redirect()->back()
                        ->with(['error' => '????????  ???????????? ?????????? ?????????????? ???????????? ???????????? ??????????????']);
                }
            }
            $item_row = inv_stores_transfer_details::find($id);
            if (!empty($item_row)) {
                $flag = $item_row->delete();
                if ($flag) {
                    /** update parent pill */
                    $this->recalclate_parent_invoice($parent_pill_data['auto_serial']);
                    $itemCard_Data = get_cols_where_row(new Inv_itemCard(), array("uom_id", "retail_uom_quntToParent", "retail_uom_id", "does_has_retailunit", "item_type"), array("come_code" => $come_code, "item_code" => $item_row['item_code']));
                    $batch_data = get_cols_where_row(new Inv_itemcard_batches(), array("quantity", "unit_cost_price", "id", "production_date", "expired_date"), array("come_code" => $come_code, "auto_serial" => $item_row['transfer_from_batch_id'], 'store_id' => $parent_pill_data['transfer_from_store_id'], 'item_code' => $item_row['item_code']));
                    if (!empty($itemCard_Data) and !empty($batch_data)) {
                        //?????? ???????????? ???? ????????????
                        //???????? ?????????? ?????? ?????????????? ?????? ????????????
                        $quantityBeforMove = get_sum_where(
                            new Inv_itemcard_batches(),
                            "quantity",
                            array(
                                "item_code" => $item_row['item_code'],
                                "come_code" => $come_code
                            )
                        );
                        //get Quantity Befor any Action  ?????????? ???????? ??????????  ?????????????? ???????????? ??????   ???????????? ?????? ????????????
                        $quantityBeforMoveCurrntStore = get_sum_where(
                            new Inv_itemcard_batches(),
                            "quantity",
                            array(
                                "item_code" => $item_row['item_code'], "come_code" => $come_code,
                                'store_id' => $parent_pill_data['transfer_from_store_id']
                            )
                        );
                        //??????????  ???????????? ?????????? ???? ???????? ??????????
                        //update current Batch ?????????? ?????? ???????????? ??????????????
                        if ($item_row['isparentuom'] == 1) {
                            //?????????? ???????? ?????????? ???????? ???????? ???????? ???????????? ????????
                            $dataUpdateOldBatch['quantity'] = $batch_data['quantity'] + $item_row['deliverd_quantity'];
                        } else {
                            //???????? ?????????????? ?????????? ?????????????? ?????????? ???????????? ?????? ???????? ?????? ?????????? ?????????? !!
                            $item_quantityByParentUom = $item_row['deliverd_quantity'] / $itemCard_Data['retail_uom_quntToParent'];
                            $dataUpdateOldBatch['quantity'] = $batch_data['quantity'] + $item_quantityByParentUom;
                        }
                        $dataUpdateOldBatch['total_cost_price'] = $batch_data['unit_cost_price'] * $dataUpdateOldBatch['quantity'];
                        $dataUpdateOldBatch["updated_at"] = date("Y-m-d H:i:s");
                        $dataUpdateOldBatch["updated_by"] = auth()->user()->id;
                        $flag = update(new Inv_itemcard_batches(), $dataUpdateOldBatch, array("id" => $batch_data['id'], "come_code" => $come_code));
                        if ($flag) {
                            $quantityAfterMove = get_sum_where(
                                new Inv_itemcard_batches(),
                                "quantity",
                                array(
                                    "item_code" => $item_row['item_code'],
                                    "come_code" => $come_code
                                )
                            );
                            //get Quantity Befor any Action  ?????????? ???????? ??????????  ?????????????? ???????????? ??????   ???????????? ?????? ????????????
                            $quantityAfterMoveCurrentStore = get_sum_where(
                                new Inv_itemcard_batches(),
                                "quantity",
                                array("item_code" => $item_row['item_code'], "come_code" => $come_code, 'store_id' => $parent_pill_data['transfer_from_store_id'])
                            );
                            //?????????????? ???? ???????? ???????? ??????????
                            $dataInsert_inv_itemcard_movements['inv_itemcard_movements_categories'] = 3;
                            $dataInsert_inv_itemcard_movements['items_movements_types'] = 21;
                            $dataInsert_inv_itemcard_movements['item_code'] = $item_row['item_code'];
                            //?????? ???????????????? ????????
                            $dataInsert_inv_itemcard_movements['FK_table'] = $parent_pill_data['auto_serial'];
                            //?????? ???? ?????????? ?????????????? ????????????????
                            $dataInsert_inv_itemcard_movements['FK_table_details'] = $item_row['id'];
                            $to_store_name = Store::where('id', $parent_pill_data['transfer_to_store_id'])->value('name');
                            $from_store_name = Store::where('id', $parent_pill_data['transfer_from_store_id'])->value('name');
                            $dataInsert_inv_itemcard_movements['byan'] = " ???????? ?????? ?????? ?????????? ???? ?????? ?????????? ???? ????????????            " . " " . $from_store_name . "  ?????? ????????????" . $to_store_name . " ?????? ??????????" . " " . $parent_pill_data['auto_serial'];
                            $MainUomName = get_field_value(new Inv_uom(), "name", array("come_code" => $come_code, "id" => $itemCard_Data['uom_id']));
                            //???????? ?????????? ?????? ?????????????? ?????? ????????????
                            $dataInsert_inv_itemcard_movements['quantity_befor_movement'] = "?????? " . " " . ($quantityBeforMove * 1) . " " . $MainUomName;
                            // ???????? ?????????? ?????? ?????????????? ??????  ????????????
                            $dataInsert_inv_itemcard_movements['quantity_after_move'] = "?????? " . " " . ($quantityAfterMove * 1) . " " . $MainUomName;
                            //???????? ??????????  ???????????? ???????????? ?????? ????????????
                            $dataInsert_inv_itemcard_movements['quantity_befor_move_store'] = "?????? " . " " . ($quantityBeforMoveCurrntStore * 1) . " " . $MainUomName;
                            // ???????? ?????????? ?????????????? ???????????? ?????? ???????????? ????????????
                            $dataInsert_inv_itemcard_movements['quantity_after_move_store'] = "?????? " . " " . ($quantityAfterMoveCurrentStore * 1) . " " . $MainUomName;
                            $dataInsert_inv_itemcard_movements["store_id"] = $parent_pill_data['transfer_from_store_id'];
                            $dataInsert_inv_itemcard_movements["created_at"] = date("Y-m-d H:i:s");
                            $dataInsert_inv_itemcard_movements["added_by"] = auth()->user()->id;
                            $dataInsert_inv_itemcard_movements["date"] = date("Y-m-d");
                            $dataInsert_inv_itemcard_movements["come_code"] = $come_code;
                            $flag = insert(new Inv_itemcard_movements(), $dataInsert_inv_itemcard_movements);
                            if ($flag) {
                                //update itemcard Quantity mirror  ?????????? ???????????? ???????????????? ??????????
                                do_update_itemCardQuantity(
                                    new Inv_itemCard(),
                                    $item_row['item_code'],
                                    new Inv_itemcard_batches(),
                                    $itemCard_Data['does_has_retailunit'],
                                    $itemCard_Data['retail_uom_quntToParent']
                                );
                                return redirect()->back()
                                    ->with(['success' => '   ???? ?????? ???????????????? ??????????']);
                            }
                        } else {
                            return redirect()->back()
                                ->with(['error' => '2???????? ?????? ?????? ????']);
                        }
                    } else {
                        return redirect()->back()
                            ->with(['error' => '3???????? ?????? ?????? ????']);
                    }
                } else {
                    return redirect()->back()
                        ->with(['error' => '4???????? ?????? ?????? ????']);
                }
            } else {
                return redirect()->back()
                    ->with(['error' => '???????? ?????? ???????? ?????? ???????????? ???????????????? ????????????????']);
            }
        } catch (\Exception $ex) {
            return redirect()->back()
                ->with(['error' => '5???????? ?????? ?????? ????' . $ex->getMessage()]);
        }
    }
}
