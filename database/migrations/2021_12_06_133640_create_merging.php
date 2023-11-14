<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class CreateMerging extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('merging', function (Blueprint $table) {
            $table->id();
            $table->integer('del')->default(0);
            $table->integer('numb_counter');
            $table->string('numb', 1024);
            $table->integer('uid')->unsigned();
            $table->integer('object')->nullable();
            $table->date('date');
            $table->integer('cid')->unsigned()->nullable();
            $table->string('name', 1024)->nullable();
            $table->string('sku', 1024)->nullable();
            $table->integer('unit')->nullable();
            $table->double('count')->nullable()->default(1);
            $table->integer('stock_id');
            $table->integer('org');
            $table->integer('type')->default(1)->comment('1 - комплектовка, 2 - раскомплектовка');
            $table->integer('active')->default(0)->comment('0 - нет, 1 - да');

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
        Schema::dropIfExists('merging');
    }
}
