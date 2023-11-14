<?php

/*
* @name        JMY CORE
* @link        https://jmy.su/
* @copyright   Copyright (C) 2012-2021 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

class AutoloaderFramework {

	//конструктор с вызовом spl_autoload_register
	public function __construct() {
		spl_autoload_register(array($this, 'loader'));
	}

	//поиска классов
	public function load($className, $patch, $type = null) {
		$patchArray = explode("/", $patch);
		$className = str_replace('\\', '/', $className);
		$classArray = explode("/", $className);
		$classNameClear = array(array_pop($classArray).'.class.php');
		switch($type) {
			default:				
				$correctPatch = implode('/', array_merge($patchArray, $classArray, $classNameClear));
			break;
			case "noFirst":
				array_shift($classArray);				
				$correctPatch = implode('/', array_merge($patchArray, $classArray, $classNameClear));
			break;
			case "module":
				$moduleName = array(mb_strtolower(array_shift($classArray)), 'sub_classes');
				$correctPatch = implode('/', array_merge($patchArray, $moduleName, $classArray, $classNameClear));
			break;
			case "moduleShift":
				$moduleName = array(mb_strtolower(array_shift($classArray)));
				$correctPatch = implode('/', array_merge($patchArray, $moduleName, $classArray, $classNameClear));
			break;
		}
		if(!in_array($correctPatch, $this->ignorePathArray())) {
			$correctPatch = ROOT.$correctPatch;			
			if (file_exists($correctPatch)){
				require_once($correctPatch);
				return true;			
			} elseif (file_exists(mb_strtolower($correctPatch))) {
				require_once(mb_strtolower($correctPatch));
				return true;
			}
		}		
		return false;
	}

	//список путей для классов
	public function pathArray() {
		$pathArray = array(
			'boot' => array('boot', 'noFirst'),
			'boot/sub_classes' => 'boot/sub_classes',
			'usr/other/sub_classes' => 'usr/other/sub_classes',
			'module' => array('usr/modules', 'module'),
			'moduleShift' => array('usr/modules', 'moduleShift'),
			'api' => array('api', 'noFirst'),
			'database/Seeders/' => array('Database', 'noFirst'),
		);
		return $pathArray;
	}

	//черный список
	public function ignorePathArray() {
		$ignorePathArray = array(
			'db' => 'boot/DB.class.php',			
			'Autoloader' => 'boot/Autoloader.class.php',			
		);
		return $ignorePathArray;
	}

	//запуск поиска классов
	public function loader($className) {
		foreach ($this->pathArray() as $patch) {
			if(is_array($patch)){
				$loaded = $this->load($className, $patch[0], $patch[1]);
			} else{
				$loaded = $this->load($className, $patch);
			}
			if($loaded) {
				return true;
			}			
		}		
		return false;
	}
}

$autoloader = new AutoloaderFramework();