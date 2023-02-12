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
        Schema::create('inv_production_orders', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('auto_serial');
            $table->text('production_plane');
            $table->tinyInteger('state');// واحد قيد المراجعة/اثنين معتمد وجاري التنفيد/ثلاثة تم الانتهاء/اربعة تم الالغاء
            $table->tinyInteger('is_approved')->default(0);//هل تم اعتماده
            $table->integer('added_by');
            $table->integer('updated_by')->nullable();
            $table->integer('come_code');
            $table->integer('approved_by')->nullable();//مين الي اعتمده
            $table->dateTime('approved_at')->nullable();
            $table->tinyInteger('is_closed')->default(0);//هل مغلق
            $table->tinyInteger('closed_by')->nullable();//هل مغلق
            $table->dateTime('closed_at')->nullable();//هل مغلق
            $table->date('date')->nullable();
            $table->date('production_plane_date');
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
        Schema::dropIfExists('inv_production_orders');
    }
};
