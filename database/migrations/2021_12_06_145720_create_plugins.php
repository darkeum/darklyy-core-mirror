<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class CreatePlugins extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('plugins', function (Blueprint $table) {
            $table->id();
            $table->string('title', 55)->nullable();
            $table->text('content')->nullable();
            $table->string('file', 55)->nullable();
            $table->tinyInteger('priority')->nullable()->unsigned();
            $table->string('type', 55)->nullable();
            $table->string('service', 44)->nullable();
            $table->string('showin', 255)->nullable();
            $table->string('unshow', 255)->nullable();
            $table->string('groups', 255)->nullable();
            $table->tinyInteger('free')->nullable()->unsigned();
            $table->integer('categories')->nullable();
            $table->integer('xfields')->nullable();
            $table->integer('menu')->nullable();
            $table->integer('sitemap')->default(1);
            $table->tinyInteger('active')->unsigned();

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
        Schema::dropIfExists('plugins');
    }
}
