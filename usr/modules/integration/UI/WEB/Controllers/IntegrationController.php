<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2022 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Integration\UI\WEB\Controllers;

use Boot\Controller\AbstractController;

class IntegrationController extends AbstractController
{

    /**
     * Список сокращенных URL
     *
     * @return void
     */
    static function index()
    {
        return view('integration::manager.index');
    }

    /**
     * Редактор для изменения сокращенных URL
     *
     * @param int $id
     * @return void
     */
    public static function edit($id)
    {
        return view('integration::manager.edit', [
            'integration' => Integration::findOrFail($id)
        ]);
    }
}
