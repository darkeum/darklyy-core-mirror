<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class CreateNotif extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notif', function (Blueprint $table) {
            $table->id();
            $table->integer('deleted_at')->nullable()->comment('Маркер удаления');
            $table->integer('uid')->nullable();
            $table->string('module', 256)->nullable();
            $table->text('url')->nullable();
            $table->text('text')->nullable();
            $table->text('title')->nullable();
            $table->string('icon', 1024)->nullable();
            $table->dateTime('date')->nullable();
            $table->dateTime('read_date')->nullable();
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
        Schema::dropIfExists('notif');
    }
}
