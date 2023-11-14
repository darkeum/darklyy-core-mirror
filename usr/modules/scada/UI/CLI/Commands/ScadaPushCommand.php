<?php

/*
* @name        JMY CORE
* @link        https://jmy.su/
* @copyright   Copyright (C) 2012-2022 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Scada\UI\CLI\Commands;

use Exception;
use Scada\Scada;
use Boot\System\Core\Abstracts\Commands\ConsoleCommand;

class ScadaPushCommand extends ConsoleCommand
{
  /**
   * Имя и параметры консольной команды.
   *
   * @var string
   */
  protected $signature = 'scada:push';

  /**
   * Описание консольной команды.
   *
   * @var string
   */
  protected $description = 'Отправка данных диспетчеризации в WebSocket';

  /**
   * Обработчик консольной команды.
   *
   * @return mixed
   */
  public function handle()
  {
    $this->info('Отправка данных диспетчеризации начата');
    while (1) {
      try {
        Scada::getScadaCollect(4);
      } catch (Exception $exception) {
        dump('An exception occurred' . PHP_EOL);
        dump($exception->getMessage() . PHP_EOL);
        dump($exception->getTraceAsString() . PHP_EOL);
      }
      sleep(5);
    }
  }
}
