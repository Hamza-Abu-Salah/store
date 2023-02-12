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
        Schema::create('sales_invoices_return_details', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('sales_invoices_auto_serial');
            $table->integer('store_id');
            $table->integer('come_code');
            $table->decimal('quantity');
            $table->integer('uom_id');
            $table->tinyInteger('isparentuom'); //'1-main -0 retail'
            $table->decimal('unit_price');
            $table->decimal('total_price');
            $table->date('invoice_date');
            $table->integer('added_by');
            $table->integer('updated_by')->nullable();
            $table->bigInteger('item_code');
            $table->tinyInteger('sales_item_type'); //نوع البيع مع الصنف - واحد->قطاعي-اثنين->نصف جملة -ثلاثة->جملة
            $table->bigInteger('batch_auto_serial')->nullable(); //'رقم الباتش بالمخزن التي تم خروج الصنف منها ',
            $table->date('production_date')->nullable();
            $table->date('expire_date')->nullable();
            $table->tinyInteger('item_card_type');
            $table->tinyInteger('is_normal_orOther'); //واحد بيع عادي - اثنين->بونص - ثلاثة->دعاية - هالك
            $table->date('date');
            $table->decimal('unit_cost_price'); //في حالة المرتجع بدون تحديد باتش للمرتجع
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
        Schema::dropIfExists('sales_invoices_return_details');
    }
};
