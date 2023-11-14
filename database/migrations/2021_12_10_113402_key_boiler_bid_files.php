<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class KeyBoilerBidFiles extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('boiler_bid_files', function (Blueprint $table) {
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
        Schema::table('boiler_bid_files', function (Blueprint $table) {
            $table->dropForeign(['attach_id']);
        });
    }
}
