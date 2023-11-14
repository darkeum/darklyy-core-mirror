<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class CreateMeteringScheme extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('metering_scheme', function (Blueprint $table) {
            $table->id();
            $table->integer('uid')->nullable();
            $table->string('title', 1024)->nullable();
            $table->integer('aid_scheme')->nullable();
            $table->integer('aid_dt')->nullable();
            $table->integer('aid_dd')->nullable();
            $table->integer('aid_auto')->nullable();
            $table->integer('aid_el')->nullable();
            $table->integer('aid_pd')->nullable();
            $table->integer('aid_shu')->nullable();
            $table->integer('aid_pl')->nullable();
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
        Schema::dropIfExists('metering_scheme');
    }
}
