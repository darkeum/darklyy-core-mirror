<?php

use \Boot\System\Database\Migrations\Migration;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\Support\Facades\Schema;

class CreateBankDetail extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bank_detail', function (Blueprint $table) {
            $table->id();
            $table->string('hash', 255)->nullable();
            $table->string('numb', 1024)->nullable();
            $table->integer('org')->default(1);
            $table->integer('organizations_bank_id');
            $table->string('type', 1024)->nullable();
            $table->integer('pay_in')->nullable();
            $table->date('date')->nullable();
            $table->date('date_create')->nullable();
            $table->string('sum', 1024)->nullable();
            $table->integer('payer')->nullable();
            $table->integer('payer_bank')->nullable();
            $table->integer('recipient')->nullable();
            $table->integer('recipient_bank')->nullable();
            $table->string('comment', 2048)->nullable();
            $table->integer('upload_id')->nullable();
            $table->string('module', 255)->nullable()->comment('Связь. Имя модуля');
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
        Schema::dropIfExists('bank_detail');
    }
}
