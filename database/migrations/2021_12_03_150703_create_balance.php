<?php

use \Boot\System\Database\Migrations\Migration;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\Support\Facades\Schema;

class CreateBalance extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('balance', function (Blueprint $table) {
            $table->id();
            $table->integer('org');
            $table->integer('oid');
            $table->integer('reestr_id')->nullable();
            $table->decimal('balance');
            $table->date('date');
            $table->integer('pid')->nullable();
            $table->string('module', 255)->nullable();
            $table->integer('uid');
            $table->string('action', 255);

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
        Schema::dropIfExists('balance');
    }
}
