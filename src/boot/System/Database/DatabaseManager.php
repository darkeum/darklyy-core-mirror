<?php

namespace Boot\System\Database;

use Doctrine\DBAL\Types\Type;
use Illuminate\Database\Connectors\ConnectionFactory;
use Illuminate\Support\Arr;
use Illuminate\Support\ConfigurationUrlParser;
use Illuminate\Support\Str;
use InvalidArgumentException;
use PDO;
use RuntimeException;
use Illuminate\Database\DatabaseManager as LaravelDatabaseManager;

/**
 * @mixin \Illuminate\Database\Connection
 */
class DatabaseManager extends LaravelDatabaseManager
{


    protected function configuration($name)
    {
        $name = $name ?: $this->getDefaultConnection();

        // To get the database connection configuration, we will just pull each of the
        // connection configurations and get the configurations for the given name.
        // If the configuration doesn't exist, we'll throw an exception and bail.
        $connections = $this->app['config']->get('database.connections');

        if (is_null($config = Arr::get($connections, $name))) {
            throw new InvalidArgumentException("Database connection [{$name}] not configured.");
        }
        return (new ConfigurationUrlParser)
            ->parseConfiguration($config);
    }

    /**
     * Get the default connection name.
     *
     * @return string
     */
    public function getDefaultConnection()
    {
        return $this->app['config']->get('database.default');
    }

    /**
     * Register custom Doctrine types with the connection.
     *
     * @param  \Illuminate\Database\Connection  $connection
     * @return void
     */
    protected function registerConfiguredDoctrineTypes(\Illuminate\Database\Connection $connection): void
    {
        // foreach ($this->app['config']->get('database.dbal.types', []) as $name => $class) {
        //     $this->registerDoctrineType($class, $name, $name);
        // }

        foreach ($this->doctrineTypes as $name => [$type, $class]) {
            $connection->registerDoctrineType($class, $name, $type);
        }
    }
}
