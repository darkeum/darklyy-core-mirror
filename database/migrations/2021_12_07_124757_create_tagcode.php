<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class CreateTagcode extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tagcode', function (Blueprint $table) {
            $table->id();
            $table->integer('uid');
            $table->text('desc');
            $table->string('title', 255);
            $table->integer('active');
            $table->string('tag', 255);
            $table->string('date', 44);

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
        Schema::dropIfExists('tagcode');
    }
}
