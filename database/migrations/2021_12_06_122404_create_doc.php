<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class CreateDoc extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('doc', function (Blueprint $table) {
            $table->id();
            $table->integer('test_type')->nullable();
            $table->integer('capital_construction')->nullable();
            $table->string('project', 1024)->nullable();
            $table->text('responsible_persons')->nullable();
            $table->text('test_data')->nullable();
            $table->integer('uid')->nullable();
            $table->dateTime('date')->useCurrent();
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
        Schema::dropIfExists('doc');
    }
}
