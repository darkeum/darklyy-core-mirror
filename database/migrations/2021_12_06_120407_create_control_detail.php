<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class CreateControlDetail extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('control_detail', function (Blueprint $table) {
            $table->id();
            $table->integer('control_id')->nullable();
            $table->string('s_name', 1024)->nullable();
            $table->string('s_sku', 1024)->nullable();
            $table->integer('cid')->nullable();
            $table->integer('sod_id');
            $table->integer('sdid')->nullable();
            $table->double('count');
            $table->double('price_now')->nullable();
            $table->text('comment')->nullable();
            $table->integer('status')->nullable()->default(1);

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
        Schema::dropIfExists('control_detail');
    }
}
