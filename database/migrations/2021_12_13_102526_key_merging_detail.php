<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class KeyMergingDetail extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('merging_detail', function (Blueprint $table) {
            $table->foreign('merging_id')->references('id')->on('merging')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Откат миграции
     *
     * @return void
     */
    public function down()
    {
        Schema::table('merging_detail', function (Blueprint $table) {
            $table->dropForeign(['merging_id']);
        });
    }
}
