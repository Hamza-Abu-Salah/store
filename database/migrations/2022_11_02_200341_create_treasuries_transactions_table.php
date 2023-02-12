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
        Schema::create('treasuries_transactions', function (Blueprint $table) {
            $table->id();
            $table->integer('treasuries_id');
            $table->integer('mov_type'); //'نوع حركة النقدية ',
            $table->bigInteger('the_foregin_key')->nullable(); //'كود الجدول الاخر المرتبط بالحركة',
            $table->bigInteger('account_number'); //'رقم الحساب المالي ',
            $table->tinyInteger('is_account'); //'هل هو حساب مالي',
            $table->bigInteger('admin_shift_id'); //'كود الشفت للمستخدم',
            $table->tinyInteger('is_approved');
            $table->bigInteger('shift_code');
            $table->date('move_date');
            $table->decimal('money_for_account'); //'قيمة المبلغ المستحق للحساب او علي الحساب',
            $table->decimal('money'); // 'قيمة المبلغ المصروف او المحصل بالخزنة',
            $table->string('byan');
            $table->integer('added_by');
            $table->integer('updated_by');
            $table->integer('come_code');
            $table->bigInteger('isal_number');
            $table->bigInteger('auto_serial');
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
        Schema::dropIfExists('treasuries_transactions');
    }
};
