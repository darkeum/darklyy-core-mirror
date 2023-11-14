<?php

use \Boot\System\Database\Migrations\Migration;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\Support\Facades\Schema;

class CreateBoardThreads extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('board_threads', function (Blueprint $table) {
            $table->id();
            $table->integer('forum');
            $table->string('title', 55);
            $table->integer('poster');
            $table->string('startTime', 55);
            $table->string('lastTime', 55);
            $table->string('lastPoster', 55);
            $table->integer('views');
            $table->integer('replies');
            $table->integer('important')->default(0);
            $table->integer('closed')->default(0);
            $table->float('score');
            $table->smallInteger('votes');
            $table->string('icon', 44);
            $table->string('closetime', 55);

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
        Schema::dropIfExists('board_threads');
    }
}
