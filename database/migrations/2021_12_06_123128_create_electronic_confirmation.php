<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class CreateElectronicConfirmation extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('electronic_confirmation', function (Blueprint $table) {
            $table->id();
            $table->string('module', 1024);
            $table->integer('pid');
            $table->integer('uid');
            $table->integer('used')->default(0);
            $table->integer('active')->default(0)->comment('Подтверждение');
            $table->string('code', 1024);

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
        Schema::dropIfExists('electronic_confirmation');
    }
}
