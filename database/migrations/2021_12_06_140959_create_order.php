<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class CreateOrder extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order', function (Blueprint $table) {
            $table->id();
            $table->dateTime('deleted_at')->nullable()->comment('Маркер удаления');
            $table->integer('uid')->nullable();
            $table->string('surname', 255)->nullable();
            $table->string('name', 255)->nullable();
            $table->string('ochestvo', 255)->nullable();
            $table->longText('fio_source')->nullable();
            $table->string('phone', 255);
            $table->longText('phone_source')->nullable();
            $table->string('email', 255)->nullable();
            $table->string('type', 1024);
            $table->text('message')->nullable();
            $table->string('org_name', 255)->nullable();
            $table->string('org_inn', 20)->nullable();
            $table->integer('org_id')->nullable();
            $table->string('object_name', 5000)->nullable();
            $table->string('object_address', 5000)->nullable();
            $table->integer('object_id')->nullable();
            $table->longText('request_source')->nullable();
            $table->date('visit_date')->nullable();
            $table->longText('read_uid')->nullable()->comment('Кто и когда прочитал');
            $table->integer('read_status')->default(0)->comment('Статус прочитанного');
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
        Schema::dropIfExists('order');
    }
}
