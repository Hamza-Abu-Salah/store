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
        Schema::create('inv_production_exchange_details', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('inv_production_exchange_auto_serial');
            $table->tinyInteger('order_type');
            $table->integer('come_code');
            $table->decimal('deliverd_quantity');
            $table->integer('uom_id');
            $table->tinyInteger('isparentuom');
            $table->integer('unit_price');
            $table->tinyInteger('inMain_or_retail_uom');
            $table->decimal('total_price');
            $table->date('order_date');
            $table->integer('added_by');
            $table->integer('updated_by')->nullable();
            $table->bigInteger('item_code');
            $table->bigInteger('batch_auto_serial')->nullable();
            $table->date('production_date');
            $table->date('expire_date');
            $table->tinyInteger('item_card_type');
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
        Schema::dropIfExists('inv_production_exchange_details');
    }
};
