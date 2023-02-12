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
        Schema::create('services_with_orders_details', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('services_with_orders_auto_serial');
            $table->integer('service_id');
            $table->tinyInteger('order_type');
            $table->string('notes');
            $table->decimal('total');
            $table->integer('added_by');
            $table->integer('updated_by')->nullable();
            $table->integer('come_code');
            $table->date('date');
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
        Schema::dropIfExists('services_with_orders_details');
    }
};
