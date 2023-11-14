<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class KeySupportMessage extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('support_message', function (Blueprint $table) {
            $table->foreign('support_id')->references('id')->on('support')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign('uid')->references('id')->on('users')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign('attach_id')->references('id')->on('attach')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Откат миграции
     *
     * @return void
     */
    public function down()
    {
        Schema::table('support', function (Blueprint $table) {
            $table->dropForeign(['support_id']);
            $table->dropForeign(['uid']);
            $table->dropForeign(['attach_id']);
        });
    }
}
