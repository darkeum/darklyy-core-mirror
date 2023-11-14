<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class CreateUsers extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('nick', 255);
            $table->string('password', 255);
            $table->string('tail', 55);
            $table->string('hash', 1024)->nullable();
            $table->string('email', 255);
            $table->string('provider', 255)->nullable();
            $table->string('social_id', 255)->nullable();
            $table->string('status', 255)->nullable();
            $table->integer('oid')->nullable();
            $table->string('icq', 55)->nullable();
            $table->string('phone', 55)->nullable();
            $table->string('phone2', 55)->nullable();
            $table->string('skype', 55)->nullable();
            $table->string('surname', 55)->nullable();
            $table->string('name', 55)->nullable();
            $table->string('ochestvo', 55)->nullable();
            $table->string('place', 255)->nullable();
            $table->date('birthday')->nullable();
            $table->integer('age')->nullable();
            $table->integer('sex')->nullable();
            $table->string('hobby', 255)->nullable();
            $table->text('signature')->nullable();
            $table->integer('points')->nullable()->default(0);
            $table->integer('carma')->nullable();
            $table->integer('user_comments')->nullable();
            $table->integer('user_news')->nullable();
            $table->integer('group')->nullable();
            $table->string('exgroup', 3)->nullable();
            $table->integer('last_visit')->nullable();
            $table->integer('regdate')->nullable();
            $table->integer('active')->nullable();
            $table->string('ip', 55)->nullable();
            $table->integer('hidden')->nullable()->default(0);
            $table->text('fields')->nullable();
            $table->string('regId', 256)->nullable();
            $table->integer('section')->nullable()->comment('Область работы');
            $table->string('utm_source', 1024)->nullable();
            $table->string('inn', 255)->nullable();

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
        Schema::dropIfExists('users');
    }
}
