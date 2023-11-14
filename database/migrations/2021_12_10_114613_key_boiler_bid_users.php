<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class KeyBoilerBidUsers extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('boiler_bid_users', function (Blueprint $table) {
            $table->foreign('bid_id')->references('id')->on('boiler_bid')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('uid')->references('id')->on('users')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Откат миграции
     *
     * @return void
     */
    public function down()
    {
        Schema::table('boiler_bid_users', function (Blueprint $table) {
            $table->dropForeign(['bid_id']);
            $table->dropForeign(['uid']);
        });
    }
}
