<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class CreateComments extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('comments', function (Blueprint $table) {
            $table->id();
            $table->smallInteger('uid');
            $table->smallInteger('post_id');
            $table->string('module', 55);
            $table->text('text')->nullable();
            $table->string('date', 44);
            $table->string('gemail', 55);
            $table->string('gname', 55);
            $table->string('gurl', 55);
            $table->integer('parent');
            $table->integer('status');

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
        Schema::dropIfExists('comments');
    }
}
