<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class CreateSpecificationSave extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('specification_save', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255)->nullable()->default('Спецификация');
            $table->text('data')->nullable();
            $table->dateTime('date')->nullable();
            $table->integer('uid')->nullable();
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
        Schema::dropIfExists('specification_save');
    }
}
