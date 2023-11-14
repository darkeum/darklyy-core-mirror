<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class CreateCorpMeter extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('corp_meter', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->string('address', 255);
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
        Schema::dropIfExists('corp_meter');
    }
}
