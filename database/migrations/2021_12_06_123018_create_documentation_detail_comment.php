<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class CreateDocumentationDetailComment extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('documentation_detail_comment', function (Blueprint $table) {
            $table->id();
            $table->integer('ddid')->comment('documentation detail id');
            $table->integer('uid');
            $table->text('comment')->comment('Комментарий');
            $table->dateTime('date');

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
        Schema::dropIfExists('documentation_detail_comment');
    }
}
