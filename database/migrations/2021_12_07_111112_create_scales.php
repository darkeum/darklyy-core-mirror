<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class CreateScales extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('scales', function (Blueprint $table) {
            $table->id();
            $table->dateTime('deleted_at')->nullable()->comment('Маркер удаления');
            $table->string('types', 1024)->nullable()->comment('Тип');
            $table->string('stand', 1024)->nullable()->comment('Стандарт');
            $table->string('dia', 1024)->nullable()->comment('Размер');
            $table->string('dlina', 1024)->nullable()->comment('Длина');
            $table->string('material', 1024)->nullable()->comment('Материал');
            $table->longText('item')->nullable()->comment('Данные');
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
        Schema::dropIfExists('scales');
    }
}
