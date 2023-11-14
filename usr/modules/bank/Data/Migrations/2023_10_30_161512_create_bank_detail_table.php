<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2023 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Golovin Egor
*/

use Boot\Support\Facades\Schema;
use Boot\System\Database\Schema\Blueprint;
use Boot\System\Database\Migrations\Migration;

class CreateBankDetailTable extends Migration
{
    /**
     * Запуск миграции
     */
    public function up(): void
    {
        Schema::create('bank_detail', function (Blueprint $table) {
            $table->id();
            $table->string('hash', 255)->nullable()->comment('Hash'); //есть ли отдельный тип столбцов hash? (в таблице вроде не написано)
            $table->numb();
            $table->org()->default(1); // по умолчанию стоит значение 1 для org - нужно это указывать или это прописано уже в этом типе столбца
            $table->bigInteger('organizations_bank_id', 20)->default(346)->comment('Идентификатор банка организации');
            $table->string('type', 1024)->nullable()->comment('Тип');
            $table->integer('pay_in', 11)->nullable()->comment('Поступление или списание');
            $table->date('date')->nullable()->comment('Дата');
            $table->date('date_create')->nullable()->comment('Дата создания');
            $table->double('sum')->nullable()->comment('Сумма');
                //здесь много id всяких - для них нужно отдельный тип вроде bigIncrements или оставить как есть?
            $table->bigInteger('payer', 20)->nullable()->comment('Идентификатор плательщика');
            $table->bigInteger('payer_bank', 20)->nullable()->comment('Идентификатор банка плательщика');
            $table->bigInteger('recipient', 20)->nullable()->comment('Идентификатор получателя');
            $table->bigInteger('recipient_bank', 20)->nullable()->comment('Идентификатор банка получателя');
                //для комментария есть отдельный тип $table->comment_column - надо заменить или оставить как есть?
            $table->string('comment', 2048)->nullable()->comment('Комментарий');
            $table->bigInteger('upload_id', 20)->nullable()->comment('Идентификатор загрузки');
            $table->string('module', 255)->nullable()->comment('Связь. Имя модуля');
            $table->status('status')->comment('Статус');
            $table->timestamps();
            //->comment('')
        });
    }

    /**
     * Отмена миграции.
     */
    public function down(): void
    {
        Schema::dropIfExists('bank_detail');
    }
}
