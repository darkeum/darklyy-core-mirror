<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2022 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Developers\Actions;

use Boot\System\Http\Request;
use Boot\Abstracts\Actions\Action;
use Symfony\Component\Process\PhpExecutableFinder;

class ComposerAction extends Action
{	
	/**
	 * Определение и выполнение команды Composer
	 *
	 * @param  Boot\System\Http\Request $request Запрос
	 * @return array
	 */
	public function run(Request $request): array
	{				
		// $output = __('developers::composer.output.null');
		$output = 'null';
		$command = null;

		if ($request->has("composer_action")) {

			$output = '';
			$command = $request->get("composer_action");

			switch ($command) {
				case 'dump-autoload':
					$output = static::commandExec('dump-autoload --optimize');
					break;
				case 'about':
					$output = static::commandExec('about');
					break;
				case 'update':
					$output = static::commandExec('update');
					break;
				case 'install':
					$output = static::commandExec('install');
					break;				
				default:
					// $output = __('developers::composer.output.error');
					$output = 'error';
					break;
			}
		}
		return [
			'output' => $output,
			'command' => $command
		];
	}

	/**
	 * Выполнение команды на стороне сервера
	 *
	 * @param  string $command
	 * @return string
	 */
	private static function commandExec(string $command): string
	{
		$phpFinder = new PhpExecutableFinder;
		if (!$phpPath = $phpFinder->find()) {
			throw new \Exception('The php executable could not be found, add it to your PATH environment variable and try again');
		}	
		ob_start();
		system("cd .. \n ".$phpPath.' -f composer.phar '.$command . " 2>&1", $result);
		$contents = ob_get_contents();
		ob_end_clean();
		return $contents;
	}
}
