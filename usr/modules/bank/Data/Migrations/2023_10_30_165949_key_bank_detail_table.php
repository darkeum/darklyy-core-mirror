<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2023 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Golovin Egor
*/

use Boot\Support\Facades\Schema;
use Boot\System\Database\Schema\Blueprint;
use Boot\System\Database\Migrations\Migration;

class KeyBankDetailTable extends Migration
{
    /**
     * Запуск миграции
     */
    public function up(): void
    {
        Schema::table('act_reconciliation', function (Blueprint $table) {
            $table->foreign('org')->references('id')->on('org')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign('payer')->references('id')->on('organizations')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign('recipient')->references('id')->on('organizations')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign('upload_id')->references('id')->on('bank_upload')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign('recipient_bank')->references('id')->on('organizations_bank')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign('payer_bank')->references('id')->on('organizations_bank')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Отмена миграции.
     */
    public function down(): void
    {
        Schema::table('act_reconciliation', function (Blueprint $table) {
            $table->dropForeign(['org']);
            $table->dropForeign(['payer']);
            $table->dropForeign(['recipient']);
            $table->dropForeign(['upload_id']);
            $table->dropForeign(['recipient_bank']);
            $table->dropForeign(['payer_bank']);
        });
    }
}
