<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class CreatePtoPrice extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pto_price', function (Blueprint $table) {
            $table->id();
            $table->integer('pto_id');
            $table->integer('type_in')->comment('Тип теплоносителя на входе');
            $table->integer('type_out')->comment('Тип теплоносителя на выходе');
            $table->double('power')->comment('Мощность');
            $table->integer('t1');
            $table->integer('t2');
            $table->integer('t3');
            $table->integer('t4');
            $table->decimal('price')->comment('Прайс в евро');
            $table->integer('aid_calc')->comment('Файл расчета');
            $table->integer('d1')->comment('ДУ 1');
            $table->integer('d2')->comment('ДУ 2');
            $table->date('date')->useCurrent();
            $table->integer('uid');
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
        Schema::dropIfExists('pto_price');
    }
}
