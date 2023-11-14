<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2022 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace App\Providers;

use Darkeum\Menu\Item;
use Darkeum\Menu\Link;
use Darkeum\Menu\Menu;
use Support\Models\Support;
use Darkeum\Menu\MenuManager;
use Task\Support\TaskSupport;
use Order\Support\OrderSupport;
use Boot\Support\ServiceProvider;
use Supply\Support\SupplySupport;
use Control\Support\ControlSupport;
use Storage\Support\StorageSupport;
use PhpParser\Node\Expr\Instanceof_;

class AdminMenuServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $vendor = $this->app->make(MenuManager::class)->get();
        Menu::macro('main', function () use ($vendor) {
            $task_count = TaskSupport::getTaskCount();
            $order_count = OrderSupport::getOrderCount();

            $control = ControlSupport::getControlCount();
            $control_pay = ControlSupport::getControlPayCount();
            $control_all = $control + $control_pay;

            $count_recount = StorageSupport::getStorageCount();
            $count_assembly = StorageSupport::getStorageAssemblyCount();
            $count_transfer = StorageSupport::getStorageTransferCount();
            $count_storage = $count_recount + $count_assembly + $count_transfer;

            $count_type_1 = SupplySupport::getSupplyCount(1);
            $count_type_2 = SupplySupport::getSupplyCount(2);
            $count_type_all = $count_type_1 + $count_type_2;

            $support_count = Support::whereNot('status', 3)->count();

            $menu = Menu::new()
                ->addClass('navigation navigation-main')
                ->setAttribute('id', 'main-menu-navigation')
                ->setAttribute('data-menu', 'menu-navigation')

                // Главная
                ->add(Link::toRoute('admin.home.dashboard', 'Главная')
                    ->setIcon('fa-light fa-house'))

                // Задачи
                ->add(Link::toUrl('/administration/module/task', 'Задачи')
                    ->setIcon('fa-light fa-badge-check')
                    ->setBadge($task_count))

                // Заказы
                ->submenu([
                    'text' => 'Заказы',
                    'icon' => 'fa-light fa-cart-shopping',
                ], function (Menu $menu) {
                    $menu
                        ->add(Link::toUrl('/administration/module/boiler', 'Котел')
                            ->setIcon('fa-light fa-cauldron'))
                        ->add(Link::toRoute('admin.sales.index', 'Все счета')
                            ->setIcon('fa-light fa-book'))
                        ->add(Link::toUrl('/administration/module/realization', 'Реализации')
                            ->setIcon('fa-light fa-rub'))
                        ->add(Link::toUrl('/administration/module/realization/correct', 'Документы корректировки')
                            ->setIcon('fa-light fa-edit'));
                })

                // Клиенты
                ->submenu([
                    'text' => 'Клиенты',
                    'icon' => 'fa-light fa-users',
                    'badge' => $order_count,
                ], function (Menu $menu) use ($order_count) {
                    $menu
                        ->add(Link::toRoute('admin.calls.index', 'Звонки')->setIcon('fa-light fa-phone'))
                        ->add(Link::toRoute('admin.order.index', 'Заявки и обращения')
                            ->setIcon('fa-light fa-comment-alt-check')
                            ->setBadge($order_count))
                        ->add(Link::toRoute('admin.organizations.index', 'Контрагенты')
                            ->setIcon('fa-light fa-building'))
                        ->add(Link::toUrl('/administration/module/promotions', 'Акции')
                            ->setIcon('fa-light fa-gift'));
                })

                // Закупки
                ->submenu([
                    'text' => 'Закупки',
                    'icon' => 'fa-light fa-boxes-packing',
                    'badge' => $count_type_all,
                ], function (Menu $menu) use ($count_type_1, $count_type_2) {
                    $menu
                        ->add(Link::toUrl('/administration/module/supply', 'Заявки на закупку')
                            ->setIcon('fa-light fa-clipboard-list-check')
                            ->setBadge($count_type_1))
                        ->add(Link::toUrl('/administration/module/supply/twomain', 'Заявки на сроки')
                            ->setIcon('fa-light fa-truck-clock')
                            ->setBadge($count_type_2))
                        ->add(Link::toUrl('/administration/module/supply/order', 'Счета поставщиков')
                            ->setIcon('fa-light fa-file-invoice-dollar'))
                        ->add(Link::toUrl('/administration/module/supply/receipts', 'Поступления')
                            ->setIcon('fa-light fa-receipt'))
                        ->add(Link::toUrl('/administration/module/supply/logistics_receipts', 'Приемка груза')
                            ->setIcon('fa-light fa-truck-ramp-box'));
                })
                ->submenuIfCan(
                    ['delivery access'],
                    [
                        'text' => 'Логистика',
                        'icon' => 'fa-light fa-truck-moving',
                    ],
                    function (Menu $menu) {
                        $menu
                            ->add(Link::toUrl('/administration/module/logistics', 'Забор товаров')
                                ->setIcon('fa-light fa-truck-plane'))
                            ->add(Link::toUrl('/administration/module/logistics/delivery', 'Доставка товаров')
                                ->setIcon('fa-light fa-truck-fast'));
                    }
                )

                // Склад
                ->submenu([
                    'text' => 'Склад',
                    'icon' => 'fa-light fa-boxes-stacked',
                    'badge' => $count_storage,
                ], function (Menu $menu) use ($count_recount, $count_assembly, $count_transfer) {
                    $menu
                        ->add(Link::toUrl('/administration/module/storage', 'Заявки на пересчет')
                            ->setIcon('fa-light fa-truck-plane')
                            ->setBadge($count_recount))
                        ->add(Link::toUrl('/administration/module/storage/assembly', 'Заявки на сборку')
                            ->setIcon('fa-light fa-pallet-boxes')
                            ->setBadge($count_assembly))
                        ->add(Link::toUrl('/administration/module/storage/docPrint', 'Разрешенная отгрузка')
                            ->setIcon('fa-light fa-box-check'))
                        ->add(Link::toUrl('/administration/module/merging', 'Комплектация товаров')
                            ->setIcon('fa-light fa-box-circle-check'))
                        ->add(Link::toUrl('/administration/module/storage/transfer', 'Перемещение товаров')
                            ->setIcon('fa-light fa-people-carry-box')
                            ->setBadge($count_transfer))
                        ->add(Link::toUrl('/administration/module/storage/delivery_storage', 'Доставка')
                            ->setIcon('fa-light fa-truck'))
                        ->add(Link::toRoute('admin.warranties.create', 'Гарантийный талон')
                            ->setIcon('fa-light fa-id-card'));
                })

                // Проектирование
                ->submenu([
                    'text' => 'Проектирование',
                    'icon' => 'fa-light fa-drafting-compass',
                ], function (Menu $menu) {
                    $menu
                        ->add(Link::toUrl('/administration/module/requests', 'Заявки на расчет')
                            ->setIcon('fa-light fa-calculator'))
                        ->add(Link::toUrl('/administration/module/selection/request', 'Заявки на БТП')
                            ->setIcon('fa-light fa-cubes'))
                        ->add(Link::toUrl('/administration/module/selection', 'Блоки')
                            ->setIcon('fa-light fa-cubes-stacked'))
                        ->add(Link::toUrl('/administration/module/selection/metering', 'Узлы учета')
                            ->setIcon('fa-light fa-gauge-circle-bolt'));
                })

                // Оплата и контроль
                ->submenuIfCan(
                    ['control pay', 'control supply', 'control storage'],
                    [
                        'text' => 'Оплата и контроль',
                        'icon' => 'fa-light fa-comments-dollar',
                        'badge' => $control_all,
                    ],
                    function (Menu $menu) use ($control, $control_pay) {
                        $menu
                            ->addIfCan('control supply', Link::toRoute('admin.controls.index', 'Закупки')->setIcon('fa-light fa-money-check-pen')->setBadge($control))
                            ->addIfCan('control storage', Link::toRoute('admin.controls.storage.index', 'Остатки')->setIcon('fa-light fa-square-this-way-up'))
                            ->addIfCan('control pay', Link::toRoute('admin.controls.pay.index', 'Оплаты')->setIcon('fa-light fa-rub')->setBadge($control_pay))
                            ->addIfCan('control god',  Link::toRoute('admin.controls.god', 'Отгрузка в минус')->setIcon('fa-light fa-circle-exclamation'));
                    }
                )

                // Поверка приборов
                ->add(Link::toUrl('/administration/module/verification', 'Поверка приборов')
                    ->setIcon('fa-light fa-gauge-circle-plus'))

                // Диспетчеризация
                ->add(Link::toRoute('admin.scada.dashboard', 'Диспетчеризация')
                    ->setIcon('fa-light fa-chart-scatter'))

                // Объекты
                ->add(Link::toRoute('admin.objects.index', 'Объекты')
                    ->setIcon('fa-light fa-briefcase'))

                // Каталог товаров
                ->submenu([
                    'text' => 'Каталог товаров',
                    'icon' => 'fa-light fa-sitemap',
                ], function (Menu $menu) {
                    $menu
                        ->add(Link::toRoute('admin.catalog.index', 'Список товаров')
                            ->setIcon('fa-light fa-list'))
                        ->add(Link::toRoute('admin.catalog.excel.index', 'Работа с Excel')
                            ->setIcon('fa-light fa-file-excel'))
                        ->add(Link::toRoute('admin.price.index', 'Прайс листы')
                            ->setIcon('fa-light fa-rub'))
                        ->add(Link::toRoute('admin.catalog.pump.equate', 'Приравнивание')
                            ->setIcon('fa-regular fa-equals'));
                })

                // Касса
                ->add(Link::toRoute('admin.cashbox.index', 'Касса')
                    ->setIcon('fa-light fa-cash-register'))

                // Администрирование
                ->submenu([
                    'text' => 'Администрирование',
                    'icon' => 'fa-light fa-building',
                ], function (Menu $menu) {
                    $menu
                        ->add(Link::toRoute('admin.reestr.index', 'Реестр договоров')
                            ->setIcon('fa-light fa-books'))
                        ->add(Link::toRoute('admin.decrees.index', 'Реестр приказов')
                            ->setIcon('fa-light fa-file-check'))
                        ->add(Link::toRoute('admin.bank.index', trans('bank::main.module'))
                            ->setIcon('fa-light fa-money-bill-transfer'))
                        ->add(Link::toUrl('/administration/module/economic/act', 'Акты сверок')
                            ->setIcon('fa-light fa-file-signature'))
                        ->add(Link::toRoute('admin.inventory.index', 'Инвентаризация')
                            ->setIcon('fa-light fa-shelves'))
                        ->add(Link::toRoute('admin.orgs.index', 'Организации')
                            ->setIcon('fa-light fa-buildings'))
                        ->add(Link::toRoute('admin.stocks.index', 'Склады')
                            ->setIcon('fa-light fa-warehouse-full'))
                        ->add(Link::toRoute('admin.users.index', 'Пользователи')
                            ->setIcon('fa-light fa-users'));
                })

                // Отчеты
                ->add(Link::toRoute('admin.report.index', 'Отчеты')
                    ->setIcon('fa-light fa-chart-pie'))

                // Сайт
                ->submenu([
                    'text' => 'Сайт',
                    'icon' => 'fa-light fa-globe',
                ], function (Menu $menu) {
                    $menu
                        ->add(Link::toRoute('admin.news.index', 'Новости')
                            ->setIcon('fa-light fa-newspaper'))
                        ->add(Link::toRoute('admin.slider.index', 'Слайдер')
                            ->setIcon('fa-light fa-gallery-thumbnails'))
                        ->add(Link::toRoute('admin.job.index', 'Вакансии')
                            ->setIcon('fa-light fa-user-plus'))
                        ->add(Link::toRoute('admin.reviews.index', 'Отзывы клиентов')
                            ->setIcon('fa-light fa-star'))
                        ->add(Link::toRoute('admin.certificates.index', 'Сертификаты')
                            ->setIcon('fa-light fa-file-certificate'))
                        ->addVendor('site');
                })

                // Файлы
                ->submenu([
                    'text' => 'Файлы',
                    'icon' => 'fa-light fa-folder-open',
                ], function (Menu $menu) {
                    $menu
                        ->add(Link::toUrl('/administration/module/files', 'Серверное хранилище')
                            ->setIcon('fa-light fa-list'))
                        ->add(Link::toUrl('/administration/module/files/project', 'Проектирование')
                            ->setIcon('fa-light fa-file-excel'));
                })

                // Профиль - Перенос в правое дроп меню мини профиля в админке
                /*
                ->submenu([
                    'text' => 'Профиль',
                    'icon' => 'fa-light fa-user',
                ], function (Menu $menu) {
                    $menu
                        ->add(Link::toRoute('admin.profile.index', 'Мой профиль')
                            ->setIcon('fa-light fa-user-tie'))
                        ->add(Link::toRoute('admin.notifications.index', 'Уведомления')
                            ->setIcon('fa-light fa-bell'));
                })
                */

                // Прочее
                ->submenu([
                    'text' => 'Прочее',
                    'icon' => 'fa-light fa-ballot-check',
                ], function (Menu $menu) {
                    $menu
                        ->add(Link::toRoute('admin.changelog.index', 'История изменений')
                            ->setIcon('fa-light fa-clock-rotate-left'))
                        ->add(Link::toRoute('admin.integration.index', 'Интеграции')
                            ->setIcon('fa-light fa-rotate'))
                        ->add(Link::toRoute('admin.developers.index', 'Для разработчиков')
                            ->setIcon('fa-light fa-brackets-curly'));
                })

                // Поддержка
                ->add(Link::toRoute('admin.support.index', 'Поддержка')
                    ->setIcon('fa-light fa-circle-question')
                    ->setBadge($support_count));

            $menu->addVendor();
            $menu->setActiveFromRequest()->toArray();
            return $menu;
        });
    }
}
