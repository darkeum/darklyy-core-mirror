<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class CreateInventory extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inventory', function (Blueprint $table) {
            $table->id();
            $table->integer('wid')->nullable();
            $table->string('serial_number', 255)->nullable();
            $table->integer('category')->nullable();
            $table->text('name')->nullable();
            $table->text('cpec')->nullable();
            $table->integer('org')->nullable();
            $table->integer('location')->nullable();
            $table->integer('uid')->nullable();
            $table->dateTime('date')->nullable();
            $table->dateTime('last_date')->nullable();
            $table->string('preview', 255)->nullable();
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
        Schema::dropIfExists('inventory');
    }
}
