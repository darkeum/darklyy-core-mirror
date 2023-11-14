<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class CreateSelectionRequest extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('selection_request', function (Blueprint $table) {
            $table->id();
            $table->dateTime('deleted_at')->nullable()->comment('Маркер удаления');
            $table->integer('uid')->nullable()->comment('Идентификатор пользователя');
            $table->integer('oid')->nullable()->comment('Идентификатор контрагента');
            $table->integer('object')->nullable()->comment('Идентификатор объекта');
            $table->integer('type')->default(1)->comment('Тип расчета (1 - ИТП, 2 - Блок, 3 - Свободный расчет)');
            $table->text('detail_save')->nullable();
            $table->text('detail_free')->nullable();
            $table->text('datail_editor')->nullable();
            $table->text('detail_idata')->nullable();
            $table->text('detail_idatageneral')->nullable();
            $table->text('detail_code')->nullable();
            $table->text('detail_scheme')->nullable();
            $table->longText('requestData')->nullable();
            $table->text('view_request')->nullable()->comment('Сохранение данных view_request');
            $table->text('comment')->nullable();
            $table->text('report')->nullable();
            $table->integer('report_status')->default(0);
            $table->dateTime('date')->nullable();
            $table->integer('daemon')->default(0);
            $table->longText('resultData')->nullable();
            $table->integer('status')->nullable();

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
        Schema::dropIfExists('selection_request');
    }
}
