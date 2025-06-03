<?php

namespace Boot\System\Database\Schema;

use Boot\Support\Str;

class ForeignDefaultColumnDefinition extends ColumnDefinition
{
    /**
     * The schema builder blueprint instance.
     *
     * @var \Boot\System\Database\Schema\Blueprint
     */
    protected $blueprint;

    protected string|null $references_on;


    /**
     * Create a new foreign ID column definition.
     *
     * @param  \Boot\System\Database\Schema\Blueprint  $blueprint
     * @param  array  $attributes
     * @return void
     */
    public function __construct(Blueprint $blueprint, $attributes = [], $references_on = null)
    {
        parent::__construct($attributes);

        $this->blueprint = $blueprint;

        $this->references_on = $references_on;
    }

    /**
     * Create a foreign key constraint on this column referencing the "id" column of the conventionally related table.
     *
     * @param  string|null  $table
     * @param  string  $column
     * @return \Illuminate\Support\Fluent|\Boot\System\Database\Schema\ForeignKeyDefinition
     */
    public function constrained($table = null, $column = 'id')
    {
        return $this->references($column)->on($table ?? $this->references_on ?? Str::plural(Str::beforeLast($this->name, '_' . $column)))->onUpdate('restrict')->onDelete('restrict');
    }

    /**
     * Specify which column this foreign ID references on another table.
     *
     * @param  string  $column
     * @return \Illuminate\Support\Fluent|\Boot\System\Database\Schema\ForeignKeyDefinition
     */
    public function references($column)
    {
        return $this->blueprint->foreign($this->name)->references($column);
    }
}
