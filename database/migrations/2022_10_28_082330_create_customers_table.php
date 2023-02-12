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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->bigInteger('parent_account_number')->nullable();
            $table->bigInteger('account_number');
            $table->string('address')->nullable();
            $table->string('phones')->nullable();
            $table->bigInteger('customer_code');
            $table->decimal('start_balance'); // 'دائن او مدين او متزن اول المدة',
            $table->decimal('current_balance')->default(0);
            $table->bigInteger('other_table_FK')->nullable();
            $table->string('notes')->nullable();
            $table->integer('added_by');
            $table->integer('updated_by')->nullable();
            $table->integer('come_code');
            $table->tinyInteger('start_balance_status'); // 1-credit -2 debit 3-balanced'
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
        Schema::dropIfExists('customers');
    }
};
