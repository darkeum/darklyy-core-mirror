<?php

/*
* @name        JMY CORE
* @link        https://jmy.su/
* @copyright   Copyright (C) 2012-2022 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Sync\Actions;

use Boot\Support\Str;
use Sync\Models\SyncBase;
use Sync\Traits\SyncTrait;
use Sync\Actions\CollectDataAction;
use Sync\UI\CLI\Commands\SyncCommand;
use Boot\System\Core\Abstracts\Actions\Action;
use Symfony\Component\Console\Helper\ProgressBar;

class SyncAction extends Action
{

	use SyncTrait;

	/**
	 * Выполнение действия
	 *
	 * @param  mixed $console
	 * @param  mixed $base
	 * @return void
	 */
	public function run(SyncCommand $console, SyncBase $base): void
	{

		$collect = app(CollectDataAction::class)->run($console, $base);
		if (!empty($collect)) {
			$sectionMain = $console->commandOutput->section();
			$progressBarMain = new ProgressBar($sectionMain);
			$progressBarMain->setFormat(
				"%message%\n%current%/%max% [%bar%] %percent:3s%%"
			);
			$progressBarMain->setMessage('Синхронизация начата:', 'message');
			$progressBarMain->start(count($collect));
			foreach ($collect as $key => $value) {
				sleep(1);
				$progressBarMain->setMessage('Синхронизация раздела: ' . $key, 'message');
				$task = $this->getTaskClassName($key);
				$progressBarMain->advance();
				if (class_exists($task)) {
					try {
						app($task)->run($console, $base, $value);
					} catch (\GuzzleHttp\Exception\ClientException $e) {
						$console->error($e->getResponse()->getBody()->getContents());
					}
				} else {
					$console->error('Не найден класс задачи: ' . $task);
				}
			}
			$progressBarMain->finish();
		}
	}
}
