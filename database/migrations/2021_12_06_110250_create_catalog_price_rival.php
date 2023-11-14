<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class CreateCatalogPriceRival extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('catalog_price_rival', function (Blueprint $table) {
            $table->id();
            $table->integer('type');
            $table->decimal('our_supplier_discount')->nullable();
            $table->decimal('price_adjustments')->nullable();
            $table->decimal('price_adjustments_price')->nullable();
            $table->decimal('min_margin')->nullable();
            $table->decimal('optimal_margin')->nullable();
            $table->decimal('discount_installers_mrc')->nullable();
            $table->double('correction_factor_price')->nullable();
            $table->text('rival_link')->nullable()->comment('Ссылка поставщика');
            $table->text('competing_links')->nullable()->comment('конкурирующие ссылки');
            $table->integer('daemon')->default(0);
            $table->dateTime('disable')->nullable();
            $table->dateTime('iteration')->nullable();
            $table->integer('iteration_count')->default(0);
            
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
        Schema::dropIfExists('catalog_price_rival');
    }
}
