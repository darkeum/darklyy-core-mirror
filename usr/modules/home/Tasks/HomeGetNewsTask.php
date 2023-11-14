<?php

/*
* @name        JMY CORE
* @link        https://jmy.su/
* @copyright   Copyright (C) 2012-2022 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan & Volkov Aleksandr
*/

namespace Home\Tasks;

use News\Models\News;
use Boot\Abstracts\Tasks\Task;
use Boot\Support\Facades\Config;

class HomeGetNewsTask extends Task
{
	/**
	 * Обработчик задачи
	 *
	 * @param  mixed $request
	 * @return array
	 */
	public function run(): array
	{
        $news = News::with('categories')->with('lang')->orderBy('published_at', 'DESC')->limit(5)->get()->append('url');
        $result = [];
        foreach ($news as $value) {
            $preview = $value->preview;       
            $result[] = [
                'category' => $value->category,
                'published_at' => $value->published_at,
                'title' => $value->title,
                'short' => $value->short,
                'full' => $value->full,
                'url' => Config::get('app.url').$value->url,
                'preview' => Config::get('app.url').($preview['full_webp'] ?? ''),
            ];
        }
        return $result;
    }
}