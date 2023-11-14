<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class CreateCertType extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cert_type', function (Blueprint $table) {
            $table->id();
            $table->string('title', 1024)->nullable();
            $table->string('print', 1024)->nullable();
            $table->integer('status')->nullable();
            $table->integer('date_start')->default(1);
            $table->integer('date_end')->default(1);

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
        Schema::dropIfExists('cert_type');
    }
}
