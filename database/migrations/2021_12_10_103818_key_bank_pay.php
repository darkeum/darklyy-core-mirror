<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class KeyBankPay extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bank_pay', function (Blueprint $table) {
            $table->foreign('bank_detail_id')->references('id')->on('bank_detail')->onDelete('cascade')->onUpdate('cascade');                        
        });
    }

    /**
     * Откат миграции
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bank_pay', function (Blueprint $table) {
            $table->dropForeign(['bank_detail_id']);
        });
    }
}
