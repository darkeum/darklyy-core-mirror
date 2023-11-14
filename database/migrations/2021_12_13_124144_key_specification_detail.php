<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class KeySpecificationDetail extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('specification_detail', function (Blueprint $table) {
            $table->foreign('specification_id')->references('id')->on('specification')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('cid')->references('id')->on('catalog')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Откат миграции
     *
     * @return void
     */
    public function down()
    {
        Schema::table('specification_detail', function (Blueprint $table) {
            $table->dropForeign(['specification_id']);
            $table->dropForeign(['cid']);
        });
    }
}
