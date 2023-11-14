<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class CreateDaemon extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('daemon', function (Blueprint $table) {
            $table->id();
            $table->dateTime('deleted_at')->nullable()->comment('Маркер удаления');
            $table->integer('uid');
            $table->integer('pid')->nullable();
            $table->string('title', 1024)->nullable();
            $table->string('command', 1024);
            $table->text('comment')->nullable();
            $table->integer('autostart')->default(1)->comment('1 автостарт включен, 0 - выключен');
            $table->integer('status')->default(1)->comment('0 - остановлено в ручную, 1 - нормальная работа');

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
        Schema::dropIfExists('daemon');
    }
}
