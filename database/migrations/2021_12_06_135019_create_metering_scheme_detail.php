<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class CreateMeteringSchemeDetail extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('metering_scheme_detail', function (Blueprint $table) {
            $table->id();
            $table->integer('scheme_id')->nullable();
            $table->integer('cid')->nullable();
            $table->decimal('count')->nullable();
            $table->integer('position')->nullable();
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
        Schema::dropIfExists('metering_scheme_detail');
    }
}
