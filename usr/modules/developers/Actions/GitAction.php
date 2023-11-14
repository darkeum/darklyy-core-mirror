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

class GitAction extends Action
{
	/**
	 * Определение и выполнение команды GIT
	 *
	 * @param  Boot\System\Http\Request $request Запрос
	 * @return array
	 */
	public function run(Request $request): array
	{
		// $output = __('developers::git.output.null');
		$output = 'null';
		$command = null;

		if ($request->has("git_action")) {

			$output = '';
			$command = $request->get("git_action");

			switch ($command) {
				case 'status':
					$output = static::commandExec('git status');
					break;
				case 'resethard':
					$output = static::commandExec('git reset --hard');
					break;
				case 'add':
					$output = static::commandExec('git add --all');
					break;
				case 'pull':
					$output = static::commandExec('git pull');
					break;
				case 'push':
					$output = static::commandExec('git push');
					break;
				case 'commit':
					$comment = '';
					if (!empty($request->query->has("git_comment"))) {
						$comment = $request->query->get("git_comment");
					} else {
						$comment = 'server changes';
					}
					$output = static::commandExec('git commit -a -m ' . escapeshellarg($comment) . '');
					break;
				default:
					// $output = __('developers::git.output.error');
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
		ob_start();
		system($command . " 2>&1", $result);
		$contents = ob_get_contents();
		ob_end_clean();
		return $contents;
	}
}
