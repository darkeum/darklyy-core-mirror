<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class CreatePriceSite extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('price_site', function (Blueprint $table) {
            $table->id();
            $table->integer('catalog_manufacture_id');
            $table->string('site', 2048);
            $table->text('file')->nullable();
            $table->text('comment')->nullable();


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
        Schema::dropIfExists('price_site');
    }
}
