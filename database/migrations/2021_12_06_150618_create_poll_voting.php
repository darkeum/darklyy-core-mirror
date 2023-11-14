<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class CreatePollVoting extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('poll_voting', function (Blueprint $table) {
            $table->id();
            $table->integer('uid');
            $table->integer('pid');
            $table->string('ip', 55);
            $table->string('time', 55);

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
        Schema::dropIfExists('poll_voting');
    }
}
