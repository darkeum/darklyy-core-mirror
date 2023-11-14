<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class CreateControlStorageDetail extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('control_storage_detail', function (Blueprint $table) {
            $table->id();
            $table->integer('control_storage_id')->nullable();
            $table->integer('storage_id')->nullable();
            $table->integer('storage_detail_id')->nullable();
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
        Schema::dropIfExists('control_storage_detail');
    }
}
