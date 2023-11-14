<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class CreateSales extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->integer('del')->default(0)->comment('Удаление: 0 - есть, 1 - удалено');
            $table->integer('hidden_site')->default(0)->comment('Скрытие: 0 - есть, 1 - удалено');
            $table->integer('numb_counter')->nullable();
            $table->string('numb', 256)->nullable();
            $table->integer('clone')->nullable()->comment('clone sales. id sales');
            $table->integer('clone_active')->default(0)->comment('0 - нет, 1 - да');
            $table->integer('org')->nullable();
            $table->integer('stock_id')->nullable()->default(1);
            $table->integer('oid')->nullable();
            $table->integer('object')->nullable();
            $table->integer('uid')->nullable();
            $table->integer('uid_send')->nullable();
            $table->integer('uoif')->nullable()->comment('user_org_id_favorite');
            $table->text('confirm_supply_order')->default('[]')->comment('Подтверждение закупки');
            $table->string('title', 1024)->nullable()->comment('Наименование продажи');
            $table->text('client_title')->nullable()->comment('Заголовок для клиента');
            $table->date('date')->nullable()->useCurrent();
            $table->integer('date_add')->nullable()->useCurrent();
            $table->dateTime('date_end')->nullable();
            $table->text('comment')->nullable();
            $table->text('client_comment')->nullable();
            $table->text('comment_end')->nullable();
            $table->string('nds', 255)->nullable()->default(0);
            $table->integer('nds_includes')->nullable()->default(1);
            $table->string('total', 1024)->nullable()->default(0);
            $table->integer('pay')->nullable()->default(0);
            $table->decimal('pay_sum')->nullable();
            $table->integer('sos')->nullable();
            $table->integer('supply')->nullable();
            $table->integer('realization')->nullable();
            $table->integer('reestr')->nullable();
            $table->integer('allow_shipment')->nullable()->default(0);
            $table->integer('sales_type')->nullable()->default(1);
            $table->integer('sales_cat')->nullable()->default(0);
            $table->integer('status')->default(1);
            $table->integer('status_client')->default(1)->comment('Статус для сайта 1-создана 2-ожидает согласования 3-размещена 4-ожидает поставки 5-товар готов к отгрузке  6-реализовано');
            $table->integer('end')->default(0)->comment('Завершение продажи: 0 - в процессе, 1 - завершена успешна, 2 - завершена не успешно');
            $table->dateTime('end_date')->nullable();
            $table->string('md5', 128)->nullable();
            $table->integer('delivery_type')->default(0)->comment('Тип доставки: 0 - не выбрано, 1 - самовывоз, 2 - доставка');
            $table->text('address')->nullable()->comment('Адрес доставки');
            $table->integer('source_appeal')->nullable()->comment('источник обращения');
            $table->integer('task_id')->nullable();
            $table->text('task_title')->nullable();
            $table->dateTime('task_date_end')->nullable();
            $table->integer('task_availability')->default(0)->comment('Были ли задачи');
            $table->integer('market_segment')->nullable()->comment('Сегмент рынка');
            $table->integer('by_field_activity')->nullable()->comment('По сфере деятельности');

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
        Schema::dropIfExists('sales');
    }
}
