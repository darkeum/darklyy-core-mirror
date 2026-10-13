<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2026 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Boot\System\Database\Connectors;

use Boot\System\Database\Connection;
use Boot\System\Database\MariaDbConnection;
use Boot\System\Database\MySqlConnection;
use Boot\System\Database\PostgresConnection;
use Boot\System\Database\SQLiteConnection;
use Boot\System\Database\SqlServerConnection;
use InvalidArgumentException;

class ConnectionFactory extends \Illuminate\Database\Connectors\ConnectionFactory
{

    /**
     * Establish a PDO connection based on the configuration.
     *
     * @param  array  $config
     * @param  string|null  $name
     * @return \Boot\System\Database\Connection
     */
    public function make(array $config, $name = null)
    {
        $config = $this->parseConfig($config, $name);

        if (isset($config['read'])) {
            return $this->createReadWriteConnection($config);
        }

        return $this->createSingleConnection($config);
    }

    /**
     * Create a single database connection instance.
     *
     * @param  array  $config
     * @return \Boot\System\Database\Connection
     */
    protected function createSingleConnection(array $config)
    {
        $pdo = $this->createPdoResolver($config);

        return $this->createConnection(
            $config['driver'], $pdo, $config['database'], $config['prefix'], $config
        );
    }

    /**
     * Create a read / write database connection instance.
     *
     * @param  array  $config
     * @return \Boot\System\Database\Connection
     */
    protected function createReadWriteConnection(array $config)
    {
        $connection = $this->createSingleConnection($this->getWriteConfig($config));

        return $connection->setReadPdo($this->createReadPdo($config));
    }

    /**
     * Create a connector instance based on the configuration.
     *
     * @param  array  $config
     * @return \Boot\System\Database\Connectors\ConnectorInterface
     *
     * @throws \InvalidArgumentException
     */
    public function createConnector(array $config)
    {
        if (! isset($config['driver'])) {
            throw new InvalidArgumentException('A driver must be specified.');
        }

        if ($this->container->bound($key = "db.connector.{$config['driver']}")) {
            return $this->container->make($key);
        }

        return match ($config['driver']) {
            'mysql'   => new MySqlConnector,
            'mariadb' => new MariaDbConnector,
            'pgsql'   => new PostgresConnector,
            'sqlite'  => new SQLiteConnector,
            'sqlsrv'  => new SqlServerConnector,
            default   => throw new InvalidArgumentException("Unsupported driver [{$config['driver']}]."),
        };
    }

    /**
     * Create a new connection instance.
     *
     * @param  string  $driver
     * @param  \PDO|\Closure  $connection
     * @param  string  $database
     * @param  string  $prefix
     * @param  array  $config
     * @return \Boot\System\Database\Connection
     *
     * @throws \InvalidArgumentException
     */
    protected function createConnection($driver, $connection, $database, $prefix = '', array $config = [])
    {
        if ($resolver = Connection::getResolver($driver)) {
            return $resolver($connection, $database, $prefix, $config);
        }

        return match ($driver) {
            'mysql'   => new MySqlConnection($connection, $database, $prefix, $config),
            'mariadb' => new MariaDbConnection($connection, $database, $prefix, $config),
            'pgsql'   => new PostgresConnection($connection, $database, $prefix, $config),
            'sqlite'  => new SQLiteConnection($connection, $database, $prefix, $config),
            'sqlsrv'  => new SqlServerConnection($connection, $database, $prefix, $config),
            default   => throw new InvalidArgumentException("Unsupported driver [{$driver}]."),
        };
    }
}
