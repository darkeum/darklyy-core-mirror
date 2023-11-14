<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class CreateCertRelation extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cert_relation', function (Blueprint $table) {
            $table->id();
            $table->integer('cid')->nullable();
            $table->integer('cert_id')->nullable();
            $table->string('hash', 1024)->nullable();
            $table->date('date')->nullable();
            $table->string('status')->nullable();

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
        Schema::dropIfExists('cert_relation');
    }
}
