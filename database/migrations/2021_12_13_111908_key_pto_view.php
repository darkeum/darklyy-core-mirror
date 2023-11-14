<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class KeyPtoView extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pto_view', function (Blueprint $table) {
            $table->foreign('pto_id')->references('id')->on('pto')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('uid')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('cad_2d')->references('id')->on('attach')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('cad_3d')->references('id')->on('attach')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('cad_revit')->references('id')->on('attach')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Откат миграции
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pto_view', function (Blueprint $table) {
            $table->dropForeign(['pto_id']);
            $table->dropForeign(['uid']);
            $table->dropForeign(['cad_2d']);
            $table->dropForeign(['cad_3d']);
            $table->dropForeign(['cad_revit']);
        });
    }
}
