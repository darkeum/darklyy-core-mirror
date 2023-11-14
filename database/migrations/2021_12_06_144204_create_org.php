<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class CreateOrg extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('org', function (Blueprint $table) {
            $table->id();
            $table->integer('oid');
            $table->integer('nds');
            $table->integer('nds_includes');
            $table->string('prefix', 256);
            $table->integer('taxation')->nullable()->comment('Тип налогообложения ');
            $table->string('logo', 256)->nullable();
            $table->string('bill_sign', 256)->nullable();
            $table->integer('accountant')->nullable();
            $table->integer('director')->nullable();
            $table->string('phone', 256)->nullable();
            $table->integer('stock_id_default')->nullable();
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
        Schema::dropIfExists('org');
    }
}
