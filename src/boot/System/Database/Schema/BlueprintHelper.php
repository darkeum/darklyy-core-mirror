<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2026 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Boot\System\Database\Schema;

/**
 * Проектные хелперы построения таблиц DARKLYY.
 *
 * Вынесены из Boot\System\Database\Schema\Blueprint, чтобы отделить проектный
 * набор методов от базового (Laravel-подобного) API схемы.
 *
 * @mixin \Boot\System\Database\Schema\Blueprint
 */
trait BlueprintHelper
{
    /**
     * Автоинкрементный bigint-идентификатор с комментарием «Идентификатор».
     *
     * @param  string  $column
     * @return \Boot\System\Database\Schema\ColumnDefinition
     */
    public function id($column = 'id')
    {
        return $this->bigIncrements($column)->comment('Идентификатор');
    }

    /**
     * Беззнаковая колонка Big Integer (8-Byte) с идентификатором ответственного.
     *
     * @param  string  $column
     * @param  string  $comment
     * @return \Boot\System\Database\Schema\ColumnDefinition
     */
    public function uid($column = 'uid', $comment = 'Идентификатор ответственного')
    {
        return $this->unsignedBigInteger($column)->comment($comment);
    }

    /**
     * Беззнаковая колонка Big Integer (8-Byte) с идентификатором категории.
     *
     * @param  string  $column
     * @return \Boot\System\Database\Schema\ColumnDefinition
     */
    public function category($column = 'category')
    {
        return $this->unsignedBigInteger($column)->nullable()->comment('Идентификатор категории');
    }

    /**
     * Беззнаковая колонка Big Integer (8-Byte) с идентификатором организации.
     *
     * @param  string  $column
     * @return \Boot\System\Database\Schema\ColumnDefinition
     */
    public function org($column = 'org')
    {
        return $this->unsignedBigInteger($column)->default(1)->comment('Идентификатор организации');
    }

    /**
     * Беззнаковая колонка Big Integer (8-Byte) с идентификатором контрагента.
     *
     * @param  string  $column
     * @param  string  $comment
     * @return \Boot\System\Database\Schema\ColumnDefinition
     */
    public function oid($column = 'oid', $comment = 'Идентификатор контрагента')
    {
        return $this->unsignedBigInteger($column)->comment($comment);
    }

    /**
     * Колонка статуса со значением по умолчанию 1.
     *
     * @param  string  $column
     * @param  string  $comment
     * @return \Boot\System\Database\Schema\ColumnDefinition
     */
    public function status($column = 'status', $comment = 'Статус')
    {
        return $this->integer($column)->default(1)->comment($comment);
    }

    /**
     * Текстовая колонка комментария.
     *
     * @param  string  $column
     * @param  string  $comment
     * @return \Boot\System\Database\Schema\ColumnDefinition
     */
    public function comment_column($column = 'comment', $comment = 'Комментарий')
    {
        return $this->text($column)->comment($comment)->nullable();
    }

    /**
     * Пара колонок нумерации документа: numb_counter (bigint) и numb (varchar 255).
     *
     * @return void
     */
    public function numb()
    {
        $this->bigInteger('numb_counter')->comment('Счетчик нумерации');

        $this->string('numb', 255)->comment('Номер документа');
    }

    /**
     * Беззнаковая колонка Big Integer (8-Byte) со связью с таблицей организаций.
     *
     * @param  string  $column
     * @return \Boot\System\Database\Schema\ForeignDefaultColumnDefinition
     */
    public function foreignOrg($column)
    {
        return $this->addColumnDefinition(new ForeignDefaultColumnDefinition($this, [
            'type'          => 'bigInteger',
            'name'          => $column,
            'autoIncrement' => false,
            'unsigned'      => true,
            'comment'       => 'Идентификатор организации',
            'default'       => 1,
            'table'         => 'org',
        ]));
    }

    /**
     * Беззнаковая колонка Big Integer (8-Byte) со связью с таблицей пользователей.
     *
     * @param  string  $column
     * @return \Boot\System\Database\Schema\ForeignDefaultColumnDefinition
     */
    public function foreignUid($column)
    {
        return $this->addColumnDefinition(new ForeignDefaultColumnDefinition($this, [
            'type'          => 'bigInteger',
            'name'          => $column,
            'autoIncrement' => false,
            'unsigned'      => true,
            'comment'       => 'Идентификатор пользователя',
            'table'         => 'users',
        ]));
    }

    /**
     * Беззнаковая колонка Big Integer (8-Byte) со связью с таблицей контрагентов.
     *
     * @param  string  $column
     * @return \Boot\System\Database\Schema\ForeignDefaultColumnDefinition
     */
    public function foreignOid($column)
    {
        return $this->addColumnDefinition(new ForeignDefaultColumnDefinition($this, [
            'type'          => 'bigInteger',
            'name'          => $column,
            'autoIncrement' => false,
            'unsigned'      => true,
            'comment'       => 'контрагента',
            'table'         => 'organizations',
        ]));
    }

    /**
     * Беззнаковая колонка Big Integer (8-Byte) со связью с указанной таблицей.
     *
     * @param  string  $column
     * @param  string|null  $references_on
     * @return \Boot\System\Database\Schema\ForeignDefaultColumnDefinition
     */
    public function foreignCustom($column, $references_on = null)
    {
        return $this->addColumnDefinition(new ForeignDefaultColumnDefinition($this, [
            'type'          => 'bigInteger',
            'name'          => $column,
            'autoIncrement' => false,
            'unsigned'      => true,
            'table'         => $references_on,
        ]));
    }
}
