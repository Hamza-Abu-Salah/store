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
        Schema::create('services_with_orders', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('order_type'); //1-for us // 2-for other
            $table->bigInteger('auto_serial');
            $table->date('order_date'); //تاريخ الفاتورة
            $table->tinyInteger('is_approved')->default(0);
            $table->integer('come_code');
            $table->decimal('total_services');
            $table->decimal('total_befor_discount')->nullable();
            $table->tinyInteger('discount_type')->nullable(); //انواع الخصم //1-نسبة //2-خصم يدوي
            $table->decimal('discount_percent')->nullable();//rقيمة نسبة الخصم
            $table->decimal('discount_value')->nullable();// قيمة الخصم
            $table->decimal('tax_percent')->nullable();//نسبة الضريبة
            $table->integer('approved_by')->nullable();
            $table->decimal('tax_value')->nullable(); //قيمة الضريبة القيمة المضافة
            $table->string('notes')->nullable();
            $table->decimal('total_cost')->nullable();// القيمة الاجمالية النهائية للفاتورة
            $table->tinyInteger('is_account_number');
            $table->string('entity_name'); //اسم الجهة في حال انه ليس حساب مالي
            $table->bigInteger('account_number'); // رقم الحساب المقدم له الخدمة او المقدم لنا الخدمة
            $table->decimal('money_for_account')->nullable();
            $table->tinyInteger('pill_type'); // نوع الفاتورة //1-كاش 2-اجل
            $table->decimal('what_paid')->nullable();
            $table->decimal('what_remain')->nullable();
            $table->bigInteger('treasuries_transactions_id')->nullable();
            $table->integer('added_by');
            $table->integer('updated_by')->nullable();
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
        Schema::dropIfExists('services_with_orders');
    }
};
