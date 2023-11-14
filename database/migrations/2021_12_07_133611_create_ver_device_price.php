<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class CreateVerDevicePrice extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ver_device_price', function (Blueprint $table) {
            $table->id();
            $table->integer('vdid')->nullable();
            $table->integer('uid')->nullable();
            $table->integer('oid')->default(0);
            $table->integer('price')->nullable();
            $table->integer('nds')->nullable();
            $table->integer('status')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Откат миграции
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ver_device_price');
    }
}
