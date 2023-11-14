<?php

use \Boot\System\Database\Migrations\Migration;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\Support\Facades\Schema;

class CreateActReconciliation extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('act_reconciliation', function (Blueprint $table) {
            $table->id();
            $table->org();
            $table->oid();
            $table->uid('uid_created', 'Идентификатор создателя');
            $table->uid();
            $table->numb();          
            $table->comment_column();
            $table->integer('fill_type')->comment('Необходимость заполнять данные контрагента');
            $table->integer('agreed')->comment('Подписание документа');
            $table->datetime('date')->comment('Дата акты сверки');
            $table->date('date_start')->comment('Начальная дата');
            $table->date('date_end')->comment('Конечная дата');
            $table->json('org_data')->comment('Данные организации');
            $table->json('org_director')->comment('Данные директора');
            $table->json('oid_data')->comment('Данные контрагента');
            $table->json('records')->comment('Записи в акте сверки');
            $table->decimal('initial_balance')->comment('Сальдо начальное');
            $table->json('initial_balance_contract')->nullable()->comment('Сальдо начальное по договорам'); 
            $table->decimal('final_balance')->comment('Сальдо конечное');
            $table->boolean('break_contracts')->comment('Разбиение по договорам');
            $table->boolean('display_full_document_titles')->comment('Отображать полные названия документов');
            $table->boolean('display_invoices')->comment('Отображать счета');
            $table->boolean('active')->comment('Проведение документа');
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
        Schema::dropIfExists('act_reconciliation');
    }
}
