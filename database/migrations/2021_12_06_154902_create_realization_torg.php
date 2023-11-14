<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class CreateRealizationTorg extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('realization_torg', function (Blueprint $table) {
            $table->id();
            $table->integer('realization_id');
            $table->string('base_doc', 2048);
            $table->string('base_numb', 1024);
            $table->date('base_date');
            $table->integer('oid_bank')->nullable();
            $table->integer('consignee')->comment('Грузополучатель');
            $table->integer('consignee_bank')->nullable()->comment('Счет грузополучателя');
            $table->string('oid_address', 2048)->nullable();
            $table->integer('org_bank')->nullable();
            $table->integer('shipper')->comment('Грузополучатель');
            $table->integer('shipper_bank')->nullable()->comment('Счет грузополучателя');
            $table->integer('leader')->comment('link to users_sign');
            $table->integer('buch')->comment('link to users_sign');
            $table->integer('ship')->nullable()->comment('link to users');
            $table->string('ship_post', 255)->nullable();
            $table->integer('procuratory_id')->nullable();

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
        Schema::dropIfExists('realization_torg');
    }
}
