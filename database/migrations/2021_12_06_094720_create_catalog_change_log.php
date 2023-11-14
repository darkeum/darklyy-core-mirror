<?php

use \Boot\System\Database\Migrations\Migration;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\Support\Facades\Schema;

class CreateCatalogChangeLog extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('catalog_change_log', function (Blueprint $table) {
            $table->id();
            $table->integer('cid');
            $table->integer('uid');
            $table->dateTime('date');
            $table->dateTime('date_confirm')->nullable();
            $table->integer('type')->default(1)->comment('Тип заявки. 1 - добавление; 2 - изменение');
            $table->integer('status')->default(1);

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
        Schema::dropIfExists('catalog_change_log');
    }
}
