<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Inv_stores_inventoryRequest;
use App\Models\Admin_panel_setting;
use App\Models\Inv_itemCard;
use App\Models\Inv_itemcard_batches;
use App\Models\Inv_itemcard_movements;
use App\Models\Inv_stores_inventory;
use App\Models\Inv_stores_inventory_details;
use App\Models\Inv_uom;
use App\Models\Store;
use App\Models\User;
use Illuminate\Http\Request;

class Inv_stores_inventoryController extends Controller
{
    public function index()
    {
        $come_code = auth()->user()->come_code;
        $data = get_cols_where_p(new Inv_stores_inventory(), array("*"), array("come_code" => $come_code), 'id', 'DESC', 10);
        if (!empty($data)) {
            foreach ($data as $info) {
                $info->added_by_admin = User::where('id', $info->added_by)->value('name');
                if ($info->updated_by > 0 and $info->updated_by != null) {
                    $info->updated_by_admin = User::where('id', $info->updated_by)->value('name');
                    $info->store_name = Store::where('id', $info->store_id)->value('name');
                }
            }
        }
        $stores = get_cols_where(new Store(), array('id', 'name'), array('come_code' => $come_code, 'active' => 1), 'id', 'ASC');
        return view('admin.inv_stores_inventory.index', ['data' => $data, 'stores' => $stores]);
    }
    public function create()
    {
        $come_code = auth()->user()->come_code;
        $stores = get_cols_where(new Store(), array('id', 'name'), array('come_code' => $come_code, 'active' => 1), 'id', 'ASC');
        return view('admin.inv_stores_inventory.create', ['stores' => $stores]);
    }
    public function store(Inv_stores_inventoryRequest $request)
    {
        try {
            $come_code = auth()->user()->come_code;
            $counter_opnened_for_store = get_count_where(new Inv_stores_inventory(), array("come_code" => $come_code, 'store_id' => $request->store_id, 'is_closed' => 0));
            if ($counter_opnened_for_store > 0) {
                return redirect()->back()
                    ->with(['error' => '???????? ???????? ???????????? ?????? ?????? ?????????? ???????? ????????????'])
                    ->withInput();
            }
            $row = get_cols_where_row_orderby(new Inv_stores_inventory(), array("auto_serial"), array("come_code" => $come_code), 'id', 'DESC');
            if (!empty($row)) {
                $data_insert['auto_serial'] = $row['auto_serial'] + 1;
            } else {
                $data_insert['auto_serial'] = 1;
            }
            $data_insert['inventory_date'] = $request->inventory_date;
            $data_insert['inventory_type'] = $request->inventory_type;
            $data_insert['store_id'] = $request->store_id;
            $data_insert['notes'] = $request->notes;
            $data_insert['added_by'] = auth()->user()->id;
            $data_insert['created_at'] = date("Y-m-d H:i:s");
            $data_insert['date'] = date("Y-m-d");
            $data_insert['come_code'] = $come_code;
            insert(new Inv_stores_inventory(), $data_insert);
            return redirect()->route("admin.stores_inventory.index")->with(['success' => '?????? ???? ?????????? ???????????????? ??????????']);
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
            $data = get_cols_where_row(new Inv_stores_inventory(), array("*"), array("id" => $id, "come_code" => $come_code));
            if (empty($data)) {
                return redirect()->route('admin.inv_stores_inventory.index')->with(['error' => '???????? ?????? ???????? ?????? ???????????? ?????? ???????????????? ???????????????? !!']);
            }
            if ($data['is_closed'] == 1) {
                return redirect()->route('admin.stores_inventory.index')->with(['error' => '???????? ???????????? ?????????????? ?????? ?????? ?????? ???????? ??????????  ']);
            }
            $counterAddedDetails = get_count_where(new Inv_stores_inventory_details(), array("inv_stores_inventory_auto_serial" => $data['auto_serial'], 'come_code' => $come_code, 'is_closed' => 1));
            if ($counterAddedDetails > 0) {
                return redirect()->route('admin.stores_inventory.index')->with(['error' => '???????? ???????????? ??????  ?????? ?????? ???? ???? ???????????? ?????????? ????????  ']);
            }
            $flag = delete(new Inv_stores_inventory(), array("id" => $id, "come_code" => $come_code));
            if ($flag) {
                delete(new Inv_stores_inventory_details(), array("inv_stores_inventory_auto_serial" => $data['auto_serial'], "come_code" => $come_code));
                return redirect()->route('admin.stores_inventory.index')->with(['success' => '?????? ???? ??????  ???????????????? ??????????']);
            }
        } catch (\Exception $ex) {
            return redirect()->back()
                ->with(['error' => '???????? ?????? ?????? ????' . $ex->getMessage()]);
        }
    }
    public function edit($id)
    {
        $come_code = auth()->user()->come_code;
        $data = get_cols_where_row(new Inv_stores_inventory(), array("*"), array("id" => $id, "come_code" => $come_code));
        if (empty($data)) {
            return redirect()->route('admin.stores_inventory.index')->with(['error' => '???????? ?????? ???????? ?????? ???????????? ?????? ???????????????? ???????????????? !!']);
        }
        if ($data['is_closed'] == 1) {
            return redirect()->route('admin.stores_inventory.index')->with(['error' => '???????? ???????????? ?????????????? ?????? ?????? ?????? ???????? ??????????  ']);
        }
        $counterAddedDetails = get_count_where(new Inv_stores_inventory_details(), array("inv_stores_inventory_auto_serial" => $data['auto_serial'], 'come_code' => $come_code));
        if ($counterAddedDetails == 0) {
            $stores = get_cols_where(new Store(), array('id', 'name'), array('come_code' => $come_code, 'active' => 1), 'id', 'ASC');
        } else {
            $stores = get_cols_where(new Store(), array('id', 'name'), array('come_code' => $come_code, 'id' => $data['store_id']), 'id', 'ASC');
        }
        return view('admin.inv_stores_inventory.edit', ['data' => $data, 'stores' => $stores]);
    }
    public function update($id, Inv_stores_inventoryRequest $request)
    {
        try {
            $come_code = auth()->user()->come_code;
            $data = get_cols_where_row(new Inv_stores_inventory(), array("*"), array("id" => $id, "come_code" => $come_code));
            if (empty($data)) {
                return redirect()->route('admin.inv_stores_inventory.index')->with(['error' => '???????? ?????? ???????? ?????? ???????????? ?????? ???????????????? ???????????????? !!']);
            }
            if ($data['is_closed'] == 1) {
                return redirect()->route('admin.stores_inventory.index')->with(['error' => '???????? ???????????? ?????????????? ?????? ?????? ?????? ???????? ??????????  ']);
            }
            $counterSameStoreOpened = Inv_stores_inventory::where('id', '!=', $id)->where('come_code', '=', $come_code)->where('store_id', '=', $request->store_id)->count();
            if ($counterSameStoreOpened > 0) {
                return redirect()->back()
                    ->with(['error' => '????????  ???????? ?????? ?????? ?????????? ?????????? ???? ?????? ???????????? '])
                    ->withInput();
            }
            $counterAddedDetails = get_count_where(new Inv_stores_inventory_details(), array("inv_stores_inventory_auto_serial" => $data['auto_serial'], 'come_code' => $come_code));
            if ($counterAddedDetails == 0) {
                $data_to_update['store_id'] = $request->store_id;
            }
            $data_to_update['inventory_date'] = $request->inventory_date;
            $data_to_update['inventory_type'] = $request->inventory_type;
            $data_to_update['notes'] = $request->notes;
            $data_to_update['updated_by'] = auth()->user()->id;
            $data_to_update['updated_at'] = date("Y-m-d H:i:s");
            update(new Inv_stores_inventory(), $data_to_update, array("id" => $id, "come_code" => $come_code));
            return redirect()->route('admin.stores_inventory.index')->with(['success' => '?????? ???? ?????????? ???????????????? ??????????']);
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
            $data = get_cols_where_row(new Inv_stores_inventory(), array("*"), array("id" => $id, "come_code" => $come_code));
            if (empty($data)) {
                return redirect()->route('admin.stores_inventory.index')->with(['error' => '???????? ?????? ???????? ?????? ???????????? ?????? ???????????????? ???????????????? !!']);
            }
            $data['added_by_admin'] = User::where('id', $data['added_by'])->value('name');
            if ($data['updated_by'] > 0 and $data['updated_by'] != null) {
                $data['updated_by_admin'] = User::where('id', $data['updated_by'])->value('name');
            }
            $details = get_cols_where(new inv_stores_inventory_details(), array("*"), array('inv_stores_inventory_auto_serial' => $data['auto_serial'], 'come_code' => $come_code), 'id', 'DESC');
            if (!empty($details)) {
                foreach ($details as $info) {
                    $info->item_name = Inv_itemCard::where('item_code', $info->item_code)->value('name');
                    $info->item_type = Inv_itemCard::where('item_code', $info->item_code)->value('item_type');
                    $data['added_by_admin'] = User::where('id', $info->added_by)->value('name');
                    if ($info->updated_by > 0 and $info->updated_by != null) {
                        $data['updated_by_admin'] = User::where('id', $info->updated_by)->value('name');
                    }
                }
            }
            if ($data['is_closed'] == 0) {
                $items_in_store = Inv_itemcard_batches::where("come_code", "=", $come_code)->where("store_id", "=", $data['store_id'])->orderby('item_code', 'ASC')->distinct()->get(['item_code']);
                if (!empty($items_in_store)) {
                    foreach ($items_in_store as $info) {
                        $info->name = Inv_itemCard::where('item_code', $info->item_code)->value('name');
                    }
                }
            } else {
                $items_in_store = "";
            }
            return view("admin.inv_stores_inventory.show", ['data' => $data, 'details' => $details, 'items_in_store' => $items_in_store]);
        } catch (\Exception $ex) {
            return redirect()->back()
                ->with(['error' => '???????? ?????? ?????? ????' . $ex->getMessage()]);
        }
    }
    public function add_new_details($id, Request $request)
    {
        if ($_POST) {
            $come_code = auth()->user()->come_code;
            $data = get_cols_where_row(new Inv_stores_inventory(), array("*"), array("id" => $id, "come_code" => $come_code));
            if (empty($data)) {
                return redirect()->route('admin.stores_inventory.index')->with(['error' => '???????? ?????? ???????? ?????? ???????????? ?????? ???????????????? ???????????????? !!']);
            }
            if ($data['is_closed'] == 1) {
                return redirect()->route('admin.stores_inventory.show', $id)->with(['error' => '???????? ???????????? ?????????????? ?????? ?????? ?????? ???????? ?????????? !']);
            }
            if ($_POST['does_add_all_items'] == 1) {
                $items_in_store = Inv_itemcard_batches::where("come_code", "=", $come_code)->where("store_id", "=", $data['store_id'])->orderby('item_code', 'ASC')->distinct()->get(['item_code']);
            } else {
                $items_in_store = Inv_itemcard_batches::where("come_code", "=", $come_code)->where("store_id", "=", $data['store_id'])->where('item_code', '=', $_POST['items_in_store'])->orderby('item_code', 'ASC')->distinct()->get(['item_code']);
            }
            if (!empty($items_in_store)) {
                foreach ($items_in_store as $info) {
                    if ($_POST['dose_enter_empty_batch'] == 1) {
                        $info->Batches = Inv_itemcard_batches::select("*")->where("come_code", '=', $come_code)->where('item_code', '=', $info->item_code)->where('store_id', '=', $data['store_id'])->get();
                    } else {
                        $info->Batches = Inv_itemcard_batches::select("*")->where("come_code", '=', $come_code)->where('item_code', '=', $info->item_code)->where('store_id', '=', $data['store_id'])->where('quantity', '>', 0);
                    }
                    if (!empty($info->Batches)) {
                        foreach ($info->Batches as $batch) {
                            $counter = get_count_where(new Inv_stores_inventory_details(), array("come_code" => $come_code, "inv_stores_inventory_auto_serial" => $data['auto_serial'], 'batch_auto_serial' => $batch->auto_serial, 'item_code' => $batch->item_code));
                            if ($counter == 0) {
                                $data_insert['inv_stores_inventory_auto_serial'] = $data['auto_serial'];
                                $data_insert['batch_auto_serial'] = $batch->auto_serial;
                                $data_insert['item_code'] = $batch->item_code;
                                $data_insert['inv_uoms_id'] = $batch->inv_uoms_id;
                                $data_insert['unit_cost_price'] = $batch->unit_cost_price;
                                $data_insert['old_quantity'] = $batch->quantity;
                                $data_insert['new_quantity'] = $batch->quantity;
                                $data_insert['total_cost_price'] = $batch->total_cost_price;
                                $data_insert['production_date'] = $batch->production_date;
                                $data_insert['expired_date'] = $batch->expired_date;
                                $data_insert['added_by'] = auth()->user()->id;
                                $data_insert['created_at'] = date("Y-m-d H:i:s");
                                $data_insert['date'] = date("Y-m-d");
                                $data_insert['come_code'] = $come_code;
                                $flag = insert(new Inv_stores_inventory_details(), $data_insert);
                            }
                        }
                    }
                }
            }
            $data_to_update_parent['total_cost_batches'] = get_sum_where(new Inv_stores_inventory_details(), 'total_cost_price', array("come_code" => $come_code, 'inv_stores_inventory_auto_serial' => $data['auto_serial']));
            update(new Inv_stores_inventory(), $data_to_update_parent, array("come_code" => $come_code, "id" => $id, 'is_closed' => 0));
        }
        return redirect()->route('admin.stores_inventory.show', $id)->with(['success' => '???? ?????????? ???????????????? ??????????']);
    }
    public function load_edit_item_details(Request $request)
    {
        if ($request->ajax()) {
            $come_code = auth()->user()->come_code;
            $parent_pill_data = get_cols_where_row(new Inv_stores_inventory(), array("*"), array("id" => $request->id_parent_pill, "come_code" => $come_code));
            if (!empty($parent_pill_data)) {
                if ($parent_pill_data['is_closed'] == 0) {
                    $item_data_detials = get_cols_where_row(new Inv_stores_inventory_details(), array("*"), array("inv_stores_inventory_auto_serial" => $parent_pill_data['auto_serial'], "come_code" => $come_code, 'id' => $request->id));
                    return view("admin.inv_stores_inventory.load_edit_item_details", ['parent_pill_data' => $parent_pill_data, 'item_data_detials' => $item_data_detials]);
                }
            }
        }
    }
    public function edit_item_details($id, $parent_pill_id, Request $request)
    {
        if ($_POST) {
            $come_code = auth()->user()->come_code;
            $data = get_cols_where_row(new Inv_stores_inventory(), array("*"), array("id" => $parent_pill_id, "come_code" => $come_code));
            if (empty($data)) {
                return redirect()->route('admin.stores_inventory.index')->with(['error' => '???????? ?????? ???????? ?????? ???????????? ?????? ???????????????? ???????????????? !!']);
            }
            if ($data['is_closed'] == 1) {
                return redirect()->route('admin.stores_inventory.show', $parent_pill_id)->with(['error' => '???????? ???????????? ?????????????? ?????? ?????? ?????? ???????? ?????????? !']);
            }
            $dataDetails = get_cols_where_row(new Inv_stores_inventory_details(), array("*"), array("id" => $id, "come_code" => $come_code, 'inv_stores_inventory_auto_serial' => $data['auto_serial']));
            if (empty($dataDetails)) {
                return redirect()->route('admin.stores_inventory.show', $parent_pill_id)->with(['error' => '???????? ?????? ???????? ?????? ???????????? ?????? ???????????????? ???????????????? !!']);
            }
            if ($dataDetails['is_closed'] == 1) {
                return redirect()->route('admin.stores_inventory.show', $parent_pill_id)->with(['error' => '???????? ???????????? ?????????? ?????? ?????? ?????? ???????? ?????????? !']);
            }
            $dataUpdateDetails['new_quantity'] = $request->new_quantity_edit;
            $dataUpdateDetails['diffrent_quantity'] = ($request->new_quantity_edit - $dataDetails['old_quantity']);
            $dataUpdateDetails['total_cost_price'] = ($request->new_quantity_edit * $dataDetails['unit_cost_price']);
            $dataUpdateDetails['notes'] = $request->notes_edit;
            $dataUpdateDetails['updated_by'] = auth()->user()->id;
            $dataUpdateDetails['updated_at'] = date("Y-m-d H:i:s");
            update(new Inv_stores_inventory_details(), $dataUpdateDetails, array("come_code" => $come_code, "id" => $id, 'is_closed' => 0, 'inv_stores_inventory_auto_serial' => $data['auto_serial']));
            $data_to_update_parent['total_cost_batches'] = get_sum_where(new Inv_stores_inventory_details(), 'total_cost_price', array("come_code" => $come_code, 'inv_stores_inventory_auto_serial' => $data['auto_serial']));
            update(new Inv_stores_inventory(), $data_to_update_parent, array("come_code" => $come_code, "id" => $parent_pill_id, 'is_closed' => 0));
        }
        return redirect()->route('admin.stores_inventory.show', $parent_pill_id)->with(['success' => '???? ?????????? ???????????????? ??????????']);
    }
    public function delete_details($id, $id_parent)
    {
        try {
            $come_code = auth()->user()->come_code;
            $data_parent = get_cols_where_row(new Inv_stores_inventory(), array("is_closed", "auto_serial"), array("id" => $id_parent, "come_code" => $come_code));
            if (empty($data_parent)) {
                return redirect()->route('admin.stores_inventory.index')->with(['error' => '???????? ?????? ???????? ?????? ???????????? ?????? ???????????????? ???????????????? !!']);
            }
            if ($data_parent['is_closed'] == 1) {
                return redirect()->route('admin.stores_inventory.show', $id_parent)->with(['error' => '???????? ???????????? ?????????????? ?????? ?????? ?????? ???????? ??????????  ']);
            }
            $Data_item_details = get_cols_where_row(new Inv_stores_inventory_details(), array("is_closed"), array("id" => $id, 'come_code' => $come_code, 'inv_stores_inventory_auto_serial' => $data_parent['auto_serial']));
            if (empty($Data_item_details)) {
                return redirect()->route('admin.stores_inventory.show', $id_parent)->with(['error' => '???????? ?????? ???????? ?????? ???????????? ?????? ???????????????? ???????????????? !!']);
            }
            if ($Data_item_details['is_closed'] == 1) {
                return redirect()->route('admin.stores_inventory.show', $id_parent)->with(['error' => '???????? ???????????? ?????????????? ?????? ?????? ?????? ???????? ???????? ??????????  ']);
            }
            $flag = delete(new Inv_stores_inventory_details(), array("id" => $id, 'come_code' => $come_code, 'inv_stores_inventory_auto_serial' => $data_parent['auto_serial'], 'is_closed' => 0));
            if ($flag) {
                $data_to_update_parent['total_cost_batches'] = get_sum_where(new Inv_stores_inventory_details(), 'total_cost_price', array("come_code" => $come_code, 'inv_stores_inventory_auto_serial' => $data_parent['auto_serial']));
                update(new Inv_stores_inventory(), $data_to_update_parent, array("come_code" => $come_code, "id" => $id_parent, 'is_closed' => 0));
                return redirect()->route('admin.stores_inventory.show', $id_parent)->with(['success' => '?????? ???? ??????  ???????????????? ??????????']);
            }
        } catch (\Exception $ex) {
            return redirect()->back()
                ->with(['error' => '???????? ?????? ?????? ????' . $ex->getMessage()]);
        }
    }
    public function close_one_details($id, $id_parent)
    {
        try {
            $come_code = auth()->user()->come_code;
            $data_parent = get_cols_where_row(new Inv_stores_inventory(), array("is_closed", "auto_serial", "store_id"), array("id" => $id_parent, "come_code" => $come_code));
            if (empty($data_parent)) {
                return redirect()->route('admin.stores_inventory.index')->with(['error' => '???????? ?????? ???????? ?????? ???????????? ?????? ???????????????? ???????????????? !!']);
            }
            if ($data_parent['is_closed'] == 1) {
                return redirect()->route('admin.stores_inventory.show', $id_parent)->with(['error' => '???????? ???????????? ?????????????? ?????? ?????? ?????? ???????? ??????????  ']);
            }
            $Data_item_details = get_cols_where_row(new Inv_stores_inventory_details(), array("*"), array("id" => $id, 'come_code' => $come_code, 'inv_stores_inventory_auto_serial' => $data_parent['auto_serial']));
            if (empty($Data_item_details)) {
                return redirect()->route('admin.stores_inventory.show', $id_parent)->with(['error' => '???????? ?????? ???????? ?????? ???????????? ?????? ???????????????? ???????????????? !!']);
            }
            if ($Data_item_details['is_closed'] == 1) {
                return redirect()->route('admin.stores_inventory.show', $id_parent)->with(['error' => '???????? ???????????? ?????????????? ?????? ?????? ?????? ???????? ???????? ??????????  ']);
            }
            //first we update Old pathc with new quantity
            //???????? ?????????? ?????? ?????????????? ?????? ????????????
            $quantityBeforMove = get_sum_where(
                new Inv_itemcard_batches(),
                "quantity",
                array(
                    "item_code" => $Data_item_details['item_code'], "come_code" => $come_code
                )
            );
            //get Quantity Befor any Action  ?????????? ???????? ??????????  ?????????????? ???????????? ??????   ???????????? ?????? ????????????
            $quantityBeforMoveCurrntStore = get_sum_where(
                new Inv_itemcard_batches(),
                "quantity",
                array(
                    "item_code" => $Data_item_details['item_code'], "come_code" => $come_code,
                    'store_id' => $data_parent['store_id']
                )
            );
            $dataUpdateBatch['quantity'] = $Data_item_details['new_quantity'];
            $dataUpdateBatch['total_cost_price'] = $Data_item_details['total_cost_price'];
            $dataUpdateBatch['updated_by'] = auth()->user()->id;
            $dataUpdateBatch['updated_at'] = date("Y-m-d H:i:s");
            $flag = update(new Inv_itemcard_batches(), $dataUpdateBatch, array("come_code" => $come_code, 'auto_serial' => $Data_item_details['batch_auto_serial'], 'item_code' => $Data_item_details['item_code']));
            if ($flag) {
                $dataUpdatedetails['is_closed'] = 1;
                $dataUpdatedetails['cloased_by'] = auth()->user()->id;
                $dataUpdatedetails['closed_at'] = date("Y-m-d H:i:s");
                $flag = update(new Inv_stores_inventory_details(), $dataUpdatedetails, array("id" => $id, 'come_code' => $come_code, 'inv_stores_inventory_auto_serial' => $data_parent['auto_serial']));
                if ($flag) {
                    //???????? ?????????? ?????? ?????????????? ?????? ????????????
                    $quantityAfterMove = get_sum_where(
                        new Inv_itemcard_batches(),
                        "quantity",
                        array(
                            "item_code" => $Data_item_details['item_code'], "come_code" => $come_code
                        )
                    );
                    //???????? ??????????  ?????????????? ???????????? ?????? ????????????
                    $quantityAfterMoveCurrentStore = get_sum_where(
                        new Inv_itemcard_batches(),
                        "quantity",
                        array(
                            "item_code" => $Data_item_details['item_code'], "come_code" => $come_code,
                            'store_id' => $data_parent['store_id']
                        )
                    );
                    $MainUomName = get_field_value(new Inv_uom(), "name", array("come_code" => $come_code, "id" => $Data_item_details['inv_uoms_id']));
                    $itemCard_Data = get_cols_where_row(new Inv_itemCard(), array("uom_id", "retail_uom_quntToParent", "retail_uom_id", "does_has_retailunit"), array("come_code" => $come_code, "item_code" => $Data_item_details['item_code']));
                    //?????????????? ???? ???????? ???????? ??????????
                    $dataInsert_inv_itemcard_movements['inv_itemcard_movements_categories'] = 3;
                    $dataInsert_inv_itemcard_movements['items_movements_types'] = 6;
                    $dataInsert_inv_itemcard_movements['item_code'] = $Data_item_details['item_code'];
                    //?????? ???????????????? ????????
                    $dataInsert_inv_itemcard_movements['FK_table'] = $data_parent['auto_serial'];
                    //?????? ???? ?????????? ?????????????? ????????????????
                    $dataInsert_inv_itemcard_movements['FK_table_details'] = $Data_item_details['id'];
                    $dataInsert_inv_itemcard_movements['byan'] = "?????? ???????????????? ???????????? ??????" . " " . $Data_item_details['batch_auto_serial'] . " ?????? ??????" . " " . $data_parent['auto_serial'];
                    //???????? ?????????? ?????? ?????????????? ?????? ????????????
                    $dataInsert_inv_itemcard_movements['quantity_befor_movement'] = "?????? " . " " . ($quantityBeforMove * 1) . " " . $MainUomName;
                    // ???????? ?????????? ?????? ?????????????? ??????  ????????????
                    $dataInsert_inv_itemcard_movements['quantity_after_move'] = "?????? " . " " . ($quantityAfterMove * 1) . " " . $MainUomName;
                    //???????? ??????????  ???????????? ???????????? ?????? ????????????
                    $dataInsert_inv_itemcard_movements['quantity_befor_move_store'] = "?????? " . " " . ($quantityBeforMoveCurrntStore * 1) . " " . $MainUomName;
                    // ???????? ?????????? ?????????????? ???????????? ?????? ???????????? ????????????
                    $dataInsert_inv_itemcard_movements['quantity_after_move_store'] = "?????? " . " " . ($quantityAfterMoveCurrentStore * 1) . " " . $MainUomName;
                    $dataInsert_inv_itemcard_movements["store_id"] = $data_parent['store_id'];
                    $dataInsert_inv_itemcard_movements["created_at"] = date("Y-m-d H:i:s");
                    $dataInsert_inv_itemcard_movements["added_by"] = auth()->user()->id;
                    $dataInsert_inv_itemcard_movements["date"] = date("Y-m-d");
                    $dataInsert_inv_itemcard_movements["come_code"] = $come_code;
                    $flag = insert(new Inv_itemcard_movements(), $dataInsert_inv_itemcard_movements);
                    if ($flag) {
                        //update itemcard Quantity mirror  ?????????? ???????????? ???????????????? ??????????
                        do_update_itemCardQuantity(
                            new Inv_itemCard(),
                            $Data_item_details['item_code'],
                            new Inv_itemcard_batches(),
                            $itemCard_Data['does_has_retailunit'],
                            $itemCard_Data['retail_uom_quntToParent']
                        );
                    }
                }
            }
            return redirect()->route('admin.stores_inventory.show', $id_parent)->with(['success' => '?????? ???? ?????????? ???????????? ??????????']);
        } catch (\Exception $ex) {
            return redirect()->back()
                ->with(['error' => '???????? ?????? ?????? ????' . $ex->getMessage()]);
        }
    }
    public function do_close_parent($id)
    {
        try {
            $come_code = auth()->user()->come_code;
            $data_parent = get_cols_where_row(new Inv_stores_inventory(), array("*"), array("id" => $id, "come_code" => $come_code, 'is_closed' => 0));
            if (empty($data_parent)) {
                return redirect()->route('admin.stores_inventory.index')->with(['error' => '???????? ?????? ???????? ?????? ???????????? ?????? ???????????????? ???????????????? !!']);
            }
            if ($data_parent['is_closed'] == 1) {
                return redirect()->route('admin.stores_inventory.index')->with(['error' => '???????? ???????????? ?????????? ?????? ?????? ???? ???? ???????????? ???? ??????   ']);
            }
            $details = get_cols_where(new inv_stores_inventory_details(), array("id"), array('inv_stores_inventory_auto_serial' => $data_parent['auto_serial'], 'come_code' => $come_code, 'is_closed' => 0), 'id', 'ASC');
            if (!empty($details)) {
                foreach ($details as $info) {
                    $Data_item_details = get_cols_where_row(new Inv_stores_inventory_details(), array("*"), array("id" => $info->id, 'come_code' => $come_code, 'inv_stores_inventory_auto_serial' => $data_parent['auto_serial']));
                    if (!empty($Data_item_details)) {
                        //first we update Old pathc with new quantity
                        //???????? ?????????? ?????? ?????????????? ?????? ????????????
                        $quantityBeforMove = get_sum_where(
                            new Inv_itemcard_batches(),
                            "quantity",
                            array(
                                "item_code" => $Data_item_details['item_code'], "come_code" => $come_code
                            )
                        );
                        //get Quantity Befor any Action  ?????????? ???????? ??????????  ?????????????? ???????????? ??????   ???????????? ?????? ????????????
                        $quantityBeforMoveCurrntStore = get_sum_where(
                            new Inv_itemcard_batches(),
                            "quantity",
                            array(
                                "item_code" => $Data_item_details['item_code'], "come_code" => $come_code,
                                'store_id' => $data_parent['store_id']
                            )
                        );
                        $dataUpdateBatch['quantity'] = $Data_item_details['new_quantity'];
                        $dataUpdateBatch['total_cost_price'] = $Data_item_details['total_cost_price'];
                        $dataUpdateBatch['updated_by'] = auth()->user()->id;
                        $dataUpdateBatch['updated_at'] = date("Y-m-d H:i:s");
                        $flag = update(new Inv_itemcard_batches(), $dataUpdateBatch, array("come_code" => $come_code, 'auto_serial' => $Data_item_details['batch_auto_serial'], 'item_code' => $Data_item_details['item_code']));
                        if ($flag) {
                            $dataUpdatedetails['is_closed'] = 1;
                            $dataUpdatedetails['cloased_by'] = auth()->user()->id;
                            $dataUpdatedetails['closed_at'] = date("Y-m-d H:i:s");
                            $flag = update(new Inv_stores_inventory_details(), $dataUpdatedetails, array("id" => $id, 'come_code' => $come_code, 'inv_stores_inventory_auto_serial' => $data_parent['auto_serial']));
                            if ($flag) {
                                //???????? ?????????? ?????? ?????????????? ?????? ????????????
                                $quantityAfterMove = get_sum_where(
                                    new Inv_itemcard_batches(),
                                    "quantity",
                                    array(
                                        "item_code" => $Data_item_details['item_code'], "come_code" => $come_code
                                    )
                                );
                                //???????? ??????????  ?????????????? ???????????? ?????? ????????????
                                $quantityAfterMoveCurrentStore = get_sum_where(
                                    new Inv_itemcard_batches(),
                                    "quantity",
                                    array(
                                        "item_code" => $Data_item_details['item_code'], "come_code" => $come_code,
                                        'store_id' => $data_parent['store_id']
                                    )
                                );
                                $MainUomName = get_field_value(new Inv_uom(), "name", array("come_code" => $come_code, "id" => $Data_item_details['inv_uoms_id']));
                                $itemCard_Data = get_cols_where_row(new Inv_itemCard(), array("uom_id", "retail_uom_quntToParent", "retail_uom_id", "does_has_retailunit"), array("come_code" => $come_code, "item_code" => $Data_item_details['item_code']));
                                //?????????????? ???? ???????? ???????? ??????????
                                $dataInsert_inv_itemcard_movements['inv_itemcard_movements_categories'] = 3;
                                $dataInsert_inv_itemcard_movements['items_movements_types'] = 6;
                                $dataInsert_inv_itemcard_movements['item_code'] = $Data_item_details['item_code'];
                                //?????? ???????????????? ????????
                                $dataInsert_inv_itemcard_movements['FK_table'] = $data_parent['auto_serial'];
                                //?????? ???? ?????????? ?????????????? ????????????????
                                $dataInsert_inv_itemcard_movements['FK_table_details'] = $Data_item_details['id'];
                                $dataInsert_inv_itemcard_movements['byan'] = "?????? ???????????????? ???????????? ??????" . " " . $Data_item_details['batch_auto_serial'] . " ?????? ??????" . " " . $data_parent['auto_serial'];
                                //???????? ?????????? ?????? ?????????????? ?????? ????????????
                                $dataInsert_inv_itemcard_movements['quantity_befor_movement'] = "?????? " . " " . ($quantityBeforMove * 1) . " " . $MainUomName;
                                // ???????? ?????????? ?????? ?????????????? ??????  ????????????
                                $dataInsert_inv_itemcard_movements['quantity_after_move'] = "?????? " . " " . ($quantityAfterMove * 1) . " " . $MainUomName;
                                //???????? ??????????  ???????????? ???????????? ?????? ????????????
                                $dataInsert_inv_itemcard_movements['quantity_befor_move_store'] = "?????? " . " " . ($quantityBeforMoveCurrntStore * 1) . " " . $MainUomName;
                                // ???????? ?????????? ?????????????? ???????????? ?????? ???????????? ????????????
                                $dataInsert_inv_itemcard_movements['quantity_after_move_store'] = "?????? " . " " . ($quantityAfterMoveCurrentStore * 1) . " " . $MainUomName;
                                $dataInsert_inv_itemcard_movements["store_id"] = $data_parent['store_id'];
                                $dataInsert_inv_itemcard_movements["created_at"] = date("Y-m-d H:i:s");
                                $dataInsert_inv_itemcard_movements["added_by"] = auth()->user()->id;
                                $dataInsert_inv_itemcard_movements["date"] = date("Y-m-d");
                                $dataInsert_inv_itemcard_movements["come_code"] = $come_code;
                                $flag = insert(new Inv_itemcard_movements(), $dataInsert_inv_itemcard_movements);
                                if ($flag) {
                                    //update itemcard Quantity mirror  ?????????? ???????????? ???????????????? ??????????
                                    do_update_itemCardQuantity(
                                        new Inv_itemCard(),
                                        $Data_item_details['item_code'],
                                        new Inv_itemcard_batches(),
                                        $itemCard_Data['does_has_retailunit'],
                                        $itemCard_Data['retail_uom_quntToParent']
                                    );
                                }
                            }
                        }
                    }
                }
            }
            $dataUpdateparent['is_closed'] = 1;
            $dataUpdateparent['cloased_by'] = auth()->user()->id;
            $dataUpdateparent['closed_at'] = date("Y-m-d H:i:s");
            $flag = update(new Inv_stores_inventory(), $dataUpdateparent, array("id" => $id, 'come_code' => $come_code));
            return redirect()->route('admin.stores_inventory.show', $id)->with(['success' => '?????? ???? ?????????? ???????????? ?????? ?????????? ??????????']);
        } catch (\Exception $ex) {
            return redirect()->back()
                ->with(['error' => '???????? ?????? ?????? ????' . $ex->getMessage()]);
        }
    }

    public function ajax_search(Request $request)
    {
        if ($request->ajax()) {

            $search_by_text = $request->search_by_text;
            $is_closed_search = $request->is_closed_search;
            $store_id_search = $request->store_id_search;
            $order_date_form = $request->order_date_form;
            $order_date_to = $request->order_date_to;
            $is_closed_search = $request->is_closed_search;
            $inventory_type_search = $request->inventory_type_search;


            if ($search_by_text == '') {
                //??????????  true
                $field1 = "id";
                $operator1 = ">";
                $value1 = 0;
            } else {
                $field1 = "auto_serial";
                $operator1 = "=";
                $value1 = $search_by_text;
            }


            if ($is_closed_search == 'all') {
                //??????????  true
                $field2 = "id";
                $operator2 = ">";
                $value2 = 0;
            } else {
                $field2 = "is_closed";
                $operator2 = "=";
                $value2 = $is_closed_search;
            }
            if ($store_id_search == 'all') {
                //??????????  true
                $field3 = "id";
                $operator3 = ">";
                $value3 = 0;
            } else {
                $field3 = "store_id";
                $operator3 = "=";
                $value3 = $store_id_search;
            }



            if ($order_date_form == '') {
                //??????????  true
                $field4 = "id";
                $operator4 = ">";
                $value4 = 0;
            } else {
                $field4 = "inventory_date";
                $operator4 = ">=";
                $value4 = $order_date_form;
            }
            if ($order_date_to == '') {
                //??????????  true
                $field5 = "id";
                $operator5 = ">";
                $value5 = 0;
            } else {
                $field5 = "inventory_date";
                $operator5 = "<=";
                $value5 = $order_date_to;
            }

            if ($inventory_type_search == 'all') {
                //??????????  true
                $field6 = "id";
                $operator6 = ">";
                $value6 = 0;
            } else {
                $field6 = "inventory_type";
                $operator6 = "=";
                $value6 = $inventory_type_search;
            }
            $data = Inv_stores_inventory::where($field1, $operator1, $value1)->where($field2, $operator2, $value2)->where($field3, $operator3, $value3)->where($field4, $operator4, $value4)->where($field5, $operator5, $value5)->where($field6, $operator6, $value6)->orderBy('id', 'DESC')->paginate(10);
            if (!empty($data)) {
                foreach ($data as $info) {
                    $info->added_by_admin = User::where('id', $info->added_by)->value('name');
                    if ($info->updated_by > 0 and $info->updated_by != null) {
                        $info->updated_by_admin = User::where('id', $info->updated_by)->value('name');
                        $info->store_name = Store::where('id', $info->store_id)->value('name');
                    }
                }
            }
            return view('admin.inv_stores_inventory.ajax_search', ['data' => $data]);
        }
    }


    public function printsaleswina4($id, $size)
    {

        try {
            $come_code = auth()->user()->come_code;
            $invoice_data = get_cols_where_row(new Inv_stores_inventory(), array("*"), array("id" => $id, "come_code" => $come_code));
            if (empty($invoice_data)) {
                return redirect()->route('admin.inv_stores_inventory.index')->with(['error' => '???????? ?????? ???????? ?????? ???????????? ?????? ???????????????? ???????????????? !!']);
            }
            $invoice_data['store_name'] = Store::where('id', $invoice_data['store_id'])->value('name');

            $invoice_data['added_by_admin'] = User::where('id', $invoice_data['added_by'])->value('name');
            $invoices_details = get_cols_where(new Inv_stores_inventory_details(), array("*"), array('inv_stores_inventory_auto_serial' => $invoice_data['auto_serial'], 'come_code' => $come_code), 'id', 'ASC');
            if (!empty($invoices_details)) {
                foreach ($invoices_details as $info) {
                    $info->item_name = Inv_itemCard::where('item_code', $info->item_code)->value('name');
                    $info->item_type = Inv_itemCard::where('item_code', $info->item_code)->value('item_type');
                    $data['added_by_admin'] = User::where('id', $info->added_by)->value('name');
                    if ($info->updated_by > 0 and $info->updated_by != null) {
                        $data['updated_by_admin'] = User::where('id', $info->updated_by)->value('name');
                    }
                }
            }



            $systemData = get_cols_where_row(new Admin_panel_setting(), array("system_name", "phone", "address", "photo"), array("come_code" => $come_code));

            if ($size == "A4") {
                return view('admin.inv_stores_inventory.printsaleswina4', ['data' => $invoice_data, 'systemData' => $systemData, 'invoices_details' => $invoices_details]);
            } else {
                return view('admin.inv_stores_inventory.printsaleswina6', ['data' => $invoice_data, 'systemData' => $systemData, 'invoices_details' => $invoices_details]);
            }
        } catch (\Exception $ex) {
            return redirect()->back()
                ->with(['error' => '???????? ?????? ?????? ????' . $ex->getMessage()]);
        }
    }
}
