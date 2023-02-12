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
        Schema::create('inv_itemcard_batches', function (Blueprint $table) {
            $table->id();
            $table->integer('store_id'); //'كود المخزن',
            $table->integer('item_code'); //'كود الصنف الالي ',
            $table->integer('inv_uoms_id'); //'كود الوحده الاب ',
            $table->decimal('unit_cost_price'); //'سعر الشراء للوحده',
            $table->decimal('quantity'); //'الكمية بالوحده الاب',
            $table->decimal('total_cost_price'); //'اجمالي سعر شراء الباتش ككل',
            $table->date('production_date');
            $table->date('expired_date');
            $table->integer('come_code');
            $table->bigInteger('auto_serial');
            $table->integer('added_by');
            $table->integer('updated_by');
            $table->tinyInteger('is_send_to_archived')->default(0);
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
        Schema::dropIfExists('inv_itemcard_batches');
    }
};
