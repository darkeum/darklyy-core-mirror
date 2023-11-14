<?php

use \Boot\System\Database\Migrations\Migration;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\Support\Facades\Schema;

class CreateAttach extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attach', function (Blueprint $table) {
            $table->id();
            $table->string('name', 1024)->comment('Наименование файла');
            $table->string('url', 1024)->comment('URL файла');
            $table->bigIncrements('pid')->nullable()->comment('Идентификатор записи');
            $table->string('module', 55)->nullable()->comment('Модуль');
            $table->integer('downloads')->nullable()->default(0)->comment('Количество загрузок');
            $table->boolean('daemon')->default(0)->comment('Выполнение фоновой загрузки');
            $table->string('class_name', 255)->nullable()->comment('Имя класса FTP');
            $table->boolean('access')->nullable()->default(0)->comment('Доступность файла');
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
        Schema::dropIfExists('attach');
    }
}
