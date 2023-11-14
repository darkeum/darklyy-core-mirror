<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class CreateSpecification extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('specification', function (Blueprint $table) {
            $table->id();
            $table->string('name', 1024);
            $table->integer('uid');
            $table->integer('oid');
            $table->integer('object_id');
            $table->text('project_data')->default('[]');
            $table->string('shifr', 1024)->nullable();
            $table->dateTime('deleted_at')->nullable()->comment('Маркер удаления');


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
        Schema::dropIfExists('specification');
    }
}
