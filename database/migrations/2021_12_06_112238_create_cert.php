<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class CreateCert extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cert', function (Blueprint $table) {
            $table->id();
            $table->integer('aid')->nullable();
            $table->integer('uid')->nullable();
            $table->integer('type')->nullable();
            $table->dateTime('date_add')->nullable();
            $table->dateTime('date_edit')->nullable();
            $table->dateTime('date_start')->nullable();
            $table->dateTime('date_end')->nullable();
            $table->text('issue')->nullable();
            $table->string('numb', 1024)->nullable();
            $table->text('comment')->nullable();
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
        Schema::dropIfExists('cert');
    }
}
