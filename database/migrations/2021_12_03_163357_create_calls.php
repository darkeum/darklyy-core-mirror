<?php

use \Boot\System\Database\Migrations\Migration;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\Support\Facades\Schema;

class CreateCalls extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('calls', function (Blueprint $table) {
            $table->id();
            $table->string('src', 255);
            $table->string('dst', 255);
            $table->integer('calldate');
            $table->integer('duration');
            $table->integer('billsec');
            $table->string('uniqueid', 255);
            $table->string('recordingfile', 512);
            $table->integer('status');


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
        Schema::dropIfExists('calls');
    }
}
