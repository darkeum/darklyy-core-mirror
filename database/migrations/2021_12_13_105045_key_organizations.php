<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class KeyOrganizations extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('organizations', function (Blueprint $table) {
            $table->unique('inn');
            $table->foreign('reestr_id')->references('id')->on('reestr')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign('default_responsible')->references('id')->on('users')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Откат миграции
     *
     * @return void
     */
    public function down()
    {
        Schema::table('organizations', function (Blueprint $table) {
            $table->dropUnique(['inn']);
            $table->dropForeign(['reestr_id']);
            $table->dropForeign(['default_responsible']);
        });
    }
}
