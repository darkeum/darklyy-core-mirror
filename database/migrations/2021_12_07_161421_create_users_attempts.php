<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class CreateUsersAttempts extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users_attempts', function (Blueprint $table) {
            $table->id();
            $table->char('ip', 39)->comment('IP адрес пользователя');
            $table->dateTime('expiredate')->comment('Время актуальности записи');
            $table->timestamps();
        });
    }

    /**
     * Откат миграции
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users_attempts');
    }
}
