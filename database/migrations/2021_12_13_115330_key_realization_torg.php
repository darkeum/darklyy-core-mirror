<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class KeyRealizationTorg extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('realization_torg', function (Blueprint $table) {
            $table->unique('realization_id');
        });
    }

    /**
     * Откат миграции
     *
     * @return void
     */
    public function down()
    {
        Schema::table('realization_torg', function (Blueprint $table) {
            $table->dropUnique(['realization_id']);
        });
    }
}
