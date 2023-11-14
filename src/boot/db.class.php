<?php

/**
* @name        JMY CMS
* @link        http://jmy.su/
* @copyright   Copyright (C) 2012-2014 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

if (!defined('ACCESS')) {
    header('Location: /');
    exit;
} 
 
class dateBase extends controlDB 
{
	var $prefix = '';
	public $debug = false;

    public function __construct($debug = false) 
	{
		$this->connectSQL();
		if($debug){
			$this->debug = true;
			var_dump('connect');
			var_dump($this);
			if ($this->ping()) {
				printf ("Соединение в порядке!\n");
			} else {
				printf ("Ошибка: %s\n", mysqli_error($this->resource));
			}
		}
    }
	
	public function connectSQL() 
	{
		require ROOT . 'etc/db.config.php';		
		parent::connect($dbhost, $dbuser, $dbpass, $dbname, $dbport);
		if(!defined("DB_PREFIX")) 	define("DB_PREFIX", $prefix);
		if(!defined("USER_PREFIX")) define("USER_PREFIX", $user_prefix);
		if(!defined("USER_DB")) 	define("USER_DB", $user_db);
		unset($dbhost, $dbuser, $dbpass, $prefix, $user_prefix, $user_db);
    }
	
    public function query($str, $ignoreError = false) 
	{
		if($this->debug){
			var_dump('query');
			var_dump($this);
		}
		if (!$this->ping()) {
			$this->connectSQL();
		}
		return parent::doQuery($str, $ignoreError, $this->prefix);
    }
	
	function safesql($str)
	{
		return parent::safesql($str);
	}

    public function freeResult($resource) 
	{
		return parent::freeResult($resource);
    }

    public function getRow($resource) 
	{
		return parent::getRow($resource);
    }
	
    public function fetchRow($resource) 
	{
		return parent::fetchRow($resource);
    }

    public function numRows($resource) 
	{
		return parent::numRows($resource);
    }
	
	public function insert_id() 
	{
		return parent::insert_id();
    }
	
	public function fetch_all($resource) 
	{
		return parent::fetch_all($resource);
    }
	
	function closeSQL() {
		if($this->debug){
			var_dump('__destruct');
		}
		return parent::close();
	}
	
	function __destruct() {
		$this->closeSQL();
	}
   
}

$db = new dateBase();