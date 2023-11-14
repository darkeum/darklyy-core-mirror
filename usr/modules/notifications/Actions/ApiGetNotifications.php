<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2022 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Notifications\Actions;

use Boot\Traits\SearchApi;
use Boot\Support\Facades\Auth;
use Boot\Abstracts\Actions\Action;
use Notifications\Models\Notifications;

class ApiGetNotifications extends Action
{
  use SearchApi;

  protected $model = Notifications::class;

  /**
   * Обработчик действия
   *
   * @param  mixed $request
   * @return array
   */
  public function run(): array
  {
    $this->data = $this->model::select(array('notif.id', 'notif.module', 'notif.pid', 'notif.url', 'notif.text', 'notif.title', 'notif.icon', 'notif.date', 'notif.read_date', 'notif.status'));	
    $requestData = array();
    $requestData['filter']['uid'] = array('operator' => '=', 'value' => Auth::id());
    $requestData['select'] = array('notif.*');
    return $this->get_($requestData);			
  }
}
