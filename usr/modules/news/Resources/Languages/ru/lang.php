<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2022 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

return [

    /*
    |--------------------------------------------------------------------------
    | News Language Lines
    |--------------------------------------------------------------------------
    |
    | Этот языковой файл используется в модуле "Новости"
    | Вы можете изменить эти языковые строки в соответствии с
	| требованиями вашего приложения.
    |
    */

    // Название модуля
    'module' => 'Новости',
    'all' => 'Все новости',

    // Перевод для Админ панели
    'admin' => [
        'list' => 'Все новости',
        'add' => 'Добавить новость',
        'ago' => 'назад',

        'table' => [
            'title' => 'Список новостей',
            'grid' => [
                //'title' => '',
            ]
        ],

        'status' => [
            0 => 'Не опубликован',
            1 => 'Опубликован',
            2 => 'На удаление',
        ],

        '' => '',
    ],

    // Перевод для публичной части сайта
    'public' => [
        //'manager' => 'Менеджер новостей',
        //'list' => 'Список новостей',

        'editor.added' => 'Добавление новости',
        'editor.edited' => 'Изменение новости',

        'editor.add' => 'Добавить',
        'editor.edit' => 'Изменить',

        'editor.info' => 'Информация об новости',

        'editor.title' => 'Заголовок отзыва',
        'editor.title.placeholder' => 'Укажите заголовок отзыва',
        'editor.title.description' => 'Например "Благодарственное письмо"',

        'editor.slug' => 'Ссылка на отзыв',
        'editor.slug.placeholder' => 'Укажите ссылку на отзыва',
        'editor.slug.description' => 'Если оставить пустым заполнится автоматически',

        'editor.category' => 'Категория',
        'editor.category.description' => 'Категория к которой относится контрагент оставивший отзыв',

        'editor.published_at' => 'Дата публикации новости',
        'editor.published_at.placeholder' => 'Укажите дату публикации новости',
        'editor.published_at.description' => 'Указывается дата на которую будет опубликована новость',

        'editor.tags' => 'Теги',
        'editor.tags.placeholder' => 'Укажите теги',
        'editor.tags.description' => 'Теги указываются через запятую, например "Поставка, Монтаж"',

        'editor.preview' => 'Изображение новости',
        'editor.preview.placeholder' => 'Укажите изображение новости',
        'editor.preview.description' => 'Поддерживаются форматы jpg, jpeg, png, gif',

        'editor.short' => 'Краткое содержание новости',
        'editor.short.placeholder' => 'Напишите краткое содержание новости',
        'editor.short.description' => 'Данное содержание показывается в спике новостей',

        'editor.full' => 'Полное содержание новости',
        'editor.full.placeholder' => 'Напишите полное содержание новости',
        'editor.full.description' => 'Данное содержание показывается при просмотре новости',

        'editor.seo' => 'Настройка SEO',

        'editor.fulltitle' => 'Расширенный заголовок',
        'editor.fulltitle.placeholder' => 'Укажите расширенный заголовок',
        'editor.fulltitle.description' => 'Данный заголовок используется поисковиками для формирования название страницы в поиске',

        'editor.description' => 'Описание',
        'editor.description.placeholder' => 'Укажите описание страницы',
        'editor.description.description' => 'Данное описание используется поисковиками для формирования описания страницы в поиске',

        'editor.keywords' => 'Ключевые слова',
        'editor.keywords.placeholder' => 'Укажите список ключевых слов через запятую',
        'editor.keywords.description' => 'Не обязательно, поскольку Яндекс и Google не использует их ',

        'grid.title' => 'Заголовок новости',
        'grid.views' => 'Просмотров',

        //'status.on' => 'Опубликован',
        //'status.off' => 'Отключен',

        'info' => 'Информация',
        'info.add' => 'Новость успешно добавлена',
        'info.edit' => 'Новость успешно изменена',
        'info.list' => 'Просмотреть список новостей?',

        '' => '',
    ]

];
