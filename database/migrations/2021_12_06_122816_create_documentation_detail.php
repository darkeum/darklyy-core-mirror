<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class CreateDocumentationDetail extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('documentation_detail', function (Blueprint $table) {
            $table->id();
            $table->integer('did')->comment('documentation_id');
            $table->string('title', 255)->comment('Заголовок');
            $table->text('documentation')->comment('Документация');
            $table->integer('uid')->comment('Создатель');
            $table->dateTime('date')->comment('Дата создания');
            $table->dateTime('date_edit')->comment('Дата последнего редактирования');
            $table->integer('moderation')->default(0)->comment('Модерация. Видимость для обычных пользователей');

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
        Schema::dropIfExists('documentation_detail');
    }
}
