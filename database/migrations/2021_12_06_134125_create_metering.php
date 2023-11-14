<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class CreateMetering extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('metering', function (Blueprint $table) {
            $table->id();
            $table->integer('uid')->nullable();
            $table->double('r_min')->nullable();
            $table->double('r_max')->nullable();
            $table->integer('scheme')->default(1);
            $table->integer('type')->nullable();
            $table->integer('category')->nullable();
            $table->integer('aid_scheme')->nullable();
            $table->double('rs1_min')->nullable();
            $table->double('rs2_min')->nullable();
            $table->double('rs3_min')->nullable();
            $table->integer('rs1_cid')->nullable();
            $table->integer('rs2_cid')->nullable();
            $table->integer('rs3_cid')->nullable();
            $table->string('d1', 255)->nullable();
            $table->string('d2', 255)->nullable();
            $table->double('ddt')->nullable();
            $table->double('ldt')->nullable();
            $table->double('lbob')->nullable();
            $table->double('losn')->nullable();
            $table->double('luch')->nullable();
            $table->integer('dn94')->nullable();
            $table->integer('ldt94')->nullable();
            $table->integer('ddt94')->nullable();
            $table->dateTime('date')->nullable();
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
        Schema::dropIfExists('metering');
    }
}
