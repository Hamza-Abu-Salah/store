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
        Schema::create('accounts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('account_type');
            $table->bigInteger('parent_account_number')->nullable();
            $table->bigInteger('account_number');
            $table->decimal('start_balance');//رصيد أول المدة للحساب
            $table->decimal('current_balance')->default(0);
            $table->bigInteger('other_table_FK')->nullable();
            $table->string('notes')->nullable();
            $table->integer('added_by');
            $table->integer('updated_by')->nullable();
            $table->integer('come_code');
            $table->tinyInteger('start_balance_status');// حالة رصيد اول المدة
            $table->date('date');
            $table->tinyInteger('active')->default(0);
            $table->tinyInteger('is_parent')->default(0);
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
        Schema::dropIfExists('accounts');
    }
};
