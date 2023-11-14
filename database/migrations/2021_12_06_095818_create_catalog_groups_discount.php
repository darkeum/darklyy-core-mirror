<?php

use \Boot\System\Database\Migrations\Migration;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\Support\Facades\Schema;

class CreateCatalogGroupsDiscount extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('catalog_groups_discount', function (Blueprint $table) {
            $table->id();
            $table->integer('catalog_groups_id');
            $table->integer('catalog_groups_discount_name_id');
            $table->double('discount');

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
        Schema::dropIfExists('catalog_groups_discount');
    }
}
