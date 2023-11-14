<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class KeyAttach extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::table('attach', function (Blueprint $table) {
            $table->index('url');
            $table->index('pub_id');
        });
    }

    /**
     * Откат миграции
     *
     * @return void
     */
    public function down()
    {
        Schema::table('attach', function (Blueprint $table) {
            $table->dropIndex(['url']);
            $table->dropIndex(['pub_id']);
        });
    }
}
