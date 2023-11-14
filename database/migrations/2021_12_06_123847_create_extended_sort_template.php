<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class CreateExtendedSortTemplate extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('extended_sort_template', function (Blueprint $table) {
            $table->id();
            $table->integer('uid');
            $table->string('name', 255);
            $table->integer('common')->default(0);
            $table->text('template');

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
        Schema::dropIfExists('extended_sort_template');
    }
}
