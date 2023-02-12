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
        Schema::create('sales_matrail_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('added_by');
            $table->integer('updated_by')->nullable();
            $table->integer('come_code');
            $table->date('date');
            $table->tinyInteger('active');
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
        Schema::dropIfExists('sales_matrail_types');
    }
};
