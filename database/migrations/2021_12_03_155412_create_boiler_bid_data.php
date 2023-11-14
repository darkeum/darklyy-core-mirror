<?php

use \Boot\System\Database\Migrations\Migration;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\Support\Facades\Schema;

class CreateBoilerBidData extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('boiler_bid_data', function (Blueprint $table) {
            $table->id();
            $table->integer('bid_type_id');
            $table->integer('category');
            $table->integer('uid')->nullable();
            $table->text('comment')->nullable();
            $table->text('client_comment')->nullable();
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
        Schema::dropIfExists('boiler_bid_data');
    }
}
