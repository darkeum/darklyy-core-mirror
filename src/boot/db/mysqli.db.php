<?php

use Darkeum\Inspector\Facades\Inspector;

/**
 * @name        JMY CORE
 * @link        https://jmy.su/
 * @copyright   Copyright (C) 2012-2017 JMY LTD
 * @license     LICENSE.txt (see attached file)
 * @version     VERSION.txt (see attached file)
 * @author      Komarov Ivan
 */

class controlDB
{

    public $resource    = false;
    public $timeQuery   = 0;
    public $timeQueries = 0;
    public $numQueries  = 0;
    public $listQueries = [];

    public function connect($dbhost, $dbuser, $dbpass, $dbname, $dbport, $dbpersist = false)
    {
        if ($dbpersist) {
            $this->resource = mysqli_connect('p:' . $dbhost, $dbuser, $dbpass, false, $dbport);

        } else {
            $this->resource = mysqli_connect($dbhost, $dbuser, $dbpass, false, $dbport);

        }
        if (! mysqli_connect_errno()) {
            mysqli_set_charset($this->resource, 'utf8');
            if (! mysqli_select_db($this->resource, $dbname)) {
                if (file_exists('install.php')) {
                    if (! file_exists('install/lock.install')) {Header('Location: /install.php');}
                }

                mysqlFatalError('Ошибка в базе данных MySQL', 'База данных ' . $dbname . ' не найдена', '');

            }
        } else {
            if (file_exists('install.php')) {
                if (! file_exists('install/lock.install')) {Header('Location: /install.php');}
            }

            if (! file_exists(ROOT . '/tmp/lock.dberror')) {

                fopen(ROOT . '/tmp/lock.dberror', 'w');
                $ssh = ssh2_connect('localhost', 22);
                ssh2_auth_password($ssh, 'root', 'FmVNi5Nz69');
                $stream = ssh2_exec($ssh, 'docker restart $(docker ps -a -q)');
                stream_set_blocking($stream, true);
                $stream_out = ssh2_fetch_stream($stream, SSH2_STREAM_STDIO);
                echo stream_get_contents($stream_out);
                unlink(ROOT . '/tmp/lock.dberror');
            }
            mysqlFatalError('Ошибка в базе данных MySQL', 'Нет подключения к ' . $dbhost, '');
            exit();
        }
    }

    public function safesql($str)
    {
        return mysqli_real_escape_string($this->resource, $str);
    }

    public function doQuery($str, $ignoreError, $prefix)
    {
        $timer = microtime(1);

        if (class_exists(Inspector::class) && Inspector::canAddSegments() && $str) {
            $segment = Inspector::startSegment('db.' . 'old', $str);
        }

        if ($ignoreError) {
            $result = mysqli_query($this->resource, $str) or writeInLog('[Ошибка в базе данных] - запрос: ' . str_replace($prefix, '', $str), 'db_query');
        } else {
            $result = mysqli_query($this->resource, $str) or mysqlFatalError('Ошибка выполнения запроса в DB', "Не удалось выполнить запрос '" . str_replace($prefix, '', $str) . "' <br />Ответ с сервера: " . str_replace($prefix, '', mysqli_error($this->resource)), str_replace($prefix, '', $str));
        }

        $this->timeQuery   += microtime(1) - $timer;
        $this->timeQueries += $this->timeQuery;
        $this->numQueries++;

       if (class_exists(Inspector::class) && Inspector::canAddSegments() && $str) {
            $segment->addContext('db', [
                'connection' => 'old',
                'query'      => $str,
            ])->end();
        }

        if (DEBUG) {
            $this->listQueries[$this->numQueries] = [$str, $this->timeQuery];
        }

        return $result;
    }

    public function freeResult($resource)
    {
        if ($resource instanceof mysqli_result) {
            return mysqli_free_result($resource);
        } else {
            return false;
        }
    }

    public function getRow($resource)
    {
        if ($resource instanceof mysqli_result) {
            return @mysqli_fetch_assoc($resource);
        } else {
            return false;
        }
    }

    public function fetchRow($resource)
    {
        if ($resource instanceof mysqli_result) {
            return @mysqli_fetch_array($resource);
        } else {
            return false;
        }
    }

    public function fetch_all($resource)
    {
        if ($resource instanceof mysqli_result) {
            return @mysqli_fetch_all($resource);
        } else {
            return false;
        }
    }

    public function numRows($resource)
    {
        if ($resource instanceof mysqli_result) {
            return mysqli_num_rows($resource);
        } else {
            return false;
        }
    }

    public function info()
    {
        return mysqli_info($this->resource);
    }

    public function insert_id()
    {
        return mysqli_insert_id($this->resource);
    }

    public function ping()
    {
        return mysqli_ping($this->resource);
    }

    public function close()
    {
        return mysqli_close($this->resource);
    }
}
