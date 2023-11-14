<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class CreateCorpMeterDetail extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('corp_meter_detail', function (Blueprint $table) {
            $table->id();
            $table->integer('cid');
            $table->integer('mid');
            $table->string('number', 255);
            $table->integer('type');
            $table->integer('date_last');
            $table->integer('date_next');
            $table->integer('periud');
            $table->integer('uid');

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
        Schema::dropIfExists('corp_meter_detail');
    }
}
