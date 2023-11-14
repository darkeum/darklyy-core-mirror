<?php
return [

    'sales' => [
        'prefix' => 'PN',
        'database' => 'sales',
        'model' => \Sales\Model\Sales::class,
        'datefield' => 'date',
    ],

    'realization' => [
        'prefix' => 'RN',
        'database' => 'realization',
        'model' => \Realization\Model\Realization::class,
        'datefield' => 'date',
        'datetype' => 'date',
    ],

    'supply' => [
        'prefix' => 'SN',
        'database' => 'supply',
        'model' => \Supply\Model\Supply::class,
        'datefield' => 'date',
    ],

    'supply_order' => [
        'prefix' => 'OSN',
        'database' => 'supply_order',
        'model' => \Supply\Model\SupplyOrder::class,
        'datefield' => 'date_create',
        'datetype' => 'date',
    ],

    'supply_logistics' => [
        'prefix' => 'LSN',
        'database' => 'supply_logistics',
    ],

    'fcontrol' => [
        'prefix' => 'CTR',
        'database' => 'control',
        'model' => \Control\Model\Control::class,
        'datefield' => 'date',
    ],

    'storage_transfer' => [
        'prefix' => 'TR',
        'database' => 'stock_transfer',
        'model' => \Storage\Model\StockTransfer::class,
        'datefield' => 'date',
    ],

    'stock_transfer' => [
        'prefix' => 'TR',
        'database' => 'stock_transfer',
        'model' => \Storage\Model\StockTransfer::class,
        'datefield' => 'date',
    ],

    'object' => [
        'prefix' => 'OB',
        'database' => 'object',
        'model' => \Object\Model\ObjectModel::class,
        'datefield' => 'date',
    ],

    'receipts' => [
        'prefix' => 'PT',
        'database' => 'receipts',
        'model' => \Supply\Model\Receipts::class,
        'datefield' => 'date',
    ],

    'fcontrol_storage' => [
        'prefix' => 'CTS',
        'database' => 'control_storage',
        'model' => \Fcontrol\Model\ControlStorage::class,
        'datefield' => 'date',
    ],

    'price' => [
        'prefix' => 'CP',
        'database' => 'catalog_price',
        'model' => \Price\Models\CatalogPrice::class,
        'datefield' => 'date',
    ],

    'calls' => [
        'prefix' => 'CS',
        'database' => 'calls',
    ],

    'selection' => [
        'prefix' => 'SC',
        'database' => 'selection',
    ],

    'sync' => [
        'prefix' => 'SB',
        'database' => 'sync',
    ],

    'guestbook' => [
        'prefix' => 'GB',
        'database' => 'guestbook',
    ],

    'categories' => [
        'prefix' => 'CT',
        'database' => 'categories',
    ],

    'storage' => [
        'prefix' => 'ST',
        'database' => 'storage',
        'model' => \Storage\Model\Storage::class,
        'datefield' => 'date',
    ],

    'assembly' => [
        'prefix' => 'AS',
        'database' => 'assembly',
        'model' => \Storage\Model\StorageAssembly::class,
        'datefield' => 'date',
    ],

    'boiler_bid' => [
        'prefix' => 'BB',
        'database' => 'boiler_bid',
        'model' => \Boiler\Model\BoilerBid::class,
        'datefield' => 'date_create',
    ],

    'merging' => [
        'prefix' => 'ME',
        'database' => 'merging',
        'model' => \Merging\Model\Merging::class,
        'datefield' => 'date',
        'datetype' => 'date',
    ],

    'correct' => [
        'prefix' => 'CO',
        'database' => 'correct',
        'model' => \Realization\Model\Correct::class,
        'datefield' => 'date',
        'datetype' => 'date',
    ],

    'act_reconciliation' => [
        'prefix' => 'AR',
        'database' => 'act_reconciliation',
        'model' => \Economic\Model\ActReconciliation::class,
        'datefield' => 'date',
    ],
];
