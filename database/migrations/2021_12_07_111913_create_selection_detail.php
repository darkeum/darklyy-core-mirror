<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class CreateSelectionDetail extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('selection_detail', function (Blueprint $table) {
            $table->id();
            $table->integer('selection_id')->nullable();
            $table->integer('cid')->nullable();
            $table->double('count')->nullable();
            $table->integer('position')->nullable();
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
        Schema::dropIfExists('selection_detail');
    }
}
