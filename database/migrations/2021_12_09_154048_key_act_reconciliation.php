<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class KeyActReconciliation extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::table('act_reconciliation', function (Blueprint $table) {
            $table->foreign('org')->references('id')->on('org')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign('oid')->references('id')->on('organizations')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign('uid_created')->references('id')->on('users')->onUpdate('restrict')->onDelete('restrict');
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
        Schema::table('act_reconciliation', function (Blueprint $table) {
            $table->dropForeign(['org']);
            $table->dropForeign(['oid']);
            $table->dropForeign(['uid_created']);
            $table->dropForeign(['uid']);
        });
    }
}
