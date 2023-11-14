<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class CreateVerDetail extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ver_detail', function (Blueprint $table) {
            $table->id();
            $table->integer('vid')->nullable();
            $table->integer('vdid')->nullable();
            $table->dateTime('deleted_at')->nullable()->comment('Маркер удаления');
            $table->integer('uid')->nullable();
            $table->integer('cid')->nullable();
            $table->string('sku', 1024)->nullable();
            $table->string('name', 1024)->nullable();
            $table->integer('unit')->nullable();
            $table->decimal('price')->nullable();
            $table->integer('price_type')->nullable();
            $table->integer('count')->nullable();
            $table->integer('discount')->nullable();
            $table->integer('nds_type')->nullable();
            $table->integer('position')->nullable();
            $table->integer('sum')->nullable()->default(0);
            $table->string('stable_name', 1024)->nullable();
            $table->string('stable_price', 1024)->nullable();
            $table->string('model', 1024)->nullable();
            $table->string('number', 1024)->nullable();
            $table->integer('dn')->nullable();
            $table->text('comment')->nullable();
            $table->integer('status')->nullable();

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
        Schema::dropIfExists('ver_detail');
    }
}
