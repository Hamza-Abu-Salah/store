<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inv_production_exchanges', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('order_type');//واحد-صرف خامات من المخزن للخط//اثنين مرتجع خامات من المخزن من الخط للمخزن
            $table->bigInteger('auto_serial');
            $table->bigInteger('inv_production_order_auto_serial');
            $table->date('order_date');//تاريخ الفاتورة
            $table->bigInteger('production_lines_code');
            $table->tinyInteger('is_approved');
            $table->integer('come_code');
            $table->string('notes')->nullable();
            $table->tinyInteger('discount_type')->nullable();//انواع الخصم->//واحد-خصم نسبة//اثنين-خصم يدوي
            $table->decimal('discount_percent');//قيمة نسبة الخصم
            $table->decimal('discount_value');//قيمة الخصم
            $table->decimal('tax_percent');//نسبة الضريبة
            $table->decimal('total_cost_items');//اجمالي الاصناف فقط
            $table->decimal('tax_value');//قيمة الضريبة القيمة المضافة
            $table->decimal('total_befor_discount');
            $table->decimal('total_cost');
            $table->bigInteger('account_number');
            $table->decimal('money_for_account');
            $table->tinyInteger('pill_type');//واحد-كاش//اثنين-اجل
            $table->decimal('what_paid');
            $table->decimal('what_remain');
            $table->bigInteger('treasuries_transactions_id');
            $table->decimal('Supplier_balance_befor');//حالة رصيد المورد قبل الفاتورة
            $table->decimal('Supplier_balance_after');//حالة رصيد المورد بعد الفاتورة
            $table->integer('added_by');
            $table->integer('updated_by')->nullable();
            $table->bigInteger('store_id');//كود المخزن المستلم للفاتورة
            $table->integer('approved_by');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('inv_production_exchanges');
    }
};
