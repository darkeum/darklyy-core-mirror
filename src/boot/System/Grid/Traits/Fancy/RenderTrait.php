<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2022 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Boot\System\Grid\Traits\Fancy;

use Boot\Support\Facades\DB;
use Boot\Support\Str;

trait RenderTrait
{
    #
    public function render()
    {
        $data = [];
        $data['id'] = Str::replace('::', '_', $this->grid_id);

        //$index = 0;
        //foreach ($this->cols as $k => $c) {
        //
        //    if (isset($c['unset']) && $c['unset']) {
        //        continue;
        //    }
        //
        //    $colModel[] = $this->renderColumn($c);
        //    $colIndex[$k] = $index;
        //    $index++;
        //}

        $options = array(
            //'colModel' => $colModel,
        );

        //$options['url'] = $this->renderGridUrl();
        //$data['options'] = $this->renderOptions(array_merge($this->default['options'], $options, $this->options));

        // Сортировка по умолчанию
        //if (isset($data['options']['sort']['name']) && isset($colIndex[$data['options']['sort']['name']])) {
        //    $data['short']['index'] = $colIndex[$data['options']['sort']['name']];
        //    $data['short']['order'] = in_array(Str::lower(($data['options']['sort']['order'] ?? 'asc')),
        //        array('asc', 'desc')) ? Str::lower(($data['options']['sort']['order'] ?? 'asc')) : 'asc';
        //}

        //if (!empty($data['options']['sort']['name']) && isset($colIndex[$data['options']['sort']['name']])) {
        //    $data['short']['index'] = $colIndex[$data['options']['sort']['name']];
        //    $data['short']['order'] = strtolower($data['options']['sort']['order'] ?? 'asc') === 'desc' ? 'desc' : 'asc';
        //}

        //$this->getParams($this);

        return view('grid::fancy.layout', [
            'title' => $this->title ?? 'Список компонента FancyGrid',
            'data' => $data,
            'url' => $this->renderGridUrl(),

            //'options' => $options = Utils::jsonEncode($data['options']),
            //'cols' => $data['options']['colModel'],

            //'language' => $this->getLanguage(),

            //'mouse' => isset($this->mouse) ? $this->mouse + $this->default['mouse'] : $this->default['mouse'],
        ]);
    }

    #
    protected function renderColumn($c)
    {
        if (is_object($c['db']) and $c['db'] instanceof \Illuminate\Database\Query\Expression) {
            $c['db'] = strval($c['db']->getValue(DB::connection()->getQueryGrammar()));
        }
        return $c;
    }

    #
    protected function renderPostData()
    {
        return array();
    }

    #
    protected function renderOptions($opts)
    {
        return $opts;
    }

    #
    protected function renderNav()
    {
        if (is_array($this->nav)) {
            $nav = array_merge($this->default['nav'], $this->nav);
        } else {
            $nav = $this->default['nav'];
        }
        return $nav;
    }

    #
    protected function renderGridUrl()
    {
        return route('grid.index', $this->grid_id);
    }

    #
    protected function getParams($t)
    {
        //dd($t);

        //$params = collect();

        //$this = $t;

        //$params->put('configs', [
        //    '' => '',
        //    '' => '',
        //    '' => '',
        //    '' => '',
        //    '' => '',
        //]);

    }

    # Языковой файл для Grid
    protected function getLanguage()
    {
        $language = trans('grid::lang');

        //
        // $language['infoPostFix'] = " © " . trans('app.name_' . check_company(true));

        // Замена текста в лоадере на анимацию
        $language['loadingRecords'] = str_replace(["\r\n", "\r", "\n", "\t", '  '], '',
            view("elements.preloader", ['bg' => false])
        );

        return collect($language)->toJson(JSON_UNESCAPED_UNICODE);
    }

    # Функция фильтрации значений в столбцах // Не найдено использование
    public function checkFilters($type, $filters, $default = null)
    {
        return array_key_exists($type, $filters) ? $filters[$type] : $default;
    }

}
