<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class CreateNotifSend extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notif_send', function (Blueprint $table) {
            $table->id();
            $table->integer('nid')->comment('notif_id');
            $table->integer('pid');
            $table->string('module');
            $table->string('type');
            $table->dateTime('date');

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
        Schema::dropIfExists('notif_send');
    }
}
