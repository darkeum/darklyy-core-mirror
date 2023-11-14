<?php

use \Boot\System\Database\Migrations\Migration;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\Support\Facades\Schema;

class CreateBoardForums extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('board_forums', function (Blueprint $table) {
            $table->id();
            $table->string('title', 55);
            $table->string('description', 255);
            $table->integer('pid');
            $table->string('type', 3);
            $table->smallInteger('active');
            $table->smallInteger('open');
            $table->integer('threads');
            $table->integer('posts');
            $table->string('lastPost', 55);
            $table->string('lastPoster', 255);
            $table->integer('lastTid');
            $table->string('lastSubject', 255);
            $table->string('password', 255);
            $table->string('rulestitle', 255);
            $table->text('rules');
            $table->integer('position');

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
        Schema::dropIfExists('board_forums');
    }
}
