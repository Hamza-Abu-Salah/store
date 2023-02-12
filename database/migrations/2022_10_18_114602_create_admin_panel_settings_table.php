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
        Schema::create('admin_panel_settings', function (Blueprint $table) {
            $table->id();
            $table->string('system_name');
            $table->string('photo');
            $table->tinyInteger('active')->default(1);
            $table->string('general_alert');
            $table->string('address');
            $table->string('phone');
            $table->bigInteger('customer_parent_account_number');
            $table->bigInteger('suppliers_parent_account_number');
            $table->bigInteger('delegate_parent_account_number'); //رقم الحساب المالي لحساب الاب للمناديب
            $table->bigInteger('employees_parent_account_number'); //رقم الحساب المالي للموظفين الأب
            $table->bigInteger('production_lines_parent_account');
            $table->integer('added_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->integer('come_code');
            $table->text('notes')->nullable();
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
        Schema::dropIfExists('admin_panel_settings');
    }
};
