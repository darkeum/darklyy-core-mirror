<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class CreateSelectionArea extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('selection_area', function (Blueprint $table) {
            $table->id();
            $table->dateTime('deleted_at')->nullable()->comment('Маркер удаления');
            $table->integer('uid')->comment('Идентификатор ответственного');
            $table->string('title', 1024)->comment('Название');
            $table->integer('object_id')->comment('Идентификатор объекта');
            $table->integer('oid')->comment('Идентификатор организации');
            $table->longText('save_area')->nullable()->comment('Сохраненная область');
            $table->integer('status')->default(1)->comment('Статус');

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
        Schema::dropIfExists('selection_area');
    }
}
