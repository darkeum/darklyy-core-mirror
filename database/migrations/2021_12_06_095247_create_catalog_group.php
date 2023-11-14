<?php

use \Boot\System\Database\Migrations\Migration;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\Support\Facades\Schema;

class CreateCatalogGroup extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('catalog_group', function (Blueprint $table) {
            $table->id();
            $table->string('title', 124)->nullable();
            $table->text('short')->nullable();
            $table->text('full')->nullable();
            $table->string('preview', 1024)->nullable();
            $table->date('date')->nullable();
            $table->integer('cat')->nullable();
            $table->integer('min_price')->nullable();
            $table->integer('uid')->nullable();
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
        Schema::dropIfExists('catalog_group');
    }
}
