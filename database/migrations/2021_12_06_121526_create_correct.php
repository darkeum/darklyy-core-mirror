<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class CreateCorrect extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('correct', function (Blueprint $table) {
            $table->id();
            $table->integer('numb_counter')->nullable();
            $table->string('numb', 1024)->nullable();
            $table->integer('org');
            $table->integer('pid');
            $table->string('module', 1024);
            $table->integer('realization_id');
            $table->integer('uid');
            $table->date('date_create')->useCurrent();
            $table->date('date')->useCurrent();
            $table->integer('active')->default(0);
            
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
        Schema::dropIfExists('correct');
    }
}
