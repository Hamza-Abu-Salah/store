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
        Schema::create('account_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->tinyInteger('active');
            $table->tinyInteger('relatediternalaccounts'); //هل الحساب يتم تكويده من شاشته الداخلية ام من خلال الشاشة الرئيسية للحسابات---->واحد->داخلي/صفر->من شاشة الرئيسية
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
        Schema::dropIfExists('account_types');
    }
};
