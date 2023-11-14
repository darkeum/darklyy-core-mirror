<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class CreateLangs extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('langs', function (Blueprint $table) {
            $table->id();
            $table->string('postId', 255)->nullable();
            $table->string('module', 255);
            $table->string('title', 255);
            $table->string('fulltitle', 255);
            $table->text('short', 255)->nullable();
            $table->text('full', 255)->nullable();
            $table->string('lang', 255);

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
        Schema::dropIfExists('langs');
    }
}
