<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class CreateScada extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('scada', function (Blueprint $table) {
            $table->id();
            $table->dateTime('deleted_at')->nullable()->comment('Маркер удаления');
            $table->string('title', 1024)->nullable();
            $table->integer('uid')->nullable();
            $table->integer('oid')->nullable();
            $table->integer('object')->nullable();
            $table->text('access')->nullable();
            $table->string('ip', 255)->nullable();
            $table->integer('port')->nullable();
            $table->integer('status')->nullable();
            $table->dateTime('date')->nullable();

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
        Schema::dropIfExists('scada');
    }
}
