<?php

use \Boot\System\Database\Migrations\Migration;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\Support\Facades\Schema;

class CreateCatalog extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('catalog', function (Blueprint $table) {
            $table->id();
            $table->string('sku', 255)->nullable();
            $table->string('sku2', 255)->nullable();
            $table->string('sku3', 255)->nullable();
            $table->string('sku_UdmurtSnab', 255)->nullable()->comment('Артикул УдмуртСнаб');
            $table->integer('unit')->default(796);
            $table->string('title', 1024);
            $table->string('fulltitle', 1024)->nullable();
            $table->text('short')->nullable();
            $table->text('full')->nullable();
            $table->string('tags', 255)->nullable();
            $table->text('price_data')->nullable();
            $table->string('category', 256)->nullable()->comment('Основная');
            $table->string('section', 255)->nullable();
            $table->text('attach_id')->default('[]')->comment('JSON. связь с attach');
            $table->string('altname', 255)->nullable();
            $table->text('preview')->nullable();
            $table->text('keywords')->nullable();
            $table->text('description')->nullable();
            $table->integer('man')->nullable();
            $table->integer('allow_rating')->nullable();
            $table->integer('allow_index')->nullable();
            $table->float('score')->nullable()->default(0);
            $table->smallInteger('votes')->nullable()->default(0);
            $table->smallInteger('views')->nullable()->default(0);
            $table->integer('clicks')->default(0);
            $table->longText('attribute')->nullable()->default('[]')->comment('utf8mb4_bin');
            $table->longText('publish_urls')->nullable();
            $table->integer('fixed')->default(0)->nullable();
            $table->integer('types')->default(3)->nullable();
            $table->longText('spec')->nullable();
            $table->longText('weight')->nullable();
            $table->integer('date_delivery')->comment('Дата поставки в днях')->nullable();
            $table->string('qmin', 255)->nullable();
            $table->string('qnorm', 255)->nullable();
            $table->integer('hidden')->default(0)->comment('0 - показать, 1 - скрыть');
            $table->integer('check_update')->nullable()->default(0)->comment('проверка прохождения');
            $table->integer('active')->nullable()->default(0)->comment('Активная номенклатура для портала');
            $table->integer('active_guest')->default(0)->comment('Активная номенклатура для гостей');
            $table->integer('active_user')->default(0)->comment('Активно только для пользователей');
            $table->integer('catalog_groups_id')->nullable();

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
        Schema::dropIfExists('catalog');
    }
}
