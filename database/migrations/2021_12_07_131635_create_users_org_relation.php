<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class CreateUsersOrgRelation extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users_org_relation', function (Blueprint $table) {
            $table->id();
            $table->org();
            $table->uid();
            $table->string('email', 256)->nullable()->comment('Почта');
            $table->string('phone', 255)->nullable()->comment('Телефон');
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
        Schema::dropIfExists('users_org_relation');
    }
}
