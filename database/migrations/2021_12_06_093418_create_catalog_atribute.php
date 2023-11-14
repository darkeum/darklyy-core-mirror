<?php

use \Boot\System\Database\Migrations\Migration;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\Support\Facades\Schema;

class CreateCatalogAtribute extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('catalog_atribute', function (Blueprint $table) {
            $table->id();
            $table->text('cat')->nullable();
            $table->text('hidden')->nullable();
            $table->string('title', 1024)->nullable();
            $table->integer('type')->nullable();
            $table->longText('value')->nullable();
            $table->integer('uid')->nullable();
            $table->integer('position')->nullable();
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
        Schema::dropIfExists('catalog_atribute');
    }
}
