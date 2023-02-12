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
        Schema::create('inv_item_card_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('added_by');
            $table->integer('updated_by')->nullable();
            $table->tinyInteger('active');
            $table->date('date');
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
        Schema::dropIfExists('inv_item_card_categories');
    }
};
