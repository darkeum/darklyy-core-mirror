<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class KeyReestrDoc extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reestr_doc', function (Blueprint $table) {
            $table->foreign('reestr_id')->references('id')->on('reestr')->onUpdate('cascade')->onDelete('cascade');
            $table->index('type_id');
        });
    }

    /**
     * Откат миграции
     *
     * @return void
     */
    public function down()
    {
        Schema::table('reestr_doc', function (Blueprint $table) {
            $table->dropForeign(['reestr_id']);
            $table->dropIndex(['type_id']);
        });
    }
}
