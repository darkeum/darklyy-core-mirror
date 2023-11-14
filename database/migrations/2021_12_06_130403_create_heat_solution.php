<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class CreateHeatSolution extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('heat_solution', function (Blueprint $table) {
            $table->id();
            $table->dateTime('deleted_at')->nullable()->comment('Маркер удаления');
            $table->integer('heat_id');
            $table->integer('solution_id');
            $table->text('solution');
            $table->decimal('price');
            $table->double('deliveryWeeks');
            $table->string('effectiveSurface', 128);
            $table->string('weight', 256);
            $table->string('height', 256);
            $table->string('width', 256);
            $table->string('length', 256);
            $table->longText('detailed');
            $table->longText('response')->nullable();
            $table->longText('result')->nullable();
            $table->string('aid', 256)->nullable();
            $table->longText('cid')->nullable();
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
        Schema::dropIfExists('heat_solution');
    }
}
