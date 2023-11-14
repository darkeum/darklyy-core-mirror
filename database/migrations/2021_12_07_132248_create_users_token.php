<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class CreateUsersToken extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users_token', function (Blueprint $table) {
            $table->id();
            $table->integer('uid')->nullable();
            $table->string('token', 1024)->nullable();
            $table->string('platform', 256)->nullable();
            $table->string('device_id', 1024)->nullable();
            $table->dateTime('date')->useCurrent();
            $table->string('veritification_code', 256)->nullable();
            $table->integer('status')->nullable();

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
        Schema::dropIfExists('users_token');
    }
}
