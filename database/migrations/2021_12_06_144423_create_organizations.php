<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class CreateOrganizations extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('organizations', function (Blueprint $table) {
            $table->id();
            $table->string('type', 255)->nullable();
            $table->string('name', 1024)->nullable();
            $table->string('name_full', 1024)->nullable();
            $table->string('yaddress', 1024)->nullable();
            $table->string('maddress', 1024)->nullable();
            $table->longText('address_source')->nullable();
            $table->string('inn', 24);
            $table->string('kpp', 24)->nullable();
            $table->string('ogrn', 24)->nullable();
            $table->string('post', 1024)->nullable();
            $table->string('director', 96)->nullable();
            $table->text('director_detail')->nullable();
            $table->string('dey', 1024)->nullable();
            $table->string('phone', 1024)->nullable();
            $table->string('email', 32)->nullable();
            $table->integer('form')->nullable();
            $table->integer('reestr_id')->nullable()->comment('Документы по умолчанию');
            $table->string('description', 1024)->nullable();
            $table->integer('client_type')->default(1);
            $table->integer('default_responsible')->nullable();
            $table->integer('status')->nullable();

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
        Schema::dropIfExists('organizations');
    }
}
