<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class KeySelectionDetail extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('selection_detail', function (Blueprint $table) {
            $table->foreign('selection_id')->references('id')->on('selection')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('cid')->references('id')->on('catalog')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Откат миграции
     *
     * @return void
     */
    public function down()
    {
        Schema::table('selection_detail', function (Blueprint $table) {
            $table->dropForeign(['selection_id']);
            $table->dropForeign(['cid']);
        });
    }
}
