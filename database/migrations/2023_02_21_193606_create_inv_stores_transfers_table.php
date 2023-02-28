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
        Schema::create('inv_stores_transfers', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('auto_serial');
            $table->integer('transfer_from_store_id');//مخزن تحويل
            $table->integer('transfer_to_store_id');//مخزن استلام
            $table->date('order_date');//تاريخ التحويل
            $table->tinyInteger('is_approved');
            $table->integer('come_code');
            $table->string('notes')->nullable();
            $table->decimal('total_cost_items');//اجمالي الاصناف فقط
            $table->integer('added_by');
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
        Schema::dropIfExists('inv_stores_transfers');
    }
};
