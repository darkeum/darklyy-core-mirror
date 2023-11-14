<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class CreateNotifDaemon extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notif_daemon', function (Blueprint $table) {
            $table->id();
            $table->integer('notif_id');
            $table->dateTime('deleted_at')->nullable()->comment('Маркер удаления');
            $table->string('platform', 255);

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
        Schema::dropIfExists('notif_daemon');
    }
}
