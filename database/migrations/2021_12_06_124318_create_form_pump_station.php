<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class CreateFormPumpStation extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('form_pump_station', function (Blueprint $table) {
            $table->id();
            $table->integer('fid');
            $table->integer('syn');
            $table->integer('sum');
            $table->integer('sumr');
            $table->integer('tocha');
            $table->integer('rashod');
            $table->integer('napor');
            $table->integer('pch');
            $table->text('comment');

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
        Schema::dropIfExists('form_pump_station');
    }
}
