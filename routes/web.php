<?php

use App\Http\Controllers\Admin\Account_typeController;
use App\Http\Controllers\Admin\AccountController;
use App\Http\Controllers\Admin\dashboardController;
use App\Http\Controllers\Admin\admin_panel_settingController;
use App\Http\Controllers\Admin\Admin_ShiftsController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\Inv_itemcard_categoriesController;
use App\Http\Controllers\Admin\Inv_itemCardController;
use App\Http\Controllers\Admin\Sales_matrial_typesController;
use App\Http\Controllers\Admin\StoresController;
use App\Http\Controllers\Admin\SupplierController;
use App\Http\Controllers\Admin\SupplierCotegoryController;
use App\Http\Controllers\Admin\Supplire_with_orderController;
use App\Http\Controllers\Admin\TreasurController;
use App\Http\Controllers\Admin\UomsController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\CollectController;
use App\Http\Controllers\Admin\DelegatesController;
use App\Http\Controllers\Admin\ExchangeController;
use App\Http\Controllers\Admin\FinancialReportController;
use App\Http\Controllers\Admin\Inv_production_exchangeController;
use App\Http\Controllers\Admin\Inv_production_linesController;
use App\Http\Controllers\Admin\inv_production_orderController;
use App\Http\Controllers\Admin\inv_production_ReceiveController;
use App\Http\Controllers\Admin\Inv_stores_inventoryController;
use App\Http\Controllers\Admin\Inv_stores_transferController;
use App\Http\Controllers\Admin\Inv_stores_transferIncomingController;
use App\Http\Controllers\Admin\ItemcardBalance;
use App\Http\Controllers\Admin\SalesInvoicesController;
use App\Http\Controllers\Admin\SalesReturnInvoicesController;
use App\Http\Controllers\Admin\Services_with_ordersController;
use App\Http\Controllers\Admin\ServicesController;
use App\Http\Controllers\Admin\Suppliers_with_ordersGeneralRetuen;
use App\Models\Supplier_with_order;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;


Route::redirect('', 'login');
Route::prefix('admin/')->name('admin.')->middleware('auth', 'check_user')->group(function () {

    Route::get('/', [dashboardController::class, 'index'])->name('dashboard');
    Route::get('admin_panel_setting/index', [admin_panel_settingController::class, 'index'])->name('adminPanelSetting.index');
    Route::get('admin_panel_setting/edit', [admin_panel_settingController::class, 'edit'])->name('adminPanelSetting.edit');
    Route::post('admin_panel_setting/update', [admin_panel_settingController::class, 'update'])->name('adminPanelSetting.update');
    // =============================================================================================================================
    Route::get('treasuries/index', [TreasurController::class, 'index'])->name('treasuries.index');
    Route::get('treasuries/create', [TreasurController::class, 'create'])->name('treasuries.create');
    Route::post('treasuries/store', [TreasurController::class, 'store'])->name('treasuries.store');
    Route::get('treasuries/edit/{id}', [TreasurController::class, 'edit'])->name('treasuries.edit');
    Route::post('treasuries/update/{id}', [TreasurController::class, 'update'])->name('treasuries.update');
    Route::post('/treasuries/ajax_search', [TreasurController::class, 'ajax_search'])->name('treasuries.ajax_search');
    Route::get('/treasuries/details/{id}', [TreasurController::class, 'details'])->name('treasuries.details');
    Route::get('/treasuries/Add_treasuries_delivery/{id}', [TreasurController::class, 'Add_treasuries_delivery'])->name('treasuries.Add_treasuries_delivery');
    Route::post('/treasuries/store_treasuries_delivery/{id}', [TreasurController::class, 'store_treasuries_delivery'])->name('treasuries.store_treasuries_delivery');
    Route::get('/treasuries/delete_treasuries_delivery/{id}', [TreasurController::class, 'delete_treasuries_delivery'])->name('treasuries.delete_treasuries_delivery');

    //================================================================================================================================================================

    Route::get('/sales_matrial_types/index', [Sales_matrial_typesController::class, 'index'])->name('sales_matrial_types.index');
    Route::get('/sales_matrial_types/create', [Sales_matrial_typesController::class, 'create'])->name('sales_matrial_types.create');
    Route::post('/sales_matrial_types/store', [Sales_matrial_typesController::class, 'store'])->name('sales_matrial_types.store');
    Route::get('/sales_matrial_types/edit/{id}', [Sales_matrial_typesController::class, 'edit'])->name('sales_matrial_types.edit');
    Route::post('/sales_matrial_types/update/{id}', [Sales_matrial_typesController::class, 'update'])->name('sales_matrial_types.update');
    Route::get('/sales_matrial_types/delete/{id}', [Sales_matrial_typesController::class, 'delete'])->name('sales_matrial_types.delete');

    //========================================================================================================================================================================

    Route::get('/stores/index', [StoresController::class, 'index'])->name('stores.index');
    Route::get('/stores/create', [StoresController::class, 'create'])->name('stores.create');
    Route::post('/stores/store', [StoresController::class, 'store'])->name('stores.store');
    Route::get('/stores/edit/{id}', [StoresController::class, 'edit'])->name('stores.edit');
    Route::post('/stores/update/{id}', [StoresController::class, 'update'])->name('stores.update');
    Route::get('/stores/delete/{id}', [StoresController::class, 'delete'])->name('stores.delete');

    //========================================================================================================================================

    //========================================================================================================================================================================

    Route::get('/uoms/index', [UomsController::class, 'index'])->name('uoms.index');
    Route::get('/uoms/create', [UomsController::class, 'create'])->name('uoms.create');
    Route::post('/uoms/store', [UomsController::class, 'store'])->name('uoms.store');
    Route::get('/uoms/edit/{id}', [UomsController::class, 'edit'])->name('uoms.edit');
    Route::post('/uoms/update/{id}', [UomsController::class, 'update'])->name('uoms.update');
    Route::get('/uoms/delete/{id}', [UomsController::class, 'delete'])->name('uoms.delete');
    Route::post('/uoms/ajax_search', [UomsController::class, 'ajax_search'])->name('uoms.ajax_search');

    //========================================================================================================================================
    Route::get('/inv_itemcard_categories/delete/{id}', [Inv_itemcard_categoriesController::class, 'delete'])->name('inv_itemcard_categories.delete');

    Route::resource('/inv_itemcard_categories', Inv_itemcard_categoriesController::class);

    //========================================================================================================================================

    Route::get('/inv_itemcard/index', [Inv_itemCardController::class, 'index'])->name('inv_itemcard.index');
    Route::get('/inv_itemcard/create', [Inv_itemCardController::class, 'create'])->name('inv_itemcard.create');
    Route::post('/inv_itemcard/store', [Inv_itemCardController::class, 'store'])->name('inv_itemcard.store');
    Route::get('/inv_itemcard/edit/{id}', [Inv_itemCardController::class, 'edit'])->name('inv_itemcard.edit');
    Route::post('/inv_itemcard/update/{id}', [Inv_itemCardController::class, 'update'])->name('inv_itemcard.update');
    Route::get('/inv_itemcard/delete/{id}', [Inv_itemCardController::class, 'delete'])->name('inv_itemcard.delete');
    Route::post('/inv_itemcard/ajax_search', [Inv_itemCardController::class, 'ajax_search'])->name('inv_itemcard.ajax_search');
    Route::post('/inv_itemcard/ajax_search_movements', [Inv_itemCardController::class, 'ajax_search_movements'])->name('inv_itemcard.ajax_search_movements');
    Route::get('/inv_itemcard/show/{id}', [Inv_itemCardController::class, 'show'])->name('inv_itemcard.show');

    //========================================================================================================================================

    Route::get('/account_type/index', [Account_typeController::class, 'index'])->name('account_type.index');

    //========================================================================================================================================

    Route::get('/accounts/index', [AccountController::class, 'index'])->name('accounts.index');
    Route::get('/accounts/create', [AccountController::class, 'create'])->name('accounts.create');
    Route::post('/accounts/store', [AccountController::class, 'store'])->name('accounts.store');
    Route::get('/accounts/edit/{id}', [AccountController::class, 'edit'])->name('accounts.edit');
    Route::post('/accounts/update/{id}', [AccountController::class, 'update'])->name('accounts.update');
    Route::get('/accounts/delete/{id}', [AccountController::class, 'delete'])->name('accounts.delete');
    Route::post('/accounts/ajax_search', [AccountController::class, 'ajax_search'])->name('accounts.ajax_search');
    Route::get('/accounts/show/{id}', [AccountController::class, 'show'])->name('accounts.show');

    //========================================================================================================================================

    Route::get('/customer/index', [CustomerController::class, 'index'])->name('customer.index');
    Route::get('/customer/create', [CustomerController::class, 'create'])->name('customer.create');
    Route::post('/customer/store', [CustomerController::class, 'store'])->name('customer.store');
    Route::get('/customer/edit/{id}', [CustomerController::class, 'edit'])->name('customer.edit');
    Route::post('/customer/update/{id}', [CustomerController::class, 'update'])->name('customer.update');
    Route::get('/customer/delete/{id}', [CustomerController::class, 'delete'])->name('customer.delete');
    Route::post('/customer/ajax_search', [CustomerController::class, 'ajax_search'])->name('customer.ajax_search');
    Route::get('/customer/show/{id}', [CustomerController::class, 'show'])->name('customer.show');

    //========================================================================================================================================

    Route::get('/suppliers_categories/index', [SupplierCotegoryController::class, 'index'])->name('suppliers_categories.index');
    Route::get('/suppliers_categories/create', [SupplierCotegoryController::class, 'create'])->name('suppliers_categories.create');
    Route::post('/suppliers_categories/store', [SupplierCotegoryController::class, 'store'])->name('suppliers_categories.store');
    Route::get('/suppliers_categories/edit/{id}', [SupplierCotegoryController::class, 'edit'])->name('suppliers_categories.edit');
    Route::post('/suppliers_categories/update/{id}', [SupplierCotegoryController::class, 'update'])->name('suppliers_categories.update');
    Route::get('/suppliers_categories/delete/{id}', [SupplierCotegoryController::class, 'delete'])->name('suppliers_categories.delete');

    //========================================================================================================================================================================

    Route::get('/supplier/index', [SupplierController::class, 'index'])->name('supplier.index');
    Route::get('/supplier/create', [SupplierController::class, 'create'])->name('supplier.create');
    Route::post('/supplier/store', [SupplierController::class, 'store'])->name('supplier.store');
    Route::get('/supplier/edit/{id}', [SupplierController::class, 'edit'])->name('supplier.edit');
    Route::post('/supplier/update/{id}', [SupplierController::class, 'update'])->name('supplier.update');
    Route::get('/supplier/delete/{id}', [SupplierController::class, 'delete'])->name('supplier.delete');
    Route::post('/supplier/ajax_search', [SupplierController::class, 'ajax_search'])->name('supplier.ajax_search');
    Route::get('/supplier/show/{id}', [SupplierController::class, 'show'])->name('supplier.show');

    //========================================================================================================================================

    Route::get('/suppliers_orders/index', [Supplire_with_orderController::class, 'index'])->name('suppliers_orders.index');
    Route::get('/suppliers_orders/create', [Supplire_with_orderController::class, 'create'])->name('suppliers_orders.create');
    Route::post('/suppliers_orders/store', [Supplire_with_orderController::class, 'store'])->name('suppliers_orders.store');
    Route::get('/suppliers_orders/edit/{id}', [Supplire_with_orderController::class, 'edit'])->name('suppliers_orders.edit');
    Route::post('/suppliers_orders/update/{id}', [Supplire_with_orderController::class, 'update'])->name('suppliers_orders.update');
    Route::get('/suppliers_orders/delete/{id}', [Supplire_with_orderController::class, 'delete'])->name('suppliers_orders.delete');
    Route::post('/suppliers_orders/ajax_search', [Supplire_with_orderController::class, 'ajax_search'])->name('suppliers_orders.ajax_search');
    Route::get('/suppliers_orders/show/{id}', [Supplire_with_orderController::class, 'show'])->name('suppliers_orders.show');
    Route::post('/suppliers_orders/get_item_uoms', [Supplire_with_orderController::class, 'get_item_uoms'])->name('suppliers_orders.get_item_uoms');
    Route::post('/suppliers_orders/load_modal_add_details', [Supplire_with_orderController::class, 'load_modal_add_details'])->name('suppliers_orders.load_modal_add_details');
    Route::post('/suppliers_orders/add_new_details', [Supplire_with_orderController::class, 'add_new_details'])->name('suppliers_orders.add_new_details');
    Route::post('/suppliers_orders/reload_itemsdetials', [Supplire_with_orderController::class, 'reload_itemsdetials'])->name('suppliers_orders.reload_itemsdetials');
    Route::post('/suppliers_orders/reload_parent_pill', [Supplire_with_orderController::class, 'reload_parent_pill'])->name('suppliers_orders.reload_parent_pill');
    Route::post('/suppliers_orders/load_edit_item_details', [Supplire_with_orderController::class, 'load_edit_item_details'])->name('suppliers_orders.load_edit_item_details');
    Route::post('/suppliers_orders/edit_item_details', [Supplire_with_orderController::class, 'edit_item_details'])->name('suppliers_orders.edit_item_details');
    Route::get('/suppliers_orders/delete_details/{id}/{id_parent}', [Supplire_with_orderController::class, 'delete_details'])->name('suppliers_orders.delete_details');
    Route::post('/suppliers_orders/do_approve/{id}', [Supplire_with_orderController::class, 'do_approve'])->name('suppliers_orders.do_approve');
    Route::post('/suppliers_orders/load_modal_approve_invoice', [Supplire_with_orderController::class, 'load_modal_approve_invoice'])->name('suppliers_orders.load_modal_approve_invoice');
    Route::post('/suppliers_orders/load_usershiftDiv', [Supplire_with_orderController::class, 'load_usershiftDiv'])->name('suppliers_orders.load_usershiftDiv');
    Route::get('/suppliers_orders/printsaleswina4/{id}/{size}', [Supplire_with_orderController::class, 'printsaleswina4'])->name('suppliers_orders.printsaleswina4');

    //========================================================================================================================================
    Route::get('/admins_accounts/index', [AdminController::class, 'index'])->name('admins_accounts.index');
    Route::get('/admins_accounts/create', [AdminController::class, 'create'])->name('admins_accounts.create');
    Route::post('/admins_accounts/store', [AdminController::class, 'store'])->name('admins_accounts.store');
    Route::get('/admins_accounts/edit/{id}', [AdminController::class, 'edit'])->name('admins_accounts.edit');
    Route::post('/admins_accounts/update/{id}', [AdminController::class, 'update'])->name('admins_accounts.update');
    Route::post('/admins_accounts/ajax_search', [AdminController::class, 'ajax_search'])->name('admins_accounts.ajax_search');
    Route::get('/admins_accounts/details/{id}', [AdminController::class, 'details'])->name('admins_accounts.details');
    Route::get('/admins_accounts/Add_treasuries_delivery/{id}', [AdminController::class, 'Add_treasuries_delivery'])->name('admins_accounts.Add_treasuries_delivery');
    Route::post('/admins_accounts/Add_treasuries_To_Admin/{id}', [AdminController::class, 'Add_treasuries_To_Admin'])->name('admins_accounts.Add_treasuries_To_Admin');
    Route::get('/admins_accounts/delete_treasuries_delivery/{id}', [AdminController::class, 'delete_treasuries_delivery'])->name('admins_accounts.delete_treasuries_delivery');

    //================================================================================================================================================================

    Route::get('admin_shift/index', [Admin_ShiftsController::class, 'index'])->name('admin_shift.index');
    Route::get('admin_shift/create', [Admin_ShiftsController::class, 'create'])->name('admin_shift.create');
    Route::post('admin_shift/store', [Admin_ShiftsController::class, 'store'])->name('admin_shift.store');


    //================================================================================================================================================================

    Route::get('collect_transaction/index', [CollectController::class, 'index'])->name('collect_transaction.index');
    Route::get('collect_transaction/create', [CollectController::class, 'create'])->name('collect_transaction.create');
    Route::post('collect_transaction/store', [CollectController::class, 'store'])->name('collect_transaction.store');
    Route::post('collect_transaction/get_account_blance', [CollectController::class, 'get_account_blance'])->name('collect_transaction.get_account_blance');
    Route::post('collect_transaction/ajax_search', [CollectController::class, 'ajax_search'])->name('collect_transaction.ajax_search');

    //================================================================================================================================================================

    Route::get('exchange_transaction/index', [ExchangeController::class, 'index'])->name('exchange_transaction.index');
    Route::get('exchange_transaction/create', [ExchangeController::class, 'create'])->name('exchange_transaction.create');
    Route::post('exchange_transaction/store', [ExchangeController::class, 'store'])->name('exchange_transaction.store');
    Route::post('exchange_transaction/get_account_blance', [ExchangeController::class, 'get_account_blance'])->name('exchange_transaction.get_account_blance');
    Route::post('exchange_transaction/ajax_search', [ExchangeController::class, 'ajax_search'])->name('exchange_transaction.ajax_search');

    //================================================================================================================================================================

    Route::get('/SalesInvoices/index', [SalesInvoicesController::class, 'index'])->name('SalesInvoices.index');
    Route::get('/SalesInvoices/create', [SalesInvoicesController::class, 'create'])->name('SalesInvoices.create');
    Route::post('/SalesInvoices/store', [SalesInvoicesController::class, 'store'])->name('SalesInvoices.store');
    Route::get('/SalesInvoices/edit/{id}', [SalesInvoicesController::class, 'edit'])->name('SalesInvoices.edit');
    Route::post('/SalesInvoices/update/{id}', [SalesInvoicesController::class, 'update'])->name('SalesInvoices.update');
    Route::get('/SalesInvoices/delete/{id}', [SalesInvoicesController::class, 'delete'])->name('SalesInvoices.delete');
    Route::get('/SalesInvoices/show/{id}', [SalesInvoicesController::class, 'show'])->name('SalesInvoices.show');
    Route::post('/SalesInvoices/get_item_uoms', [SalesInvoicesController::class, 'get_item_uoms'])->name('SalesInvoices.get_item_uoms');
    Route::post('/SalesInvoices/load_modal_add', [SalesInvoicesController::class, 'load_modal_add'])->name('SalesInvoices.load_modal_add');
    Route::post('/SalesInvoices/get_item_batches', [SalesInvoicesController::class, 'get_item_batches'])->name('SalesInvoices.get_item_batches');
    Route::post('/SalesInvoices/get_item_unit_price', [SalesInvoicesController::class, 'get_item_unit_price'])->name('SalesInvoices.get_item_unit_price');
    Route::post('/SalesInvoices/get_Add_new_item_row', [SalesInvoicesController::class, 'get_Add_new_item_row'])->name('SalesInvoices.get_Add_new_item_row');
    Route::post('/SalesInvoices/load_modal_addMirror', [SalesInvoicesController::class, 'load_modal_addMirror'])->name('SalesInvoices.load_modal_addMirror');
    Route::post('/SalesInvoices/load_modal_addActiveInvoice', [SalesInvoicesController::class, 'load_modal_addActiveInvoice'])->name('SalesInvoices.load_modal_addActiveInvoice');
    Route::post('/SalesInvoices/store', [SalesInvoicesController::class, 'store'])->name('SalesInvoices.store');
    Route::post('/SalesInvoices/load_invoice_update_modal', [SalesInvoicesController::class, 'load_invoice_update_modal'])->name('SalesInvoices.load_invoice_update_modal');
    Route::post('/SalesInvoices/Add_item_to_invoice', [SalesInvoicesController::class, 'Add_item_to_invoice'])->name('SalesInvoices.Add_item_to_invoice');
    Route::post('/SalesInvoices/reload_items_in_invoice', [SalesInvoicesController::class, 'reload_items_in_invoice'])->name('SalesInvoices.reload_items_in_invoice');
    Route::post('/SalesInvoices/recalclate_parent_invoice', [SalesInvoicesController::class, 'recalclate_parent_invoice'])->name('SalesInvoices.recalclate_parent_invoice');
    Route::post('/SalesInvoices/remove_active_row_item', [SalesInvoicesController::class, 'remove_active_row_item'])->name('SalesInvoices.remove_active_row_item');
    Route::post('/SalesInvoices/DoApproveInvoiceFinally', [SalesInvoicesController::class, 'DoApproveInvoiceFinally'])->name('SalesInvoices.DoApproveInvoiceFinally');
    Route::post('/SalesInvoices/load_usershiftDiv', [SalesInvoicesController::class, 'load_usershiftDiv'])->name('SalesInvoices.load_usershiftDiv');
    Route::post('/SalesInvoices/load_invoice_details_modal', [SalesInvoicesController::class, 'load_invoice_details_modal'])->name('SalesInvoices.load_invoice_details_modal');
    Route::post('/SalesInvoices/ajax_search', [SalesInvoicesController::class, 'ajax_search'])->name('SalesInvoices.ajax_search');
    Route::post('/SalesInvoices/do_add_new_customer', [SalesInvoicesController::class, 'do_add_new_customer'])->name('SalesInvoices.do_add_new_customer');
    Route::post('/SalesInvoices/get_last_added_customer', [SalesInvoicesController::class, 'get_last_added_customer'])->name('SalesInvoices.get_last_added_customer');
    Route::post('/SalesInvoices/searchforcustomer', [SalesInvoicesController::class, 'searchforcustomer'])->name('SalesInvoices.searchforcustomer');
    Route::post('/SalesInvoices/searchforitems', [SalesInvoicesController::class, 'searchforitems'])->name('SalesInvoices.searchforitems');
    Route::get('/SalesInvoices/printsaleswina4/{id}/{size}', [SalesInvoicesController::class, 'printsaleswina4'])->name('SalesInvoices.printsaleswina4');

    //========================================================================================================================================

    Route::get('/delegates/index', [DelegatesController::class, 'index'])->name('delegates.index');
    Route::get('/delegates/create', [DelegatesController::class, 'create'])->name('delegates.create');
    Route::post('/delegates/store', [DelegatesController::class, 'store'])->name('delegates.store');
    Route::get('/delegates/edit/{id}', [DelegatesController::class, 'edit'])->name('delegates.edit');
    Route::post('/delegates/update/{id}', [DelegatesController::class, 'update'])->name('delegates.update');
    Route::get('/delegates/delete/{id}', [DelegatesController::class, 'delete'])->name('delegates.delete');
    Route::post('/delegates/ajax_search', [DelegatesController::class, 'ajax_search'])->name('delegates.ajax_search');
    Route::post('/delegates/show', [DelegatesController::class, 'show'])->name('delegates.show');


    //=============================================================================================================================
    /*         start  suppliers_orders Gernal Return   مرتجع المشتريات العام             */
    Route::get('/suppliers_orders_general_return/index', [Suppliers_with_ordersGeneralRetuen::class, 'index'])->name('suppliers_orders_general_return.index');
    Route::get('/suppliers_orders_general_return/create', [Suppliers_with_ordersGeneralRetuen::class, 'create'])->name('suppliers_orders_general_return.create');
    Route::post('/suppliers_orders_general_return/store', [Suppliers_with_ordersGeneralRetuen::class, 'store'])->name('suppliers_orders_general_return.store');
    Route::get('/suppliers_orders_general_return/edit/{id}', [Suppliers_with_ordersGeneralRetuen::class, 'edit'])->name('suppliers_orders_general_return.edit');
    Route::post('/suppliers_orders_general_return/update/{id}', [Suppliers_with_ordersGeneralRetuen::class, 'update'])->name('suppliers_orders_general_return.update');
    Route::get('/suppliers_orders_general_return/delete/{id}', [Suppliers_with_ordersGeneralRetuen::class, 'delete'])->name('suppliers_orders_general_return.delete');
    Route::post('/suppliers_orders_general_return/ajax_search', [Suppliers_with_ordersGeneralRetuen::class, 'ajax_search'])->name('suppliers_orders_general_return.ajax_search');
    Route::get('/suppliers_orders_general_return/show/{id}', [Suppliers_with_ordersGeneralRetuen::class, 'show'])->name('suppliers_orders_general_return.show');
    Route::post('/suppliers_orders_general_return/get_item_uoms', [Suppliers_with_ordersGeneralRetuen::class, 'get_item_uoms'])->name('suppliers_orders_general_return.get_item_uoms');
    Route::post('/suppliers_orders_general_return/load_modal_add_details', [Suppliers_with_ordersGeneralRetuen::class, 'load_modal_add_details'])->name('suppliers_orders_general_return.load_modal_add_details');
    Route::post('/suppliers_orders_general_return/reload_itemsdetials', [Suppliers_with_ordersGeneralRetuen::class, 'reload_itemsdetials'])->name('suppliers_orders_general_return.reload_itemsdetials');
    Route::post('/suppliers_orders_general_return/reload_parent_pill', [Suppliers_with_ordersGeneralRetuen::class, 'reload_parent_pill'])->name('suppliers_orders_general_return.reload_parent_pill');
    Route::post('/suppliers_orders_general_return/load_edit_item_details', [Suppliers_with_ordersGeneralRetuen::class, 'load_edit_item_details'])->name('suppliers_orders_general_return.load_edit_item_details');
    Route::post('/suppliers_orders_general_return/edit_item_details', [Suppliers_with_ordersGeneralRetuen::class, 'edit_item_details'])->name('suppliers_orders_general_return.edit_item_details');
    Route::get('/suppliers_orders_general_return/delete_details/{id}/{id_parent}', [Suppliers_with_ordersGeneralRetuen::class, 'delete_details'])->name('suppliers_orders_general_return.delete_details');
    Route::post('/suppliers_orders_general_return/do_approve/{id}', [Suppliers_with_ordersGeneralRetuen::class, 'do_approve'])->name('suppliers_orders_general_return.do_approve');
    Route::post('/suppliers_orders_general_return/load_modal_approve_invoice', [Suppliers_with_ordersGeneralRetuen::class, 'load_modal_approve_invoice'])->name('suppliers_orders_general_return.load_modal_approve_invoice');
    Route::post('/suppliers_orders_general_return/load_usershiftDiv', [Suppliers_with_ordersGeneralRetuen::class, 'load_usershiftDiv'])->name('suppliers_orders_general_return.load_usershiftDiv');
    Route::post('/suppliers_orders_general_return/Add_item_to_invoice', [Suppliers_with_ordersGeneralRetuen::class, 'Add_item_to_invoice'])->name('suppliers_orders_general_return.Add_item_to_invoice');
    Route::post('/suppliers_orders_general_return/get_item_batches', [Suppliers_with_ordersGeneralRetuen::class, 'get_item_batches'])->name('suppliers_orders_general_return.get_item_batches');
    Route::get('/suppliers_orders_general_return/printsaleswina4/{id}/{size}', [Suppliers_with_ordersGeneralRetuen::class, 'printsaleswina4'])->name('suppliers_orders_general_return.printsaleswina4');


    /*           end  suppliers_orders Gernal Return                */

    /*           start  itemcardBalance  Return                */
    Route::get('/itemcardBalance/index', [ItemcardBalance::class, 'index'])->name('itemcardBalance.index');
    Route::post('/itemcardBalance/ajax_search', [ItemcardBalance::class, 'ajax_search'])->name('itemcardBalance.ajax_search');


    /*           end  itemcardBalance  Return                */

    /*         start  sales Invoices   مرتجع المبيعات العام             */
    Route::get('/SalesReturnInvoices/index', [SalesReturnInvoicesController::class, 'index'])->name('SalesReturnInvoices.index');
    Route::get('/SalesReturnInvoices/create', [SalesReturnInvoicesController::class, 'create'])->name('SalesReturnInvoices.create');
    Route::post('/SalesReturnInvoices/store', [SalesReturnInvoicesController::class, 'store'])->name('SalesReturnInvoices.store');
    Route::get('/SalesReturnInvoices/edit/{id}', [SalesReturnInvoicesController::class, 'edit'])->name('SalesReturnInvoices.edit');
    Route::post('/SalesReturnInvoices/update/{id}', [SalesReturnInvoicesController::class, 'update'])->name('SalesReturnInvoices.update');
    Route::get('/SalesReturnInvoices/delete/{id}', [SalesReturnInvoicesController::class, 'delete'])->name('SalesReturnInvoices.delete');
    Route::get('/SalesReturnInvoices/show/{id}', [SalesReturnInvoicesController::class, 'show'])->name('SalesReturnInvoices.show');
    Route::post('/SalesReturnInvoices/get_item_uoms', [SalesReturnInvoicesController::class, 'get_item_uoms'])->name('SalesReturnInvoices.get_item_uoms');
    Route::post('/SalesReturnInvoices/get_item_batches', [SalesReturnInvoicesController::class, 'get_item_batches'])->name('SalesReturnInvoices.get_item_batches');
    Route::post('/SalesReturnInvoices/get_item_unit_price', [SalesReturnInvoicesController::class, 'get_item_unit_price'])->name('SalesReturnInvoices.get_item_unit_price');
    Route::post('/SalesReturnInvoices/get_Add_new_item_row', [SalesReturnInvoicesController::class, 'get_Add_new_item_row'])->name('SalesReturnInvoices.get_Add_new_item_row');
    Route::post('/SalesReturnInvoices/load_modal_addMirror', [SalesReturnInvoicesController::class, 'load_modal_addMirror'])->name('SalesReturnInvoices.load_modal_addMirror');
    Route::post('/SalesReturnInvoices/load_modal_addActiveInvoice', [SalesReturnInvoicesController::class, 'load_modal_addActiveInvoice'])->name('SalesReturnInvoices.load_modal_addActiveInvoice');
    Route::post('/SalesReturnInvoices/store', [SalesReturnInvoicesController::class, 'store'])->name('SalesReturnInvoices.store');
    Route::post('/SalesReturnInvoices/load_invoice_update_modal', [SalesReturnInvoicesController::class, 'load_invoice_update_modal'])->name('SalesReturnInvoices.load_invoice_update_modal');
    Route::post('/SalesReturnInvoices/Add_item_to_invoice', [SalesReturnInvoicesController::class, 'Add_item_to_invoice'])->name('SalesReturnInvoices.Add_item_to_invoice');
    Route::post('/SalesReturnInvoices/reload_items_in_invoice', [SalesReturnInvoicesController::class, 'reload_items_in_invoice'])->name('SalesReturnInvoices.reload_items_in_invoice');
    Route::post('/SalesReturnInvoices/recalclate_parent_invoice', [SalesReturnInvoicesController::class, 'recalclate_parent_invoice'])->name('SalesReturnInvoices.recalclate_parent_invoice');
    Route::post('/SalesReturnInvoices/remove_active_row_item', [SalesReturnInvoicesController::class, 'remove_active_row_item'])->name('SalesReturnInvoices.remove_active_row_item');
    Route::post('/SalesReturnInvoices/DoApproveInvoiceFinally', [SalesReturnInvoicesController::class, 'DoApproveInvoiceFinally'])->name('SalesReturnInvoices.DoApproveInvoiceFinally');
    Route::post('/SalesReturnInvoices/load_usershiftDiv', [SalesReturnInvoicesController::class, 'load_usershiftDiv'])->name('SalesReturnInvoices.load_usershiftDiv');
    Route::post('/SalesReturnInvoices/load_invoice_details_modal', [SalesReturnInvoicesController::class, 'load_invoice_details_modal'])->name('SalesReturnInvoices.load_invoice_details_modal');
    Route::post('/SalesReturnInvoices/ajax_search', [SalesReturnInvoicesController::class, 'ajax_search'])->name('SalesReturnInvoices.ajax_search');
    Route::post('/SalesReturnInvoices/do_add_new_customer', [SalesReturnInvoicesController::class, 'do_add_new_customer'])->name('SalesReturnInvoices.do_add_new_customer');
    Route::post('/SalesReturnInvoices/get_last_added_customer', [SalesReturnInvoicesController::class, 'get_last_added_customer'])->name('SalesReturnInvoices.get_last_added_customer');
    Route::post('/SalesReturnInvoices/searchforcustomer', [SalesReturnInvoicesController::class, 'searchforcustomer'])->name('SalesReturnInvoices.searchforcustomer');
    Route::post('/SalesReturnInvoices/searchforitems', [SalesReturnInvoicesController::class, 'searchforitems'])->name('SalesReturnInvoices.searchforitems');
    Route::get('/SalesReturnInvoices/printsaleswina4/{id}/{size}', [SalesReturnInvoicesController::class, 'printsaleswina4'])->name('SalesReturnInvoices.printsaleswina4');

    /*           sales Invoices   المبيعات                   */

    /* start  FinancialReportController تقاير الحسابات */
    Route::get('/FinancialReport/supplieraccountmirror', [FinancialReportController::class, 'supplier_account_mirror'])->name('FinancialReport.supplieraccountmirror');
    Route::post('/FinancialReport/supplieraccountmirror', [FinancialReportController::class, 'supplier_account_mirror'])->name('FinancialReport.supplieraccountmirror');
    Route::get('/FinancialReport/customeraccountmirror', [FinancialReportController::class, 'customer_account_mirror'])->name('FinancialReport.customeraccountmirror');
    Route::post('/FinancialReport/customeraccountmirror', [FinancialReportController::class, 'customer_account_mirror'])->name('FinancialReport.customeraccountmirror');
    Route::post('/FinancialReport/searchforcustomer', [FinancialReportController::class, 'searchforcustomer'])->name('FinancialReport.searchforcustomer');
    Route::get('/FinancialReport/delegateaccountmirror', [FinancialReportController::class, 'delegate_account_mirror'])->name('FinancialReport.delegateaccountmirror');
    Route::post('/FinancialReport/delegateaccountmirror', [FinancialReportController::class, 'delegate_account_mirror'])->name('FinancialReport.delegateaccountmirror');

    /*  end  FinancialReportController */

    /*         start  Services                */
    Route::get('/Services/index', [ServicesController::class, 'index'])->name('Services.index');
    Route::get('/Services/create', [ServicesController::class, 'create'])->name('Services.create');
    Route::post('/Services/store', [ServicesController::class, 'store'])->name('Services.store');
    Route::get('/Services/edit/{id}', [ServicesController::class, 'edit'])->name('Services.edit');
    Route::post('/Services/update/{id}', [ServicesController::class, 'update'])->name('Services.update');
    Route::get('/Services/delete/{id}', [ServicesController::class, 'delete'])->name('Services.delete');
    Route::post('/Services/ajax_search', [ServicesController::class, 'ajax_search'])->name('Services.ajax_search');
    /*           end Services                */

    /*         start  suppliers_orders   خدمات مقدمة لنا ونقدمها للغير             */
    Route::get('/Services_orders/index', [Services_with_ordersController::class, 'index'])->name('Services_orders.index');
    Route::get('/Services_orders/create', [Services_with_ordersController::class, 'create'])->name('Services_orders.create');
    Route::post('/Services_orders/store', [Services_with_ordersController::class, 'store'])->name('Services_orders.store');
    Route::get('/Services_orders/edit/{id}', [Services_with_ordersController::class, 'edit'])->name('Services_orders.edit');
    Route::post('/Services_orders/update/{id}', [Services_with_ordersController::class, 'update'])->name('Services_orders.update');
    Route::get('/Services_orders/delete/{id}', [Services_with_ordersController::class, 'delete'])->name('Services_orders.delete');
    Route::post('/Services_orders/ajax_search', [Services_with_ordersController::class, 'ajax_search'])->name('Services_orders.ajax_search');
    Route::get('/Services_orders/show/{id}', [Services_with_ordersController::class, 'show'])->name('Services_orders.show');
    Route::post('/Services_orders/load_modal_add_details', [Services_with_ordersController::class, 'load_modal_add_details'])->name('Services_orders.load_modal_add_details');
    Route::post('/Services_orders/add_new_details', [Services_with_ordersController::class, 'add_new_details'])->name('Services_orders.add_new_details');
    Route::post('/Services_orders/reload_itemsdetials', [Services_with_ordersController::class, 'reload_itemsdetials'])->name('Services_orders.reload_itemsdetials');
    Route::post('/Services_orders/reload_parent_pill', [Services_with_ordersController::class, 'reload_parent_pill'])->name('Services_orders.reload_parent_pill');
    Route::post('/Services_orders/load_edit_item_details', [Services_with_ordersController::class, 'load_edit_item_details'])->name('Services_orders.load_edit_item_details');
    Route::post('/Services_orders/edit_item_details', [Services_with_ordersController::class, 'edit_item_details'])->name('Services_orders.edit_item_details');
    Route::get('/Services_orders/delete_details/{id}/{id_parent}', [Services_with_ordersController::class, 'delete_details'])->name('Services_orders.delete_details');
    Route::post('/Services_orders/do_approve/{id}', [Services_with_ordersController::class, 'do_approve'])->name('Services_orders.do_approve');
    Route::post('/Services_orders/load_modal_approve_invoice', [Services_with_ordersController::class, 'load_modal_approve_invoice'])->name('Services_orders.load_modal_approve_invoice');
    Route::post('/Services_orders/load_usershiftDiv', [Services_with_ordersController::class, 'load_usershiftDiv'])->name('Services_orders.load_usershiftDiv');
    Route::get('/Services_orders/printsaleswina4/{id}/{size}', [Services_with_ordersController::class, 'printsaleswina4'])->name('Services_orders.printsaleswina4');
    /*           end suppliers_orders               */

/*         start  inv_stores_inventory  جرد المخازن            */
Route::get('/stores_inventory/index', [Inv_stores_inventoryController::class, 'index'])->name('stores_inventory.index');
Route::get('/stores_inventory/create', [Inv_stores_inventoryController::class, 'create'])->name('stores_inventory.create');
Route::post('/stores_inventory/store', [Inv_stores_inventoryController::class, 'store'])->name('stores_inventory.store');
Route::get('/stores_inventory/edit/{id}', [Inv_stores_inventoryController::class, 'edit'])->name('stores_inventory.edit');
Route::post('/stores_inventory/update/{id}', [Inv_stores_inventoryController::class, 'update'])->name('stores_inventory.update');
Route::get('/stores_inventory/delete/{id}', [Inv_stores_inventoryController::class, 'delete'])->name('stores_inventory.delete');
Route::post('/stores_inventory/ajax_search', [Inv_stores_inventoryController::class, 'ajax_search'])->name('stores_inventory.ajax_search');
Route::get('/stores_inventory/show/{id}', [Inv_stores_inventoryController::class, 'show'])->name('stores_inventory.show');
Route::post('/stores_inventory/add_new_details/{id}', [Inv_stores_inventoryController::class, 'add_new_details'])->name('stores_inventory.add_new_details');
Route::post('/stores_inventory/load_edit_item_details', [Inv_stores_inventoryController::class, 'load_edit_item_details'])->name('stores_inventory.load_edit_item_details');
Route::post('/stores_inventory/edit_item_details/{id}/{id_parent}', [Inv_stores_inventoryController::class, 'edit_item_details'])->name('stores_inventory.edit_item_details');
Route::get('/stores_inventory/delete_details/{id}/{id_parent}', [Inv_stores_inventoryController::class, 'delete_details'])->name('stores_inventory.delete_details');
Route::get('/stores_inventory/close_one_details/{id}/{id_parent}', [Inv_stores_inventoryController::class, 'close_one_details'])->name('stores_inventory.close_one_details');
Route::get('/stores_inventory/do_close_parent/{id}', [Inv_stores_inventoryController::class, 'do_close_parent'])->name('stores_inventory.do_close_parent');
Route::get('/stores_inventory/printsaleswina4/{id}/{size}', [Inv_stores_inventoryController::class, 'printsaleswina4'])->name('stores_inventory.printsaleswina4');
/*           end sservices_orders               */

/* start inv_production_order */

Route::get('/inv_production_order/index',[inv_production_orderController::class,'index'])->name('inv_production_order.index');
Route::get('/inv_production_order/create',[inv_production_orderController::class,'create'])->name('inv_production_order.create');
Route::post('/inv_production_order/store',[inv_production_orderController::class,'store'])->name('inv_production_order.store');
Route::get('/inv_production_order/edit/{id}',[inv_production_orderController::class,'edit'])->name('inv_production_order.edit');
Route::post('/inv_production_order/update/{id}',[inv_production_orderController::class,'update'])->name('inv_production_order.update');
Route::post('/inv_production_order/ajax_search',[inv_production_orderController::class,'ajax_search'])->name('inv_production_order.ajax_search');
Route::get('/inv_production_order/delete/{id}',[inv_production_orderController::class,'delete'])->name('inv_production_order.delete');
Route::post('/inv_production_order/show_more_detials', [Inv_production_orderController::class, 'show_more_detials'])->name('inv_production_order.show_more_detials');
Route::get('/inv_production_order/do_approve/{id}',[inv_production_orderController::class,'do_approve'])->name('inv_production_order.do_approve');
Route::get('/inv_production_order/do_closes_archive/{id}',[inv_production_orderController::class,'do_closes_archive'])->name('inv_production_order.do_closes_archive');

/* end inv_production_order */
/*         start  inv_production_lines                */
Route::get('/inv_production_lines/index', [Inv_production_linesController::class, 'index'])->name('inv_production_lines.index');
Route::get('/inv_production_lines/create', [Inv_production_linesController::class, 'create'])->name('inv_production_lines.create');
Route::post('/inv_production_lines/store', [Inv_production_linesController::class, 'store'])->name('inv_production_lines.store');
Route::get('/inv_production_lines/edit/{id}', [Inv_production_linesController::class, 'edit'])->name('inv_production_lines.edit');
Route::post('/inv_production_lines/update/{id}', [Inv_production_linesController::class, 'update'])->name('inv_production_lines.update');
Route::get('/inv_production_lines/delete/{id}', [Inv_production_linesController::class, 'delete'])->name('inv_production_lines.delete');
Route::post('/inv_production_lines/ajax_search', [Inv_production_linesController::class, 'ajax_search'])->name('inv_production_lines.ajax_search');
Route::get('/inv_production_lines/show/{id}', [Inv_production_linesController::class, 'show'])->name('inv_production_lines.show');
/*           end inv_production_lines                */
/*         start  Inv_production_exchange    صرف الخامات لخطوط الانتاج - الورش            */
Route::get('/inv_production_exchange/index', [Inv_production_exchangeController::class, 'index'])->name('inv_production_exchange.index');
Route::get('/inv_production_exchange/create', [Inv_production_exchangeController::class, 'create'])->name('inv_production_exchange.create');
Route::post('/inv_production_exchange/store', [Inv_production_exchangeController::class, 'store'])->name('inv_production_exchange.store');
Route::get('/inv_production_exchange/edit/{id}', [Inv_production_exchangeController::class, 'edit'])->name('inv_production_exchange.edit');
Route::post('/inv_production_exchange/update/{id}', [Inv_production_exchangeController::class, 'update'])->name('inv_production_exchange.update');
Route::get('/inv_production_exchange/delete/{id}', [Inv_production_exchangeController::class, 'delete'])->name('inv_production_exchange.delete');
Route::post('/inv_production_exchange/ajax_search', [Inv_production_exchangeController::class, 'ajax_search'])->name('inv_production_exchange.ajax_search');
Route::get('/inv_production_exchange/show/{id}', [Inv_production_exchangeController::class, 'show'])->name('inv_production_exchange.show');
Route::post('/inv_production_exchange/get_item_uoms', [Inv_production_exchangeController::class, 'get_item_uoms'])->name('inv_production_exchange.get_item_uoms');
Route::post('/inv_production_exchange/load_modal_add_details', [Inv_production_exchangeController::class, 'load_modal_add_details'])->name('inv_production_exchange.load_modal_add_details');
Route::post('/inv_production_exchange/Add_item_to_invoice', [Inv_production_exchangeController::class, 'Add_item_to_invoice'])->name('inv_production_exchange.Add_item_to_invoice');
Route::post('/inv_production_exchange/reload_itemsdetials', [Inv_production_exchangeController::class, 'reload_itemsdetials'])->name('inv_production_exchange.reload_itemsdetials');
Route::post('/inv_production_exchange/reload_parent_pill', [Inv_production_exchangeController::class, 'reload_parent_pill'])->name('inv_production_exchange.reload_parent_pill');
Route::post('/inv_production_exchange/load_edit_item_details', [Inv_production_exchangeController::class, 'load_edit_item_details'])->name('inv_production_exchange.load_edit_item_details');
Route::post('/inv_production_exchange/edit_item_details', [Inv_production_exchangeController::class, 'edit_item_details'])->name('inv_production_exchange.edit_item_details');
Route::get('/inv_production_exchange/delete_details/{id}/{id_parent}', [Inv_production_exchangeController::class, 'delete_details'])->name('inv_production_exchange.delete_details');
Route::post('/inv_production_exchange/do_approve/{id}', [Inv_production_exchangeController::class, 'do_approve'])->name('inv_production_exchange.do_approve');
Route::post('/inv_production_exchange/load_modal_approve_invoice', [Inv_production_exchangeController::class, 'load_modal_approve_invoice'])->name('inv_production_exchange.load_modal_approve_invoice');
Route::post('/inv_production_exchange/load_usershiftDiv', [Inv_production_exchangeController::class, 'load_usershiftDiv'])->name('inv_production_exchange.load_usershiftDiv');
Route::post('/inv_production_exchange/get_item_batches', [Inv_production_exchangeController::class, 'get_item_batches'])->name('inv_production_exchange.get_item_batches');
Route::get('/inv_production_exchange/printsaleswina4/{id}/{size}', [Inv_production_exchangeController::class, 'printsaleswina4'])->name('inv_production_exchange.printsaleswina4');
/*           end  Inv_production_exchange               */

/*         start  inv_production_Receive    استلام منتج تام من خط الانتاج الانتاج - الورش            */
Route::get('/inv_production_Receive/index', [inv_production_ReceiveController::class, 'index'])->name('inv_production_Receive.index');
Route::get('/inv_production_Receive/create', [inv_production_ReceiveController::class, 'create'])->name('inv_production_Receive.create');
Route::post('/inv_production_Receive/store', [inv_production_ReceiveController::class, 'store'])->name('inv_production_Receive.store');
Route::get('/inv_production_Receive/edit/{id}', [inv_production_ReceiveController::class, 'edit'])->name('inv_production_Receive.edit');
Route::post('/inv_production_Receive/update/{id}', [inv_production_ReceiveController::class, 'update'])->name('inv_production_Receive.update');
Route::get('/inv_production_Receive/delete/{id}', [inv_production_ReceiveController::class, 'delete'])->name('inv_production_Receive.delete');
Route::post('/inv_production_Receive/ajax_search', [inv_production_ReceiveController::class, 'ajax_search'])->name('inv_production_Receive.ajax_search');
Route::get('/inv_production_Receive/show/{id}', [inv_production_ReceiveController::class, 'show'])->name('inv_production_Receive.show');
Route::post('/inv_production_Receive/get_item_uoms', [inv_production_ReceiveController::class, 'get_item_uoms'])->name('inv_production_Receive.get_item_uoms');
Route::post('/inv_production_Receive/load_modal_add_details', [inv_production_ReceiveController::class, 'load_modal_add_details'])->name('inv_production_Receive.load_modal_add_details');
Route::post('/inv_production_Receive/add_new_details', [inv_production_ReceiveController::class, 'add_new_details'])->name('inv_production_Receive.add_new_details');
Route::post('/inv_production_Receive/reload_itemsdetials', [inv_production_ReceiveController::class, 'reload_itemsdetials'])->name('inv_production_Receive.reload_itemsdetials');
Route::post('/inv_production_Receive/reload_parent_pill', [inv_production_ReceiveController::class, 'reload_parent_pill'])->name('inv_production_Receive.reload_parent_pill');
Route::post('/inv_production_Receive/load_edit_item_details', [inv_production_ReceiveController::class, 'load_edit_item_details'])->name('inv_production_Receive.load_edit_item_details');
Route::post('/inv_production_Receive/edit_item_details', [inv_production_ReceiveController::class, 'edit_item_details'])->name('inv_production_Receive.edit_item_details');
Route::get('/inv_production_Receive/delete_details/{id}/{id_parent}', [inv_production_ReceiveController::class, 'delete_details'])->name('inv_production_Receive.delete_details');
Route::post('/inv_production_Receive/do_approve/{id}', [inv_production_ReceiveController::class, 'do_approve'])->name('inv_production_Receive.do_approve');
Route::post('/inv_production_Receive/load_modal_approve_invoice', [inv_production_ReceiveController::class, 'load_modal_approve_invoice'])->name('inv_production_Receive.load_modal_approve_invoice');
Route::post('/inv_production_Receive/load_usershiftDiv', [inv_production_ReceiveController::class, 'load_usershiftDiv'])->name('inv_production_Receive.load_usershiftDiv');
Route::post('/inv_production_Receive/get_item_batches', [inv_production_ReceiveController::class, 'get_item_batches'])->name('inv_production_Receive.get_item_batches');
Route::get('/inv_production_Receive/printsaleswina4/{id}/{size}', [inv_production_ReceiveController::class, 'printsaleswina4'])->name('inv_production_Receive.printsaleswina4');
/*           end  inv_production_Receive               */
/*         start  inv_stores_transfer       التحويل بين المخازن    */
Route::get('/inv_stores_transfer/index', [Inv_stores_transferController::class, 'index'])->name('inv_stores_transfer.index');
Route::get('/inv_stores_transfer/create', [Inv_stores_transferController::class, 'create'])->name('inv_stores_transfer.create');
Route::post('/inv_stores_transfer/store', [Inv_stores_transferController::class, 'store'])->name('inv_stores_transfer.store');
Route::get('/inv_stores_transfer/edit/{id}', [Inv_stores_transferController::class, 'edit'])->name('inv_stores_transfer.edit');
Route::post('/inv_stores_transfer/update/{id}', [Inv_stores_transferController::class, 'update'])->name('inv_stores_transfer.update');
Route::get('/inv_stores_transfer/delete/{id}', [Inv_stores_transferController::class, 'delete'])->name('inv_stores_transfer.delete');
Route::post('/inv_stores_transfer/ajax_search', [Inv_stores_transferController::class, 'ajax_search'])->name('inv_stores_transfer.ajax_search');
Route::get('/inv_stores_transfer/show/{id}', [Inv_stores_transferController::class, 'show'])->name('inv_stores_transfer.show');
Route::post('/inv_stores_transfer/get_item_uoms', [Inv_stores_transferController::class, 'get_item_uoms'])->name('inv_stores_transfer.get_item_uoms');
Route::post('/inv_stores_transfer/load_modal_add_details', [Inv_stores_transferController::class, 'load_modal_add_details'])->name('inv_stores_transfer.load_modal_add_details');
Route::post('/inv_stores_transfer/Add_item_to_invoice', [Inv_stores_transferController::class, 'Add_item_to_invoice'])->name('inv_stores_transfer.Add_item_to_invoice');
Route::post('/inv_stores_transfer/reload_itemsdetials', [Inv_stores_transferController::class, 'reload_itemsdetials'])->name('inv_stores_transfer.reload_itemsdetials');
Route::post('/inv_stores_transfer/reload_parent_pill', [Inv_stores_transferController::class, 'reload_parent_pill'])->name('inv_stores_transfer.reload_parent_pill');
Route::post('/inv_stores_transfer/load_edit_item_details', [Inv_stores_transferController::class, 'load_edit_item_details'])->name('inv_stores_transfer.load_edit_item_details');
Route::post('/inv_stores_transfer/edit_item_details', [Inv_stores_transferController::class, 'edit_item_details'])->name('inv_stores_transfer.edit_item_details');
Route::get('/inv_stores_transfer/delete_details/{id}/{id_parent}', [Inv_stores_transferController::class, 'delete_details'])->name('inv_stores_transfer.delete_details');
Route::post('/inv_stores_transfer/do_approve/{id}', [Inv_stores_transferController::class, 'do_approve'])->name('inv_stores_transfer.do_approve');
Route::post('/inv_stores_transfer/load_modal_approve_invoice', [Inv_stores_transferController::class, 'load_modal_approve_invoice'])->name('inv_stores_transfer.load_modal_approve_invoice');
Route::post('/inv_stores_transfer/load_usershiftDiv', [Inv_stores_transferController::class, 'load_usershiftDiv'])->name('inv_stores_transfer.load_usershiftDiv');
Route::post('/inv_stores_transfer/get_item_batches', [Inv_stores_transferController::class, 'get_item_batches'])->name('inv_stores_transfer.get_item_batches');
Route::get('/inv_stores_transfer/printsaleswina4/{id}/{size}', [Inv_stores_transferController::class, 'printsaleswina4'])->name('inv_stores_transfer.printsaleswina4');
/*           end  inv_stores_transfer               */

/*         start  inv_stores_transfer_incoming         أوامر تحويل مخزنية واردة    */
Route::get('/inv_stores_transfer_incoming/index', [Inv_stores_transferIncomingController::class, 'index'])->name('inv_stores_transfer_incoming.index');
Route::post('/inv_stores_transfer_incoming/ajax_search', [Inv_stores_transferIncomingController::class, 'ajax_search'])->name('inv_stores_transfer_incoming.ajax_search');
Route::get('/inv_stores_transfer_incoming/show/{id}', [Inv_stores_transferIncomingController::class, 'show'])->name('inv_stores_transfer_incoming.show');
Route::get('/inv_stores_transfer_incoming/printsaleswina4/{id}/{size}', [Inv_stores_transferIncomingController::class, 'printsaleswina4'])->name('inv_stores_transfer_incoming.printsaleswina4');
Route::get('/inv_stores_transfer_incoming/approve_one_details/{id}/{id_parent}', [Inv_stores_transferIncomingController::class, 'approve_one_details'])->name('inv_stores_transfer_incoming.approve_one_details');
Route::get('/inv_stores_transfer_incoming/cancel_one_details/{id}/{id_parent}', [Inv_stores_transferIncomingController::class, 'cancel_one_details'])->name('inv_stores_transfer_incoming.cancel_one_details');
Route::post('/inv_stores_transfer_incoming/load_cancel_one_details', [Inv_stores_transferIncomingController::class, 'load_cancel_one_details'])->name('inv_stores_transfer_incoming.load_cancel_one_details');
Route::post('/inv_stores_transfer_incoming/do_cancel_one_details/{id}/{id_parent}', [Inv_stores_transferIncomingController::class, 'do_cancel_one_details'])->name('inv_stores_transfer_incoming.do_cancel_one_details');

/*           end  inv_stores_transfer_incoming               */

});



Auth::routes();
Route::view('not-allowed', 'not_allowed');
