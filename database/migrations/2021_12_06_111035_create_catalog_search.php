<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class CreateCatalogSearch extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('catalog_search', function (Blueprint $table) {
            $table->id();
            $table->dateTime('deleted_at')->nullable()->comment('Маркер удаления')->comment('Маркер удаления');
            $table->string('title', 1024)->nullable()->comment('Наименование');
            $table->string('marka', 1024)->nullable()->comment('Марка');
            $table->string('sku', 1024)->nullable()->comment('Артикул');
            $table->string('man', 1024)->nullable()->comment('Производитель');
            $table->integer('cid')->comment('Дата создания каталога');
            $table->integer('mark')->default(0)->comment('Оценка поиска');
            

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
        Schema::dropIfExists('catalog_search');
    }
}
