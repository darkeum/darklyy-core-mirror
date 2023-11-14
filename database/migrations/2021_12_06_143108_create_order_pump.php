<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class CreateOrderPump extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_pump', function (Blueprint $table) {
            $table->id();
            $table->dateTime('deleted_at')->nullable()->comment('Маркер удаления');
            $table->integer('order_id')->comment('Идентификатор заявки');
            $table->double('pressure')->nullable()->comment('Напор');
            $table->double('qPump')->nullable()->comment('Расход');
            $table->integer('type')->nullable()->comment('Тип');
            $table->integer('r1')->nullable()->comment('Частотный преобразователь');
            $table->integer('r2')->nullable()->comment('Напряжение');
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
        Schema::dropIfExists('order_pump');
    }
}
