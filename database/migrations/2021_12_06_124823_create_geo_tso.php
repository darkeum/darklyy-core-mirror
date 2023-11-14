<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class CreateGeoTso extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('geo_tso', function (Blueprint $table) {
            $table->id();
            $table->integer('gcitid');
            $table->string('title', 1024);
            $table->integer('scheme')->nullable();
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
        Schema::dropIfExists('geo_tso');
    }
}
