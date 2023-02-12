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
        Schema::create('inv_item_cards', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->tinyInteger('item_type');
            $table->integer('inv_itemcard_categories_id');
            $table->bigInteger('parent_inv_itemcard_id')->nullable();
            $table->tinyInteger('does_has_retailunit');
            $table->integer('retail_uom_id')->nullable();
            $table->integer('uom_id');
            $table->tinyInteger('has_fixced_price')->default(0); //'هل للصنف سعر ثابت بالفواتير  او قابل للتغير بالفواتير',
            $table->decimal('price'); //'السعر القطاعي بوحدة القياس الاساسية	',
            $table->decimal('gomla_price'); //'السعر جملة بوحدة القياس الاساسية	',
            $table->decimal('cost_price'); //'متوسط التكلفة للصنف بالوحدة الاساسية	',
            $table->decimal('cost_price_retail')->nullable(); // 'متوسط التكلفة للصنف بوحدة قياس التجزئة	',
            $table->decimal('QUENTITY')->nullable(); //'الكمية بالوحده الاب',
            $table->decimal('QUENTITY_Retail')->nullable(); // 'كمية التجزئة المتبقية من الوحده الاب في حالة وجود وحده تجزئة للصنف',
            $table->decimal('QUENTITY_all_Retails')->nullable(); //'كل الكمية محولة بوحده التجزئة '
            $table->decimal('All_QUENTITY');
            $table->decimal('nos_gomla_price'); // 'سعر النص جملة مع الوحده الاب	',
            $table->decimal('price_retail')->nullable(); //'السعر القطاعي بوحدة قياس التجزئة	',
            $table->decimal('nos_gomla_price_retail')->nullable(); //'سعر النص جملة قطاعي مع الوحده التجزئة	',
            $table->decimal('gomla_price_retail')->nullable();
            $table->decimal('retail_uom_quntToParent')->nullable();
            $table->integer('added_by');
            $table->integer('updated_by')->nullable();
            $table->tinyInteger('active');
            $table->date('date');
            $table->integer('come_code');
            $table->string('barcode');
            $table->bigInteger('item_code');
            $table->string('photo')->nullable();
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
        Schema::dropIfExists('inv_item_cards');
    }
};
