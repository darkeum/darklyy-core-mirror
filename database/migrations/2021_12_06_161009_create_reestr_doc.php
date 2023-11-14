<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class CreateReestrDoc extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reestr_doc', function (Blueprint $table) {
            $table->id();
            $table->integer('reestr_id');
            $table->integer('type');
            $table->dateTime('date_create')->useCurrent();
            $table->date('date')->useCurrent();
            $table->text('comment')->nullable();
            $table->integer('uid_took')->nullable()->comment('Кто забрал документ');
            $table->string('title', 1024)->nullable();
            $table->integer('doc')->nullable();
            $table->integer('scan')->nullable();
            $table->integer('spec_numb')->nullable();
            $table->integer('sign')->default(0);
            $table->integer('status')->default(0);

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
        Schema::dropIfExists('reestr_doc');
    }
}
