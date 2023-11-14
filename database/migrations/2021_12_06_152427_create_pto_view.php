<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class CreatePtoView extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pto_view', function (Blueprint $table) {
            $table->id();
            $table->integer('pto_id');
            $table->integer('type')->comment('Тип ПТО');
            $table->integer('numb')->comment('№ рамы');
            $table->double('p')->comment('Давление');
            $table->double('l')->comment('Размер 1');
            $table->double('l1')->comment('Размер 2');
            $table->double('l2')->comment('Размер 3');
            $table->string('hairpin_type', 2048)->comment('Размер шпильки');
            $table->integer('hairpin_count')->comment('Кол-во шпилек ');
            $table->integer('plates_min')->comment('Мин Кол-во пластин');
            $table->integer('plates_max')->comment('Макс Кол-во пластин');
            $table->double('weight')->comment('Вес');
            $table->double('dn')->comment('Наружний дииаметр');
            $table->double('mesh_d')->comment('Димаетр сетки');
            $table->integer('bolt_count')->comment('Кол-во болтов');
            $table->string('bolt_type', 2048)->comment('Размер болта');
            $table->integer('cad_2d')->comment('Attach фала dwg 2д черетжа');
            $table->integer('cad_3d')->comment('Attach фала dwg 3д черетжа');
            $table->integer('cad_revit')->comment('Attach фала REVIT');
            $table->date('date')->useCurrent();
            $table->integer('uid');
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
        Schema::dropIfExists('pto_view');
    }
}
