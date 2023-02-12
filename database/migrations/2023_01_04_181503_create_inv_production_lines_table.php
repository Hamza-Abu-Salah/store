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
        Schema::create('inv_production_lines', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('production_lines_code');
            $table->string('name');
            $table->bigInteger('account_number');
            $table->tinyInteger('start_balance_status');
            $table->decimal('start_balance');
            $table->decimal('current_balance');
            $table->string('notes');
            $table->integer('added_by');
            $table->integer('updated_by')->nullable();
            $table->integer('come_code');
            $table->date('date')->nullable();
            $table->string('address');
            $table->string('phones');
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
        Schema::dropIfExists('inv_production_lines');
    }
};
