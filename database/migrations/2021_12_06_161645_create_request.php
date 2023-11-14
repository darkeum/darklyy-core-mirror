<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class CreateRequest extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('request', function (Blueprint $table) {
            $table->id();
            $table->dateTime('deleted_at')->nullable()->comment('Маркер удаления');
            $table->integer('uid_dir')->default(0)->comment('Постановщик');
            $table->integer('uid_exec')->nullable()->comment('Исполнитель');
            $table->string('title', 1024)->nullable()->comment('Заголовок');
            $table->string('title_user', 1024)->nullable();
            $table->text('request')->nullable()->comment('Текст заявки');
            $table->text('request_data')->nullable();
            $table->longText('result')->nullable();
            $table->integer('oid')->nullable();
            $table->integer('object')->nullable()->comment('Объект');
            $table->integer('org')->default(1);
            $table->integer('type')->nullable()->comment('Тип заявки: 1 - Свободная; 2 - Атри-проект;');
            $table->integer('aid')->nullable()->comment('Финальный файл');
            $table->integer('final_cid')->nullable()->comment('cid создаваемого');
            $table->integer('pid')->nullable();
            $table->text('specification')->nullable()->default('{}');
            $table->dateTime('date')->nullable()->comment('Дата создания');
            $table->integer('status')->nullable()->default(1)->comment('Статусы: 1 - Заявка создана; 2 - Заявка взята; 3 - Заявка завершена 4 - Автоматическая заявка');
            
            

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
        Schema::dropIfExists('request');
    }
}
