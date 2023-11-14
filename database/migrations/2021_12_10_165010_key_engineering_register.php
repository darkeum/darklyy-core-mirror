<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class KeyEngineeringRegister extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('engineering_register', function (Blueprint $table) {
            $table->unique('numb');
        });
    }

    /**
     * Откат миграции
     *
     * @return void
     */
    public function down()
    {
        Schema::table('engineering_register', function (Blueprint $table) {
            $table->dropUnique(['numb']);;
        });
    }
}
