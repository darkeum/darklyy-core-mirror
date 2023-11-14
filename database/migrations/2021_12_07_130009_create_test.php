<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class CreateTest extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('test', function (Blueprint $table) {
            $table->id();
            $table->integer('test')->nullable();
            $table->string('name', 1024)->nullable();
            $table->integer('oid')->nullable();
            $table->dateTime('date')->useCurrent();
            $table->dateTime('deleted_at')->nullable()->comment('Маркер удаления');
            $table->integer('del')->default(0);
            $table->text('tt')->nullable();
            $table->integer('pop');
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
        Schema::dropIfExists('test');
    }
}
