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

use Boot\System\Database\Connection;
use Boot\System\Database\Schema\Blueprint;
use Closure;
use Doctrine\DBAL\Types\Type;
use Illuminate\Container\Container;
use InvalidArgumentException;
use LogicException;

class Builder
{
    /**
     * The database connection instance.
     *
     * @var \Boot\System\Database\Connection
     */
    protected $connection;

    /**
     * The schema grammar instance.
     *
     * @var \Boot\System\Database\Schema\Grammars\Grammar
     */
    protected $grammar;

    /**
     * The Blueprint resolver callback.
     *
     * @var \Closure
     */
    protected $resolver;

    /**
     * The default string length for migrations.
     *
     * @var int
     */
    public static $defaultStringLength = 255;

    /**
     * Create a new database Schema manager.
     *
     * @param  \Boot\System\Database\Connection  $connection
     * @return void
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
        $this->grammar    = $connection->getSchemaGrammar();
    }

    /**
     * Set the default string length for migrations.
     *
     * @param  int  $length
     * @return void
     */
    public static function defaultStringLength($length)
    {
        static::$defaultStringLength = $length;
    }

    /**
     * Determine if the given table exists.
     *
     * @param  string  $table
     * @return bool
     */
    public function hasTable($table)
    {
        [$schema, $table] = $this->parseSchemaAndTable($table);

        $table = $this->connection->getTablePrefix() . $table;

        if ($sql = $this->grammar->compileTableExists($schema, $table)) {
            return (bool) $this->connection->scalar($sql);
        }

        foreach ($this->getTables($schema ?? $this->getCurrentSchemaName()) as $value) {
            if (strtolower($table) === strtolower($value['name'])) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get the tables that belong to the connection.
     *
     * @param  string|string[]|null  $schema
     * @return list<array{name: string, schema: string|null, schema_qualified_name: string, size: int|null, comment: string|null, collation: string|null, engine: string|null}>
     */
    public function getTables($schema = null)
    {
        return $this->connection->getPostProcessor()->processTables(
            $this->connection->selectFromWriteConnection($this->grammar->compileTables($schema))
        );
    }

    /**
     * Determine if the given table has a given column.
     *
     * @param  string  $table
     * @param  string  $column
     * @return bool
     */
    public function hasColumn($table, $column)
    {
        return in_array(
            strtolower($column), array_map('strtolower', $this->getColumnListing($table))
        );
    }

    /**
     * Determine if the given table has given columns.
     *
     * @param  string  $table
     * @param  array  $columns
     * @return bool
     */
    public function hasColumns($table, array $columns)
    {
        $tableColumns = array_map('strtolower', $this->getColumnListing($table));

        foreach ($columns as $column) {
            if (! in_array(strtolower($column), $tableColumns)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Get the data type for the given column name.
     *
     * @param  string  $table
     * @param  string  $column
     * @param  bool  $fullDefinition
     * @return string
     */
    public function getColumnType($table, $column, $fullDefinition = false)
    {
        $columns = $this->getColumns($table);

        foreach ($columns as $value) {
            if (strtolower($value['name']) === strtolower($column)) {
                return $fullDefinition ? $value['type'] : $value['type_name'];
            }
        }

        throw new InvalidArgumentException("There is no column with name '$column' on table '$table'.");
    }

    /**
     * Get the columns for a given table.
     *
     * @param  string  $table
     * @return list<array{name: string, type: string, type_name: string, nullable: bool, default: mixed, auto_increment: bool, comment: string|null, generation: array{type: string, expression: string|null}|null}>
     */
    public function getColumns($table)
    {
        [$schema, $table] = $this->parseSchemaAndTable($table);

        $table = $this->connection->getTablePrefix() . $table;
        return $this->connection->getPostProcessor()->processColumns(
            $this->connection->selectFromWriteConnection(
                $this->grammar->compileColumns($schema, $table)
            )
        );
    }

    /**
     * Get the foreign keys for a given table.
     *
     * @param  string  $table
     * @return array
     */
    public function getForeignKeys($table)
    {
        [$schema, $table] = $this->parseSchemaAndTable($table);

        $table = $this->connection->getTablePrefix() . $table;

        return $this->connection->getPostProcessor()->processForeignKeys(
            $this->connection->selectFromWriteConnection(
                $this->grammar->compileForeignKeys($schema, $table)
            )
        );
    }

    /**
     * Parse the given database object reference and extract the schema and table.
     *
     * @param  string  $reference
     * @param  string|bool|null  $withDefaultSchema
     * @return array{string|null, string}
     */
    public function parseSchemaAndTable($reference, $withDefaultSchema = null)
    {
        $segments = explode('.', $reference);

        if (count($segments) > 2) {
            throw new InvalidArgumentException(
                "Using three-part references is not supported, you may use `Schema::connection('{$segments[0]}')` instead."
            );
        }

        $table = $segments[1] ?? $segments[0];

        $schema = match (true) {
            isset($segments[1])           => $segments[0],
            is_string($withDefaultSchema) => $withDefaultSchema,
            $withDefaultSchema            => $this->getCurrentSchemaName(),
            default                       => null,
        };

        return [$schema, $table];
    }

    /**
     * Get the names of the current schemas for the connection.
     *
     * @return string[]|null
     */
    public function getCurrentSchemaListing()
    {
        return null;
    }

    /**
     * Get the default schema name for the connection.
     *
     * @return string|null
     */
    public function getCurrentSchemaName()
    {
        return $this->getCurrentSchemaListing()[0] ?? null;
    }

    /**
     * Get the column listing for a given table.
     *
     * @param  string  $table
     * @return array
     */
    public function getColumnListing($table)
    {
        $results = $this->connection->selectFromWriteConnection($this->grammar->compileColumnListing(
            $this->connection->getTablePrefix() . $table
        ));

        return $this->connection->getPostProcessor()->processColumnListing($results);
    }

    /**
     * Modify a table on the schema.
     *
     * @param  string  $table
     * @param  \Closure  $callback
     * @return void
     */
    public function table($table, Closure $callback)
    {
        $this->build($this->createBlueprint($table, $callback));
    }

    /**
     * Create a new table on the schema.
     *
     * @param  string  $table
     * @param  \Closure  $callback
     * @return void
     */
    public function create($table, Closure $callback)
    {
        $this->build(tap($this->createBlueprint($table), function ($blueprint) use ($callback) {
            $blueprint->create();

            $callback($blueprint);
        }));
    }

    /**
     * Drop a table from the schema.
     *
     * @param  string  $table
     * @return void
     */
    public function drop($table)
    {
        $this->build(tap($this->createBlueprint($table), function ($blueprint) {
            $blueprint->drop();
        }));
    }

    /**
     * Drop a table from the schema if it exists.
     *
     * @param  string  $table
     * @return void
     */
    public function dropIfExists($table)
    {
        $this->build(tap($this->createBlueprint($table), function ($blueprint) {
            $blueprint->dropIfExists();
        }));
    }

    /**
     * Drop all tables from the database.
     *
     * @return void
     *
     * @throws \LogicException
     */
    public function dropAllTables()
    {
        throw new LogicException('This database driver does not support dropping all tables.');
    }

    /**
     * Drop all views from the database.
     *
     * @return void
     *
     * @throws \LogicException
     */
    public function dropAllViews()
    {
        throw new LogicException('This database driver does not support dropping all views.');
    }

    /**
     * Drop all types from the database.
     *
     * @return void
     *
     * @throws \LogicException
     */
    public function dropAllTypes()
    {
        throw new LogicException('This database driver does not support dropping all types.');
    }

    /**
     * Get all of the table names for the database.
     *
     * @return void
     *
     * @throws \LogicException
     */
    public function getAllTables()
    {
        throw new LogicException('This database driver does not support getting all tables.');
    }

    /**
     * Rename a table on the schema.
     *
     * @param  string  $from
     * @param  string  $to
     * @return void
     */
    public function rename($from, $to)
    {
        $this->build(tap($this->createBlueprint($from), function ($blueprint) use ($to) {
            $blueprint->rename($to);
        }));
    }

    /**
     * Enable foreign key constraints.
     *
     * @return bool
     */
    public function enableForeignKeyConstraints()
    {
        return $this->connection->statement(
            $this->grammar->compileEnableForeignKeyConstraints()
        );
    }

    /**
     * Disable foreign key constraints.
     *
     * @return bool
     */
    public function disableForeignKeyConstraints()
    {
        return $this->connection->statement(
            $this->grammar->compileDisableForeignKeyConstraints()
        );
    }

    /**
     * Execute the blueprint to build / modify the table.
     *
     * @param  \Boot\System\Database\Schema\Blueprint  $blueprint
     * @return void
     */
    protected function build(Blueprint $blueprint)
    {
        $blueprint->build($this->connection, $this->grammar);
    }

    /**
     * Create a new command set with a Closure.
     *
     * @param  string  $table
     * @param  \Closure|null  $callback
     * @return \Boot\System\Database\Schema\Blueprint
     */
    protected function createBlueprint($table, ?Closure $callback = null)
    {
        $connection = $this->connection;

        if (isset($this->resolver)) {
            return call_user_func($this->resolver, $connection, $table, $callback);
        }

        return Container::getInstance()->make(Blueprint::class, compact('connection', 'table', 'callback'));
    }

    /**
     * Get the database connection instance.
     *
     * @return \Boot\System\Database\Connection
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * Set the database connection instance.
     *
     * @param  \Boot\System\Database\Connection  $connection
     * @return $this
     */
    public function setConnection(Connection $connection)
    {
        $this->connection = $connection;

        return $this;
    }

    /**
     * Set the Schema Blueprint resolver callback.
     *
     * @param  \Closure  $resolver
     * @return void
     */
    public function blueprintResolver(Closure $resolver)
    {
        $this->resolver = $resolver;
    }

    /**
     * Get the indexes for a given table.
     *
     * @param  string  $table
     * @return list<array{name: string, columns: list<string>, type: string, unique: bool, primary: bool}>
     */
    public function getIndexes($table)
    {
        [$schema, $table] = $this->parseSchemaAndTable($table);

        $table = $this->connection->getTablePrefix() . $table;

        return $this->connection->getPostProcessor()->processIndexes(
            $this->connection->selectFromWriteConnection(
                $this->grammar->compileIndexes($schema, $table)
            )
        );
    }

    /**
     * Get the names of the indexes for a given table.
     *
     * @param  string  $table
     * @return list<string>
     */
    public function getIndexListing($table)
    {
        return array_column($this->getIndexes($table), 'name');
    }

    /**
     * Determine if the given table has a given index.
     *
     * @param  string  $table
     * @param  string|array  $index
     * @param  string|null  $type
     * @return bool
     */
    public function hasIndex($table, $index, $type = null)
    {
        $type = is_null($type) ? $type : strtolower($type);

        foreach ($this->getIndexes($table) as $value) {
            $typeMatches = is_null($type)
                || ($type === 'primary' && $value['primary'])
                || ($type === 'unique' && $value['unique'])
                || $type === $value['type'];

            if (($value['name'] === $index || $value['columns'] === $index) && $typeMatches) {
                return true;
            }
        }

        return false;
    }
}
