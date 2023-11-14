<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class CreateUsersOrg extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users_org', function (Blueprint $table) {
            $table->id();
            $table->integer('uid');
            $table->integer('oid');
            $table->string('post', 1024)->nullable();
            $table->text('comment')->nullable();
            $table->text('writ')->nullable();
            $table->integer('type')->nullable()->default(-1)->comment('function getTypeOrg');
            $table->integer('owner')->default(0);
            $table->integer('choose')->default(0);
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
        Schema::dropIfExists('users_org');
    }
}
