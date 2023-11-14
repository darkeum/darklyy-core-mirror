<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class KeyLangs extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('langs', function (Blueprint $table) {
            $table->index('postId');
            $table->index('module');
            $table->index('title');
        });
    }

    /**
     * Откат миграции
     *
     * @return void
     */
    public function down()
    {
        Schema::table('langs', function (Blueprint $table) {
            $table->dropIndex(['postId']);
            $table->dropIndex(['module']);
            $table->dropIndex(['title']);
        });
    }
}
