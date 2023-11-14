<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class CreateSitemap extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sitemap', function (Blueprint $table) {
            $table->id();
            $table->string('module', 1024);
            $table->integer('pid');
            $table->string('loc', 1024);
            $table->double('priority')->nullable();
            $table->string('changefreq', 1024)->nullable();
            $table->dateTime('lastmod')->nullable();
            $table->string('title', 2048)->nullable();
            $table->string('description', 2048)->nullable();
            $table->string('keywords', 2048)->nullable();

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
        Schema::dropIfExists('sitemap');
    }
}
