<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class CreateSupportMessage extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('support_message', function (Blueprint $table) {
            $table->id();
            $table->integer('support_id');
            $table->integer('uid');
            $table->text('message')->nullable();
            $table->integer('attach_id')->nullable();
            $table->dateTime('message_read')->nullable();

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
        Schema::dropIfExists('support_message');
    }
}
