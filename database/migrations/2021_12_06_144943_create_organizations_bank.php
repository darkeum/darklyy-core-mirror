<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class CreateOrganizationsBank extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('organizations_bank', function (Blueprint $table) {
            $table->id();
            $table->integer('oid');
            $table->string('name_short', 255);
            $table->string('name', 255);
            $table->string('address', 255);
            $table->string('bik', 255);
            $table->string('kschet', 255);
            $table->string('rschet', 255);
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
        Schema::dropIfExists('organizations_bank');
    }
}
