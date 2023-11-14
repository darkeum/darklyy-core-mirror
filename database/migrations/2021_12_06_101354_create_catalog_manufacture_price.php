<?php

use \Boot\System\Database\Migrations\Migration;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\Support\Facades\Schema;

class CreateCatalogManufacturePrice extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('catalog_manufacture_price', function (Blueprint $table) {
            $table->id();
            $table->integer('manufacture_id');
            $table->integer('price_type_id');
            $table->string('formula', 1024);
            $table->integer('uid');
            $table->dateTime('date_add');
            $table->dateTime('deleted_at')->nullable();

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
        Schema::dropIfExists('catalog_manufacture_price');
    }
}
