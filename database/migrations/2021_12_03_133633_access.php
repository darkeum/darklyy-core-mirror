<?php

use \Boot\System\Database\Migrations\Migration;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\Support\Facades\Schema;

class Access extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('access', function (Blueprint $table) {
            $table->id();
            $table->uid();
            $table->category();
            $table->string('title', 1024)->comment('Название права доступа');
            $table->string('name', 1024)->comment('Тэг права доступа');
            $table->org();
            $table->status();        
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
        Schema::dropIfExists('access');
    }
}
