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
        Schema::create('admin_shifts', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->integer('treasuries_id');
            $table->bigInteger('shift_code');
            $table->decimal('treasuries_balnce_in_shift_start');//رصيد الخزنة في بداية استلام الشفت للمستخدم
            $table->dateTime('start_date');//توقيت بداية الشفت
            $table->dateTime('end_date');//توقيت نهاية الشفت
            $table->tinyInteger('is_finished')->default(0); // هل تم انتهاء الشفت
            $table->tinyInteger('is_delivered_and_review')->nullable();//هل تم مراجعة واستلام شفت شفت الخزنة
            $table->integer('delivered_to_admin_id')->nullable(); //كود المستخدم الدي تسلم هدا الشفت وراجعه
            $table->integer('delivered_to_admin_sift_id')->nullable();//كود الشفت الدي تسلم هدا الشفت وراجعه
            $table->integer('delivered_to_treasuries_id')->nullable();//كود الخزنة التي راجعت واستلمت هدا الشفت
            $table->decimal('money_should_deviled')->nullable();//النقدية الفعلية التي بفترض ان تسلم
            $table->decimal('what_realy_delivered')->nullable();//المبلغ الفعلي الدي تم تسليمه
            $table->tinyInteger('money_state');//صفر-متزن//واحد-يوجد عجز //اثنان-يوجد زيادة
            $table->decimal('money_state_value');//قيمة العجز او الزادة ان وجد
            $table->tinyInteger('receive_type');//واحد-استلام عنفس الخزنة//اثنان -استلام عخزنة اخري
            $table->dateTime('review_receive_date');//تاريخ مراجعة واستلام هدا الشفت
            $table->bigInteger('treasuries_transactions_id');//رقم الايصال بجدول تحصيل النقدية لحركة الخزن
            $table->integer('added_by');
            $table->string('notes');
            $table->integer('come_code');
            $table->integer('updated_by');
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
        Schema::dropIfExists('admin_shifts');
    }
};
