<?php

return [

    'models' => [
        'pump' => \Catalog_v2\Model\Pump::class,
        'catalog' => \Catalog_v2\Model\Catalog::class,
        'manufacture' => \Catalog_v2\Model\CatalogManufacture::class,
        'attribute' => \Catalog\Models\CatalogAttribute::class,
    ],

    'attributes' => [
        'model' => 127,
        'pressure' => 132,
        'voltage' => 133,
        'typeBinary' => 138,
        't' => 237,
        'tMaxEnv' => 9999,
        'power' => 137,
        'amperage' => 320,
        'frame' => 326,
        'wheel' => 327,
        'bearing' => 329,
        'dn_in' => 130,
        'dn_out' => 131,
        'length' => 207,
    ],

    'manufacture_logo' => [
        '20' => 'wilo.png',
        '270' => 'cnp.png',
        '271' => 'imp.png',
    ],

    'category' => 363,

    'destination' => ['Отопление', 'Циркуляция ГВС', 'Подпитка отопления', 'Повысительный ХВС / ГВС', 'Пожарные насосы'],
    'version' => 'Версия 4.5.12 - 2023/07/23',
];
