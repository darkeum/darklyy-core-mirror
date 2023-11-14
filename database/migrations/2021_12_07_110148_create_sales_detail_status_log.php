<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class CreateSalesDetailStatusLog extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sales_detail_status_log', function (Blueprint $table) {
            $table->id();
            $table->integer('sales_detail_id');
            $table->integer('uid');
            $table->integer('status');
            $table->text('url');

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
        Schema::dropIfExists('sales_detail_status_log');
    }
}
