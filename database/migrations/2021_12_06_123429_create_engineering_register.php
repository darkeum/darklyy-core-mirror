<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class CreateEngineeringRegister extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('engineering_register', function (Blueprint $table) {
            $table->id();
            $table->string('numb', 255)->comment('Шфир');
            $table->text('section')->default('[]')->comment('Раздел');
            $table->integer('object')->nullable()->comment('Объект');
            $table->date('date')->comment('Дата');
            $table->text('uid')->default('[]');
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
        Schema::dropIfExists('engineering_register');
    }
}
