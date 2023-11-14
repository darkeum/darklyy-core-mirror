<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class CreateCorrections extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('corrections', function (Blueprint $table) {
            $table->id();
            $table->integer('del')->default(0);
            $table->integer('uid');
            $table->integer('uid_exec')->nullable();
            $table->integer('type')->default(1);
            $table->string('modules', 1024)->nullable();
            $table->string('url', 1024)->nullable();
            $table->integer('pull')->nullable();
            $table->dateTime('date_plan')->nullable();
            $table->date('date_add');
            $table->dateTime('date_end')->nullable();
            $table->integer('mark')->default(1);
            $table->text('compl_text')->nullable();
            $table->integer('status');

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
        Schema::dropIfExists('corrections');
    }
}
