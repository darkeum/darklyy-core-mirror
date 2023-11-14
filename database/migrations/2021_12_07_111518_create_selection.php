<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class CreateSelection extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('selection', function (Blueprint $table) {
            $table->id();
            $table->string('code', 255);
            $table->integer('type')->nullable();
            $table->integer('uid')->nullable();
            $table->dateTime('date')->nullable();
            $table->integer('aid_scheme')->nullable();
            $table->integer('aid_size')->nullable();
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
        Schema::dropIfExists('selection');
    }
}
