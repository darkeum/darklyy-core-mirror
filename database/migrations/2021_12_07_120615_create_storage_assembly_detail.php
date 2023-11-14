<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class CreateStorageAssemblyDetail extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('storage_assembly_detail', function (Blueprint $table) {
            $table->id();
            $table->integer('said')->comment('storage_assembly');
            $table->integer('sdid')->comment('sales_detail_id');
            $table->double('count')->default(0);
            $table->double('count_save')->default(0);
            $table->integer('count_now')->nullable()->comment('Реальное количество');
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
        Schema::dropIfExists('storage_assembly_detail');
    }
}
