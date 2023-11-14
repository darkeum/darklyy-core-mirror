<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class CreateLetter extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('letter', function (Blueprint $table) {
            $table->id();
            $table->integer('type')->comment('Тип письма: 1 - входящие; 2 - исходящие');
            $table->integer('uid')->nullable();
            $table->integer('oid_from')->nullable()->comment('от кого');
            $table->integer('oid_for')->nullable()->comment('для кого');
            $table->date('date')->nullable();
            $table->dateTime('date_create');

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
        Schema::dropIfExists('letter');
    }
}
