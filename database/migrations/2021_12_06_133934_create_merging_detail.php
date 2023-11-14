<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class CreateMergingDetail extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('merging_detail', function (Blueprint $table) {
            $table->id();
            $table->integer('merging_id');
            $table->integer('cid');
            $table->integer('star')->default(0);
            $table->string('name', 1024);
            $table->integer('unit');
            $table->string('sku', 1024)->nullable();
            $table->double('count')->default(1);
            $table->decimal('price')->default(0.00);

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
        Schema::dropIfExists('merging_detail');
    }
}
