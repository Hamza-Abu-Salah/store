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
        Schema::create('treasuries_delivery', function (Blueprint $table) {
            $table->id();
            $table->integer('treasuries_id');
            $table->integer('treasuries_can_delivery_id');
            $table->integer('added_by');
            $table->integer('updated_by')->nullable();
            $table->integer('come_code');
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
        Schema::dropIfExists('treasuries_delivery');
    }
};
