<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class CreateSubscribe extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('subscribe', function (Blueprint $table) {
            $table->id();
            $table->dateTime('deleted_at')->nullable()->comment('Маркер удаления');
            $table->string('type', 1024)->comment('Метод отправки');
            $table->string('receiver', 1024)->comment('Куда отправлять');
            $table->integer('comfirm')->default(0)->comment('Подтверждение подписки');
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
        Schema::dropIfExists('subscribe');
    }
}
