<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class CreateGeoRegion extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('geo_region', function (Blueprint $table) {
            $table->id();
            $table->integer('gcid');
            $table->integer('code');
            $table->string('title', 1024);
            $table->integer('status')->default(0);
            
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
        Schema::dropIfExists('geo_region');
    }
}
