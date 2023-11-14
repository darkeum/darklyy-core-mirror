<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class KeyScadaTags extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('scada_tags', function (Blueprint $table) {
            $table->foreign('scada_id')->references('id')->on('scada')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Откат миграции
     *
     * @return void
     */
    public function down()
    {
        Schema::table('scada_tags', function (Blueprint $table) {
            $table->dropForeign(['scada_id']);
        });
    }
}
