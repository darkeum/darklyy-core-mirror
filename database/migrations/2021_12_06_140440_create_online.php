<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class CreateOnline extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('online', function (Blueprint $table) {
            $table->id();
            $table->integer('uid');
            $table->integer('time')->nullable();
            $table->string('ip', 15);
            $table->integer('group');
            $table->dateTime('time_marker')->useCurrent();
            $table->string('name', 255)->nullable();
            $table->text('agent')->nullable();
            $table->string('url', 255);

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
        Schema::dropIfExists('online');
    }
}
