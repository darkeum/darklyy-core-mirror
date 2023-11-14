<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class CreateStorageDetail extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('storage_detail', function (Blueprint $table) {
            $table->id();
            $table->integer('cid');
            $table->integer('position')->nullable();
            $table->integer('storage_id');
            $table->integer('sale_detail_id')->nullable();
            $table->integer('supply_detail_id')->nullable();
            $table->integer('sad_id')->nullable()->comment('storage_assembly_detail_id');
            $table->double('count')->nullable();
            $table->double('count_now')->nullable()->comment('Количество сколько сейчас');
            $table->double('count_stable')->nullable()->comment('Количество сколько было');
            $table->double('comment')->nullable();
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
        Schema::dropIfExists('storage_detail');
    }
}
