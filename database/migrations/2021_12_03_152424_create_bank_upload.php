<?php

use \Boot\System\Database\Migrations\Migration;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\Support\Facades\Schema;

class CreateBankUpload extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bank_upload', function (Blueprint $table) {
            $table->id();
            $table->integer('uid')->nullable();
            $table->integer('org')->default(1);
            $table->dateTime('date')->nullable();
            $table->dateTime('date_add')->nullable();
            $table->string('balance_in', 1024)->nullable();
            $table->string('balance_out', 1024)->nullable();
            $table->string('file', 1024)->nullable();
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
        Schema::dropIfExists('bank_upload');
    }
}
