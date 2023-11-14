<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class CreateObjectSection extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('object_section', function (Blueprint $table) {
            $table->id();
            $table->integer('object_id')->nullable();
            $table->integer('section')->nullable();
            $table->integer('mso')->nullable();
            $table->integer('sys')->nullable();
            $table->integer('itp')->nullable();
            $table->integer('mts')->nullable();
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
        Schema::dropIfExists('object_section');
    }
}
