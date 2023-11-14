<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class CreateCategories extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255)->nullable();
            $table->string('altname', 255)->nullable();
            $table->string('fulltitle', 255)->nullable();
            $table->text('description')->nullable();
            $table->text('full')->nullable();
            $table->string('keywords', 255)->nullable();
            $table->string('module', 55);
            $table->string('icon', 255)->nullable();
            $table->string('preview', 1024)->nullable();
            $table->string('preview_og', 1024)->nullable();
            $table->smallInteger('position')->nullable();
            $table->text('position_filter')->nullable()->comment('JSON позиция элементов');
            $table->smallInteger('parent_id')->nullable()->default(0);
            $table->integer('sum')->nullable();
            $table->integer('hidden')->nullable()->default(0);
            $table->integer('status')->nullable()->default(1);

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
        Schema::dropIfExists('categories');
    }
}
