<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class KeyUsers extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->unique('nick2');
            $table->unique('email');
            $table->unique('phone');
            $table->index('nick');
            $table->index('ip');
            $table->index('active');
        });
    }

    /**
     * Откат миграции
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['nick2']);
            $table->dropUnique(['email']);
            $table->dropUnique(['phone']);
            $table->dropIndex(['nick']);
            $table->dropIndex(['ip']);
            $table->dropIndex(['active']);
        });
    }
}
