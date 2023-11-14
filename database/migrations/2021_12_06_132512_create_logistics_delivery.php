<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class CreateLogisticsDelivery extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('logistics_delivery', function (Blueprint $table) {
            $table->id();
            $table->dateTime('deleted_at')->nullable()->comment('Маркер удаления');
            $table->string('module', 255);
            $table->integer('pid');
            $table->integer('uid');
            $table->integer('driver')->nullable();
            $table->text('address');
            $table->string('coordinates', 2048)->nullable();
            $table->text('contact_person')->comment('контактное лицо');
            $table->string('phone', 255);
            $table->date('delivery_date');
            $table->time('delivery_from')->comment('доставка с');
            $table->time('delivery_to')->comment('доставка до');
            $table->text('comment')->nullable();
            $table->integer('delivery_type')->comment('За чей счёт доставка');
            $table->decimal('delivery_price')->default(0.00);
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
        Schema::dropIfExists('logistics_delivery');
    }
}
