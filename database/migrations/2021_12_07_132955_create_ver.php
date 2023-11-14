<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class CreateVer extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ver', function (Blueprint $table) {
            $table->id();
            $table->dateTime('deleted_at')->nullable()->comment('Маркер удаления');
            $table->integer('uid')->nullable();
            $table->integer('oid')->nullable();
            $table->integer('iprice')->nullable()->default(0);
            $table->integer('date')->nullable();
            $table->integer('date_end')->nullable()->default(0);
            $table->string('secret_key', 1024)->nullable();
            $table->string('total', 255)->nullable();
            $table->text('comment')->nullable();
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
        Schema::dropIfExists('ver');
    }
}
