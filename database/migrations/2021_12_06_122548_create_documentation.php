<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class CreateDocumentation extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('documentation', function (Blueprint $table) {
            $table->id();
            $table->string('title', 255)->comment('Заголовок');
            $table->integer('type')->default(1)->comment('Тип: 1 - обычный; 2 - видный только программистам');

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
        Schema::dropIfExists('documentation');
    }
}
