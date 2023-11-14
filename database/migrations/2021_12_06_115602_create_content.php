<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class CreateContent extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('content', function (Blueprint $table) {
            $table->id();
            $table->string('translate', 255);
            $table->string('cat', 200);
            $table->string('fulltitle', 255)->nullable();
            $table->string('description', 255)->nullable();
            $table->string('keywords', 255)->nullable();
            $table->integer('active');
            $table->string('date', 55)->nullable();
            $table->integer('comments')->nullable()->default(0);
            $table->string('preview_og', 1024)->nullable();
            $table->string('theme', 255);
            $table->integer('views')->default(0);
            $table->integer('status')->default(1);

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
        Schema::dropIfExists('content');
    }
}
