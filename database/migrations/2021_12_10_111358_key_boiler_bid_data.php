<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class KeyBoilerBidData extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::table('boiler_bid_data', function (Blueprint $table) {
            $table->foreign('bid_type_id')->references('id')->on('boiler_bid_type')->onUpdate('cascade')->onDelete('cascade');                        
        });
    }

    /**
     * Откат миграции
     *
     * @return void
     */
    public function down()
    {
        Schema::table('boiler_bid_data', function (Blueprint $table) {
            $table->dropForeign(['bid_type_id']);
        });
    }
}
