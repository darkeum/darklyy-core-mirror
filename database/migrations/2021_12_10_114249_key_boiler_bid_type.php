<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class KeyBoilerBidType extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('boiler_bid_type', function (Blueprint $table) {
            $table->foreign('bid_id')->references('id')->on('boiler_bid')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Откат миграции
     *
     * @return void
     */
    public function down()
    {
        Schema::table('boiler_bid_type', function (Blueprint $table) {
            $table->dropForeign(['bid_id']);
        });
    }
}
