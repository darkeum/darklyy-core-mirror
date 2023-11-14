<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class CreateStorageAssembly extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('storage_assembly', function (Blueprint $table) {
            $table->id();
            $table->integer('del')->default(0)->comment('Удаление: 0 - есть, 1 - удалено');
            $table->integer('sales_id');
            $table->integer('uid');
            $table->integer('uid_end')->nullable();
            $table->dateTime('date')->useCurrent();
            $table->integer('type')->default(1)->comment('Тип заявки: 1 - сборка товара; 2 - отмена сборки');
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
        Schema::dropIfExists('storage_assembly');
    }
}
