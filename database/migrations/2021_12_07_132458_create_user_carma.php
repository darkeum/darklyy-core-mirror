<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class CreateUserCarma extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_carma', function (Blueprint $table) {
            $table->id();
            $table->integer('from');
            $table->integer('to');
            $table->string('text', 255);
            $table->string('do', 5)->nullable();
            $table->integer('time');
            
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
        Schema::dropIfExists('user_carma');
    }
}
