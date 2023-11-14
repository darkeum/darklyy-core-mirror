<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class CreateSupplyDetail extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('supply_detail', function (Blueprint $table) {
            $table->id();
            $table->integer('sid');
            $table->integer('sdid');
            $table->integer('cid');
            $table->string('s_name', 1024)->nullable();
            $table->string('s_sku', 1024)->nullable();
            $table->integer('price_type')->nullable();
            $table->string('s_price', 1024)->nullable();
            $table->text('s_price_all')->nullable();
            $table->decimal('sos_price')->nullable()->comment('Цена SOS');
            $table->date('sos_price_date')->nullable()->comment('Дата установки цены SOS');
            $table->integer('sos_bill_aid')->nullable()->comment('Счет поставщика для SOS');
            $table->integer('oid')->nullable()->comment('SOS. Поставщик');
            $table->string('s_unit', 1024)->nullable();
            $table->double('count');
            $table->double('count_send')->nullable()->default(0)->comment('Не использовать. Количество отправленных на закупку');
            $table->text('comment')->nullable();
            $table->date('date_post')->nullable()->comment('Дата SOS');
            $table->date('date_ship')->nullable()->comment('SOS. Дата отгрузки');
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
        Schema::dropIfExists('supply_detail');
    }
}
