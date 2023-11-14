<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class CreateSupply extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('supply', function (Blueprint $table) {
            $table->id();
            $table->integer('org')->nullable();
            $table->integer('uid');
            $table->integer('sales_id');
            $table->dateTime('date')->useCurrent();
            $table->dateTime('date_edit')->nullable()->comment('Дата изменения');
            $table->dateTime('date_end')->nullable()->comment('Дата завершения заявки');
            $table->text('comment')->nullable();
            $table->integer('type')->default(1);
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
        Schema::dropIfExists('supply');
    }
}
