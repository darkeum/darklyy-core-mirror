<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class CreateStorage extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('storage', function (Blueprint $table) {
            $table->id();
            $table->dateTime('deleted_at')->nullable()->comment('Маркер удаления');
            $table->integer('uid');
            $table->integer('oid')->nullable();
            $table->integer('object')->nullable();
            $table->integer('stock_id');
            $table->integer('type')->default(1);
            $table->integer('sales_id')->nullable();
            $table->integer('supply_id')->nullable();
            $table->integer('sa_id')->nullable()->comment('storage_assembly_id');
            $table->dateTime('date')->useCurrent();
            $table->dateTime('date_end')->nullable()->comment('Дата завершения заявки');
            $table->text('comment')->nullable();
            $table->integer('send_notif_uid')->nullable()->comment('uid отправки на склад');
            $table->integer('status');

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
        Schema::dropIfExists('storage');
    }
}
