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
        Schema::create('sales_invoices_returns', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('return_type'); //    واحد مرتجع باصل الفاتورة --  اثنان مرتجع عام
            $table->integer('sales_matrial_types'); //('فئة الفاتورة');
            $table->bigInteger('auto_serial');
            $table->date('invoice_date'); //'تاريخ الفاتورة',
            $table->bigInteger('customer_code'); //'كود العميل',
            $table->bigInteger('delegate_code'); //كود المندوب
            $table->tinyInteger('is_approved')->default(0);
            $table->integer('come_code');
            $table->string('notes')->nullable();
            $table->tinyInteger('discount_type'); //'نواع الخصم - واحد خصم نسبة  - اثنين خصم يدوي قيمة',
            $table->decimal('discount_percent'); //'قيمة نسبة الخصم',
            $table->decimal('discount_value'); //'قيمة الخصم'
            $table->decimal('tax_percent');  //'نسبة الضريبة ',
            $table->decimal('total_cost_items'); // 'اجمالي الاصناف فقط'
            $table->decimal('tax_value'); // 'قيمة الضريبة القيمة المضافة'
            $table->decimal('total_befor_discount'); //
            $table->decimal('total_cost'); //'القيمة الاجمالية النهائية للفاتورة',
            $table->bigInteger('account_number'); //
            $table->decimal('money_for_account');
            $table->tinyInteger('pill_type'); //'نوع الفاتورة - كاش او اجل  - واحد واثنين'
            $table->decimal('what_paid');
            $table->decimal('what_remain');
            $table->bigInteger('treasuries_transactions_id');
            $table->decimal('customer_balance_befor'); // 'حالة رصيد العميل قبل الفاتروة',
            $table->decimal('customer_balance_after'); //'حالة رصيد العميل بعد الفاتروة',
            $table->integer('added_by');
            $table->integer('updated_by');
            $table->date('date');
            $table->integer('approved_by');
            $table->tinyInteger('is_has_customer'); //هل الفاتورة مرتبطة بعميل-واحد يبقى نعم -صفر عميل طياري بدون عميل
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
        Schema::dropIfExists('sales_invoices_returns');
    }
};
