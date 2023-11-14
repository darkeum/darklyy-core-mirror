<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class CreateOrderValve extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_valve', function (Blueprint $table) {
            $table->id();
            $table->dateTime('deleted_at')->nullable()->comment('Маркер удаления');
            $table->integer('order_id')->comment('Идентификатор заявки');
            $table->string('nagr', 255)->nullable()->comment('Нарузка');
            $table->string('t1', 255)->nullable()->comment('Температура в подающем трубопровод');
            $table->string('t2', 255)->nullable()->comment('Температура в обратном трубопроводе');
            $table->string('q', 255)->nullable()->comment('Расход сетевого теплоносителя');
            $table->string('p1', 255)->nullable()->comment('Давление перед клапаном');
            $table->string('p2', 255)->nullable()->comment('Давление после клапана');
            $table->string('tmax', 255)->nullable()->comment('Максимальная температура среды');
            $table->string('pressure_drop', 255)->nullable()->comment('Расчетный перепад на полностью открытом клапане');
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
        Schema::dropIfExists('order_valve');
    }
}
