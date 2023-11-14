<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class CreatePto extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pto', function (Blueprint $table) {
            $table->id();
            $table->string('title', 255)->comment('Название');
            $table->string('altname', 255);
            $table->double('dn')->comment('Диаметр');
            $table->double('sp')->comment('Площадь пластин');
            $table->double('st')->comment('Площадь теплообменника');
            $table->text('img_2d')->comment('Избражение в 2Д');
            $table->text('img_3d')->comment('Избражение в 3Д');
            $table->integer('man_id')->comment('Ссылка на производителя');
            $table->double('t_from')->comment('Рабочая температура от');
            $table->double('t_to')->comment('Рабочая температура до');
            $table->double('q_max')->comment('Расход, макс.');
            $table->double('p_max')->comment('Максимальное рабочее давление ');
            $table->text('gasket_type')->comment('Материал прокладок');
            $table->text('plates_type')->comment('Материал пластин  ');
            $table->text('coolant')->comment('Теплоноситель');
            $table->text('main_plates')->comment('Основной тип пластин  ');
            $table->text('other_plates')->comment('Промежуточный тип пластин');
            $table->double('warranty')->comment('Гарантия');
            $table->longText('ptoTags')->nullable();
            $table->date('date')->useCurrent()->comment('Дата создания');
            $table->integer('uid')->comment('Пользователь');
            $table->integer('status')->default(1)->comment('Статус');

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
        Schema::dropIfExists('pto');
    }
}
