<?php

use \Boot\System\Database\Migrations\Migration;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\Support\Facades\Schema;

class CreateBoardPosts extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('board_posts', function (Blueprint $table) {
            $table->id();
            $table->integer('tid');
            $table->text('message');
            $table->integer('uid');
            $table->string('username', 255);
            $table->string('ip', 255);
            $table->string('time', 255);
            $table->text('files');
            $table->string('visible', 1);
            $table->string('editUser', 55);
            $table->string('editReason', 255);

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
        Schema::dropIfExists('board_posts');
    }
}
