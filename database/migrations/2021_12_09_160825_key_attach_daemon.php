<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class KeyAttachDaemon extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::table('attach_daemon', function (Blueprint $table) {
            $table->foreign('attach_id')->references('id')->on('attach')->onUpdate('cascade')->onDelete('cascade');
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
        $table->dropForeign(['attach_id']);
        });
    }
}
