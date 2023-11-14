<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class CreateOrderJob extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_job', function (Blueprint $table) {
            $table->id();
            $table->integer('order_id');
            $table->integer('category');
            $table->string('position', 1024);
            $table->string('summary', 1024)->nullable();
            $table->string('summary_url', 1024)->nullable();
            $table->string('other_file', 1024)->nullable();
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
        Schema::dropIfExists('order_job');
    }
}
