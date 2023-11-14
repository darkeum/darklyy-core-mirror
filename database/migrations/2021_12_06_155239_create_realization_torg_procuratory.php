<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class CreateRealizationTorgProcuratory extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('realization_torg_procuratory', function (Blueprint $table) {
            $table->id();
            $table->integer('oid');
            $table->string('number', 255);
            $table->date('date_from')->comment('От');
            $table->date('date_to')->comment('До');
            $table->string('issued_by', 1024)->comment('Кем выдан');
            $table->string('issued_to', 1024)->comment('Кому выдан');

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
        Schema::dropIfExists('realization_torg_procuratory');
    }
}
