<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class CreateObject extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('object', function (Blueprint $table) {
            $table->id();
            $table->string('name', 1024)->nullable();
            $table->string('address', 255)->nullable();
            $table->string('coords', 255)->nullable();
            $table->text('comment')->nullable();
            $table->string('cat', 255)->nullable()->default(0);
            $table->string('id_client', 255)->nullable()->default(0);
            $table->integer('id_con')->nullable();
            $table->integer('id_zas')->nullable();
            $table->integer('id_pod')->nullable();
            $table->integer('id_gpr')->nullable();
            $table->integer('id_spr')->nullable();
            $table->integer('date')->nullable();
            $table->integer('uid')->nullable();
            $table->string('preview', 255)->nullable();
            $table->integer('status')->nullable()->default(1);

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
        Schema::dropIfExists('object');
    }
}
