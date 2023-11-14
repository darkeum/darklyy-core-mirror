<?php

use \Boot\System\Database\Migrations\Migration;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\Support\Facades\Schema;

class CreateBoilerBidType extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('boiler_bid_type', function (Blueprint $table) {
            $table->id();
            $table->integer('bid_id');
            $table->integer('section');
            $table->integer('type');
            $table->text('allow_category')->default('[]');
            $table->text('comment')->nullable();
            $table->text('client_comment')->nullable();
            $table->integer('folder_id')->nullable()->comment('Папка');
            $table->integer('uid')->nullable();
            $table->integer('position')->nullable();
            $table->integer('active_color')->default(0);
            $table->string('color', 1024)->nullable();

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
        Schema::dropIfExists('boiler_bid_type');
    }
}
