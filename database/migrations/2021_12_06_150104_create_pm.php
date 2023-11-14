<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class CreatePm extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pm', function (Blueprint $table) {
            $table->id();
            $table->integer('toid');
            $table->integer('fromid');
            $table->text('message')->nullable();
            $table->string('time', 55);
            $table->smallInteger('status');

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
        Schema::dropIfExists('pm');
    }
}
