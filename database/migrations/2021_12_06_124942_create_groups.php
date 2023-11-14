<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class CreateGroups extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('groups', function (Blueprint $table) {
            $table->id();
            $table->dateTime('deleted_at')->nullable()->comment('Маркер удаления');
            $table->string('name', 255)->nullable();
            $table->integer('guest')->nullable();
            $table->integer('user')->nullable();
            $table->integer('moderator')->nullable();
            $table->integer('admin')->nullable();
            $table->integer('banned')->nullable();
            $table->integer('showHide')->nullable();
            $table->integer('showAttach')->nullable();
            $table->integer('loadAttach')->nullable();
            $table->integer('addPost')->nullable();
            $table->integer('addComment')->nullable();
            $table->integer('allowRating')->nullable();
            $table->integer('maxWidth')->nullable();
            $table->integer('maxPms')->nullable();
            $table->text('control')->nullable();
            $table->string('icon', 255)->nullable();
            $table->string('color', 55)->nullable();
            $table->integer('points')->nullable();
            $table->integer('protect')->nullable();
            $table->integer('special')->nullable();

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
        Schema::dropIfExists('groups');
    }
}
