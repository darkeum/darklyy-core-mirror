<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class CreateReestr extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reestr', function (Blueprint $table) {
            $table->id();
            $table->dateTime('deleted_at')->nullable()->comment('Маркер удаления');
            $table->integer('type');
            $table->string('type_doc', 255)->default('out');
            $table->integer('numb')->nullable();
            $table->string('other_numb', 1024)->nullable();
            $table->integer('uid');
            $table->integer('oid');
            $table->integer('org');
            $table->integer('object')->nullable();
            $table->integer('a_doc')->nullable()->comment('id договора');
            $table->integer('a_scan')->nullable();
            $table->integer('sign')->default(0)->comment('есть ли подписи');
            $table->integer('scan')->default(0)->comment('Есть ли скан');
            $table->text('doc')->nullable()->comment('Документы');
            $table->text('comment')->nullable();
            $table->integer('date');
            $table->date('date_view');
            $table->integer('date_create');
            $table->integer('auto')->nullable();
            $table->integer('spec_numb_counter')->default(0)->comment('Счётчик спецификаций');
            $table->integer('status');

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
        Schema::dropIfExists('reestr');
    }
}
