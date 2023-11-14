<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class CreateSelectionGroup extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('selection_group', function (Blueprint $table) {
            $table->id();
            $table->string('code', 255)->nullable();
            $table->integer('aid_scheme')->nullable();
            $table->integer('uid')->nullable();
            $table->dateTime('date')->nullable()->useCurrent();
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
        Schema::dropIfExists('selection_group');
    }
}
