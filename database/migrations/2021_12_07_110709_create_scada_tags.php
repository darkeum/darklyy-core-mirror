<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class CreateScadaTags extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('scada_tags', function (Blueprint $table) {
            $table->id();
            $table->dateTime('deleted_at')->nullable()->comment('Маркер удаления');
            $table->integer('scada_id')->nullable();
            $table->string('title', 1024)->nullable();
            $table->integer('unit_id')->nullable();
            $table->string('actions', 255)->default('read');
            $table->string('registr', 1024)->nullable();
            $table->integer('address')->nullable();
            $table->integer('quantity')->nullable();
            $table->string('type', 255)->nullable();
            $table->string('field_type', 1024)->nullable();
            $table->string('field_id', 1024)->nullable();
            $table->integer('status')->nullable();

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
        Schema::dropIfExists('scada_tags');
    }
}
