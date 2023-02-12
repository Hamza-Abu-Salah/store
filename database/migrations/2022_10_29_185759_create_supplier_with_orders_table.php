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
        Schema::create('supplier_with_orders', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('order_type');
            $table->bigInteger('auto_serial');
            $table->string('DOC_NO');
            $table->date('order_date');
            $table->bigInteger('supplier_code');
            $table->tinyInteger('is_approved')->default(0);
            $table->integer('come_code');
            $table->decimal('total_befor_discount')->nullable();
            $table->tinyInteger('discount_type')->nullable();
            $table->decimal('discount_percent')->nullable();
            $table->decimal('discount_value')->nullable();
            $table->decimal('tax_percent')->nullable();
            $table->integer('approved_by')->nullable();
            $table->decimal('tax_value')->nullable();
            $table->string('notes')->nullable();
            $table->bigInteger('store_id');
            $table->decimal('total_cost')->nullable();
            $table->bigInteger('account_number');
            $table->decimal('money_for_account')->nullable();
            $table->tinyInteger('pill_type');
            $table->decimal('what_paid')->nullable();
            $table->decimal('what_remain')->nullable();
            $table->bigInteger('treasuries_transactions_id')->nullable();
            $table->decimal('Supplier_balance_befor')->nullable();
            $table->decimal('Supplier_balance_after')->nullable();
            $table->integer('added_by');
            $table->integer('updated_by')->nullable();
            $table->decimal('total_cost_items');
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
        Schema::dropIfExists('supplier_with_orders');
    }
};
