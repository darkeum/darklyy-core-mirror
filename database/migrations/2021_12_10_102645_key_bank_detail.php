<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class KeyBankDetail extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bank_detail', function (Blueprint $table) {
            $table->foreign('org')->references('id')->on('org')->onDelete('restrict')->onUpdate('restrict');
            $table->foreign('organizations_bank_id')->references('id')->on('organizations_bank')->onDelete('restrict')->onUpdate('restrict');
            $table->foreign('payer')->references('id')->on('organizations')->onDelete('restrict')->onUpdate('restrict');
            $table->foreign('payer_bank')->references('id')->on('organizations_bank')->onDelete('restrict')->onUpdate('restrict');
            $table->foreign('recipient')->references('id')->on('organizations')->onDelete('restrict')->onUpdate('restrict');
            $table->foreign('recipient_bank')->references('id')->on('organizations_bank')->onDelete('restrict')->onUpdate('restrict');
            $table->index('hash');         
        });
    }

    /**
     * Откат миграции
     *
     * @return void
     */
    public function down()
    {
        Schema::table('access_users', function (Blueprint $table) {
            $table->dropForeign(['org']);
            $table->dropForeign(['organizations_bank_id']);
            $table->dropForeign(['payer']);
            $table->dropForeign(['payer_bank']);
            $table->dropForeign(['recipient']);
            $table->dropForeign(['recipient_bank']);
            $table->dropIndex(['hash']);
        });
    }
}
