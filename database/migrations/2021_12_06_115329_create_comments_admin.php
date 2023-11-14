<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class CreateCommentsAdmin extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('comments_admin', function (Blueprint $table) {
            $table->id();
            $table->integer('uid')->nullable();
            $table->text('text')->nullable();
            $table->dateTime('date')->nullable();
            $table->string('module', 255)->nullable();
            $table->integer('module_id')->nullable();

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
        Schema::dropIfExists('comments_admin');
    }
}
