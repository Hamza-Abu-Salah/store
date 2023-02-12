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
        // جدول امر الجرد
        Schema::create('inv_stores_inventories', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('auto_serial');
            $table->date('inventory_date');
            $table->integer('store_id');//مخزن الجرد
            $table->tinyInteger('is_closed'); //هل امر الجرد مغلق او مفتوح
           $table->integer('cloased_by');
           $table->dateTime('closed_at');
            $table->decimal('total_cost_batches');
            $table->text('notes');
            $table->integer('added_by');
            $table->integer('updated_by')->nullable();
            $table->integer('come_code');
            $table->date('date');
            $table->tinyInteger('inventory_type');//واحد جرد يومي \\اثنين جرد اسبوعي \\ثلاثة جرد شهري\\4جرد سنوي
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
        Schema::dropIfExists('inv_stores_inventories');
    }
};
