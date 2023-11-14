<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class KeySelectionRequest extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('selection_request', function (Blueprint $table) {
            $table->index('uid');
            $table->index('oid');
            $table->index('object');
        });
    }

    /**
     * Откат миграции
     *
     * @return void
     */
    public function down()
    {
        Schema::table('selection_request', function (Blueprint $table) {
            $table->dropIndex(['uid']);
            $table->dropIndex(['oid']);
            $table->dropIndex(['object']);
        });
    }
}
