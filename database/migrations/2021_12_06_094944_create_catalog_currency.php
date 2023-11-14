<?php

use \Boot\System\Database\Migrations\Migration;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\Support\Facades\Schema;

class CreateCatalogCurrency extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('catalog_currency', function (Blueprint $table) {
            $table->id();
            $table->integer('code')->nullable();
            $table->string('name', 256)->nullable();
            $table->string('short_name', 256)->nullable();
            $table->string('parameters', 1024)->nullable();
            $table->dateTime('date')->nullable();
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
        Schema::dropIfExists('catalog_currency');
    }
}
