<?php

use \Boot\System\Database\Migrations\Migration;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\Support\Facades\Schema;

class CreateBoardPermissions extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('board_permissions', function (Blueprint $table) {
            $table->id();
            $table->integer('fid');
            $table->integer('gid');
            $table->integer('allowView');
            $table->integer('allowRead');
            $table->integer('allowCreate');
            $table->integer('allowReply');
            $table->integer('allowEdit');
            $table->integer('allowModer');
            $table->integer('allowAttach');

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
        Schema::dropIfExists('board_permissions');
    }
}
