<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class CreatePromotions extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('promotions', function (Blueprint $table) {
            $table->id();
            $table->dateTime('deleted_at')->nullable()->comment('Маркер удаления');
            $table->integer('uid')->comment('Идентификатор пользователя');
            $table->string('title', 1024);
            $table->string('slug', 1024);
            $table->text('short')->nullable();
            $table->text('full')->nullable();
            $table->integer('type')->default(1);
            $table->integer('sales_id')->nullable();
            $table->string('fulltitle', 1024)->nullable();
            $table->text('description')->nullable();
            $table->string('keywords', 1024)->nullable();
            $table->string('preview', 1024);
            $table->string('preview_doc', 1024);
            $table->date('date_start');
            $table->date('date_end');
            $table->integer('views')->default(0);
            $table->text('tags')->nullable();
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
        Schema::dropIfExists('promotions');
    }
}
