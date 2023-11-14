<?php

use \Boot\System\Database\Migrations\Migration;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\Support\Facades\Schema;

class CreateBoilerBid extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('boiler_bid', function (Blueprint $table) {
            $table->id();
            $table->integer('archive')->default(0)->comment('0 - нет, 1 - да');
            $table->integer('numb_counter');
            $table->string('numb', 255);
            $table->string('title', 255)->nullable();
            $table->text('comment')->nullable();
            $table->string('client_title', 1024)->nullable();
            $table->text('client_comment')->nullable();
            $table->dateTime('date_create');
            $table->dateTime('date_end')->nullable();
            $table->integer('uid')->nullable();
            $table->integer('object')->nullable();
            $table->integer('org')->default(1);
            $table->integer('oid')->nullable();
            $table->integer('nds_includes')->default(1);
            $table->text('sales_link');
            $table->string('progress', 255);
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
        Schema::dropIfExists('boiler_bid');
    }
}
