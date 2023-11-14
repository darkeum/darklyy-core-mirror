<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2022 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Notifications\UI\WEB\Controllers;

use Boot\Support\Facades\Auth;
use Notifications\Models\Notifications;
use Boot\Abstracts\Controllers\WebController;
use Boot\Support\Carbon;

class ControllerRedirect extends WebController
{
    static public function redirect($id)
    {
        if (isset($id)) {
            $item = Notifications::findOrFail($id);
            if ($item) {
                if ($item->uid == Auth::id()) {
                    if ($item->status == 1) {
                        $item->update([
                            'status' => 2,
                            'read_date' => Carbon::now(),
                        ]);
                    }
                    return redirect($item->url);
                }
            }
        } 
        return abort('404');
    }
}
