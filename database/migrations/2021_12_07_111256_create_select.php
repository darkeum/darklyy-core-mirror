<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class CreateSelect extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('select', function (Blueprint $table) {
            $table->id();
            $table->integer('uid')->nullable();
            $table->integer('oid')->nullable();
            $table->integer('object')->nullable();
            $table->longText('detail')->nullable();
            $table->text('comment')->nullable();
            $table->integer('date')->nullable();
            $table->integer('r_date')->nullable();
            $table->integer('c_date')->nullable();
            $table->integer('ruid')->nullable();
            $table->integer('resp')->nullable();
            $table->integer('status');
            $table->integer('type')->nullable();
            $table->string('code', 255)->nullable();

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
        Schema::dropIfExists('select');
    }
}
