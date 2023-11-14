<?php

return [

    'types' => [
        'text' => [
            0 => "Договоры (авто)",
            1 => "Договоры",
            2 => "Спецификации",
            3 => "Дополнительные соглашения",
            4 => "Приложения",
            5 => "Договоры поручительства",
        ],

        'textSolo' =>[
            0 => "Договор (авто)",
            1 => "Договор",
            2 => "Спецификация",
            3 => "Доп. соглашение",
            4 => "Приложение",
            5 => "Договор поручительства",
        ],

        // tree - Формирование дерева для документов
        'icons' => [
            'default' => [
                'icon' => 'far fa-folder',
            ],
            'pdf' => [
                'icon' => 'fas fa-file-pdf text-danger',
            ],
            'docx' => [
                'icon' => 'fas fa-file-word text-info',
            ],
            'html' => [
                'icon' => 'fab fa-html5 text-danger',
            ],
            'css' => [
                'icon' => 'fab fa-css3-alt text-info',
            ],
            'img' => [
                'icon' => 'far fa-file-image text-success',
            ],
            'js' => [
                'icon' => 'fab fa-node-js text-warning',
            ],
        ],


    ],

];
