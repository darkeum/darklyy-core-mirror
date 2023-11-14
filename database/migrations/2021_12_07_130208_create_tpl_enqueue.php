<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class CreateTplEnqueue extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tpl_enqueue', function (Blueprint $table) {
            $table->id();
            $table->string('type', 255);
            $table->string('title', 1024);
            $table->string('file', 2048);
            $table->integer('async')->default(0);
            $table->string('modify', 255);
            $table->dateTime('date');
            $table->integer('version');
            $table->string('cache_key', 256);
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
        Schema::dropIfExists('tpl_enqueue');
    }
}
