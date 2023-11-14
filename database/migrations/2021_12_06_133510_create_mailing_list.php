<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class CreateMailingList extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mailing_list', function (Blueprint $table) {
            $table->id();
            $table->string('email', 255)->nullable();
            $table->integer('gender')->nullable()->default(0);
            $table->text('name')->nullable();
            $table->text('comment')->nullable();
            $table->integer('type')->nullable();
            $table->integer('send')->nullable()->default(0);

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
        Schema::dropIfExists('mailing_list');
    }
}
