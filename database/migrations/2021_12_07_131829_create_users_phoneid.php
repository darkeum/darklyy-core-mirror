<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class CreateUsersPhoneid extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users_phoneid', function (Blueprint $table) {
            $table->id();
            $table->integer('uid');
            $table->string('phoneid', 255);
            $table->integer('status');

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
        Schema::dropIfExists('users_phoneid');
    }
}
