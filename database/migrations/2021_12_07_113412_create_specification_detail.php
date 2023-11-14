<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class CreateSpecificationDetail extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('specification_detail', function (Blueprint $table) {
            $table->id();
            $table->integer('specification_id');
            $table->integer('cid');
            $table->text('title');
            $table->string('sku', 1024);
            $table->integer('unit');
            $table->double('count');
            $table->integer('position');
            $table->dateTime('deleted_at')->nullable()->comment('Маркер удаления');

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
        Schema::dropIfExists('specification_detail');
    }
}
