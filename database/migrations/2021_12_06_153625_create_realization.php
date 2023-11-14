<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class CreateRealization extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('realization', function (Blueprint $table) {
            $table->id();
            $table->integer('numb_counter');
            $table->string('numb', 11)->nullable();
            $table->string('id_1c', 256)->nullable();
            $table->string('ytid', 256)->nullable();
            $table->integer('sales_id')->nullable();
            $table->integer('stock_id')->default(1);
            $table->integer('object')->nullable();
            $table->integer('uid')->nullable();
            $table->integer('uid_create')->nullable();
            $table->integer('oid')->nullable();
            $table->integer('org')->nullable();
            $table->date('date')->useCurrent();
            $table->dateTime('date_active')->nullable();
            $table->text('comment')->nullable();
            $table->decimal('total')->nullable();
            $table->decimal('nds')->nullable();
            $table->float('nds_includes')->nullable();
            $table->integer('pay')->nullable()->default(0);
            $table->text('print_info')->nullable()->comment('Информация для печати');
            $table->text('relation')->nullable();
            $table->integer('scan')->default(0);
            $table->integer('active')->nullable()->default(0);
            $table->integer('status')->nullable()->default(1);
            

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
        Schema::dropIfExists('realization');
    }
}
