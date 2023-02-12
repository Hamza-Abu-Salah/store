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
        Schema::create('inv_stores_inventory_details', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('inv_stores_inventory_auto_serial');
            $table->bigInteger('item_code');
            $table->integer('inv_uoms_id');
            $table->bigInteger('batch_auto_serial');
            $table->decimal('old_quantity'); //الكمية القديمة الي عندي
            $table->decimal('new_quantity');
            $table->decimal('diffrent_quantity'); //الكمية بين الكمية الجديدة والكمية القديمة
            $table->decimal('unit_cost_price');
            $table->decimal('total_cost_price');
            $table->date('production_date');
            $table->date('expired_date');
            $table->string('notes')->nullable();
            $table->tinyInteger('is_closed');
            $table->integer('added_by');
            $table->integer('updated_by')->nullable();
            $table->integer('come_code');
            $table->dateTime('cloased_by')->nullable();
            $table->dateTime('closed_at')->nullable();
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
        Schema::dropIfExists('inv_stores_inventory_details');
    }
};
