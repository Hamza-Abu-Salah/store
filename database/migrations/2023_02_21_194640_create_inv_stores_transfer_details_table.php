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
        Schema::create('inv_stores_transfer_details', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('inv_stores_transfer_id');
            $table->bigInteger('inv_stores_transfer_auto_serial');
            $table->integer('come_code');
            $table->decimal('deliverd_quantity');
            $table->integer('uom_id');
            $table->tinyInteger('isparentuom');
            $table->decimal('unit_price');
            $table->decimal('total_price');
            $table->date('order_date');//تاريخ التحويل
            $table->tinyInteger('is_approved');
            $table->string('notes')->nullable();
            $table->decimal('total_cost_items');//اجمالي الاصناف فقط
            $table->integer('added_by');
            $table->bigInteger('item_code');
            $table->date('production_date');
            $table->date('expire_date');
            $table->tinyInteger('item_card_type');
            $table->bigInteger('transfer_from_batch_id');
            $table->bigInteger('transfer_to_batch_id');
            $table->integer('updated_by')->nullable();
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
        Schema::dropIfExists('inv_stores_transfer_details');
    }
};
