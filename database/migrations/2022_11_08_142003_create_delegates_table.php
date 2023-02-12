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
        Schema::create('delegates', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('delegate_code');
            $table->string('name');
            $table->string('phones');
            $table->bigInteger('account_number');
            $table->tinyInteger('start_balance_status'); //'e 1-credit -2 debit 3-balanced'
            $table->decimal('start_balance'); // دائن او مدين او متزن اول المدة'
            $table->decimal('current_balance');
            $table->string('notes');
            $table->integer('added_by');
            $table->integer('updated_by');
            $table->tinyInteger('active')->default(0);
            $table->integer('come_code');
            $table->date('date');
            $table->integer('city_id');
            $table->string('address');
            $table->tinyInteger('percent_type'); //'نوع عمولة المندوب  بشكل عام\r\nواحد اجر ثابت لكل فاتورة - لو اتنين  نسبة بكل فاتورة	',
            $table->decimal('percent_collect_commission'); //'نسبة المندوب بالتحصيل  الفواتير الاجل او الكاش',
            $table->decimal('percent_salaes_commission_kataei'); //'نسبة عمولة المندوب بالمبيعات قطاعلي	',
            $table->decimal('percent_salaes_commission_nosjomla'); //'عمول المندوب بمبيعات نص الجملة	',
            $table->decimal('percent_salaes_commission_jomla'); // 'نسبة عمولة المندوب بالمبيعات بالجملة	'
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
        Schema::dropIfExists('delegates');
    }
};
