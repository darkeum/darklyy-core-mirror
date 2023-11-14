<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class CreatePresent extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('present', function (Blueprint $table) {
            $table->id();
            $table->integer('uid');
            $table->integer('type');
            $table->integer('active')->default(0);
            $table->text('data')->nullable();
            $table->text('data2')->nullable();
            $table->integer('count')->default(1);
            $table->text('comment')->nullable();

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
        Schema::dropIfExists('present');
    }
}
