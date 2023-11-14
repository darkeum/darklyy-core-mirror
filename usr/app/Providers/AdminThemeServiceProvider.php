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

use Chat;
use Boot\Support\Currency;
use Boot\Support\Facades\App;
use Boot\System\Http\Request;
use Task\Support\TaskSupport;
use Boot\Support\Facades\Auth;
use Order\Support\OrderSupport;
use App\Components\Layouts\Base;
use Boot\Support\ServiceProvider;
use Supply\Support\SupplySupport;
use Control\Support\ControlSupport;
use Storage\Support\StorageSupport;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Blade;
use Notifications\Support\NotificationsSupport;

class AdminThemeServiceProvider extends ServiceProvider
{

    /**
     * Register any authentication / authorization services.
     */
    public function boot(Request $request): void
    {
        Blade::component('layouts.base', Base::class);

        view()->composer('*', function ($view) {
            $victoria = new \Victoria;
            $victoria->link = "/administration/fastCart";
            $uid = Auth::id();
            $task_count = TaskSupport::getTaskCount();

            $fcontrol = ControlSupport::getControlCount();
            $fcontrol_pay = ControlSupport::getControlPayCount();
            $fcontrol_all = $fcontrol + $fcontrol_pay;

            $count_type_1 = SupplySupport::getSupplyCount(1);
            $count_type_2 = SupplySupport::getSupplyCount(2);
            $count_type_all = $count_type_1 + $count_type_2;

            $count_recount = StorageSupport::getStorageCount();
            $count_assembly = StorageSupport::getStorageAssemblyCount();
            $count_transfer = StorageSupport::getStorageTransferCount();
            $count_storage = $count_recount + $count_assembly + $count_transfer;


            $chat = App::make(Chat::class);

            $order_count = OrderSupport::getOrderCount();
            $client_all_count =  $order_count;

            $notifications = '';
            $notif_count =  NotificationsSupport::getNotificationsCount();
            if ($notif_count > 0) {
                foreach (NotificationsSupport::getNotificationsBar() as $value) {
                    $icon = '';
                    if (!empty($value['icon'])) {
                        $icon = $value['icon'];
                    }
                    $notifications .=  '<li style="cursor:pointer" onclick="location.href=\'/' . ADMIN . '/module/notif/redirect/' . $value['id'] . '\';" class="list-group-item"><span class="pull-left mg-t-xs mg-r-md"><img src="' . $icon . '" class="avatar avatar-sm img-circle" alt=""></span><div class="m-body show pd-t-xs"><span>' . $value['title'] . '</span><br>' . $value['text'] . '</div></li>';
                }
            }

            $avatar = avatar($uid);
            $foot = '';
            $js_code = '';
            $body_class = '';

            $us =  Currency::get_exchange()->get(840);
            $us_old = Currency::get_exchange( date("Y-m-d", strtotime('-1 day')))->get(840);
            $us_dis = $us - $us_old;
            $eur = Currency::get_exchange()->get(978);
            $eur_old = Currency::get_exchange( date("Y-m-d", strtotime('-1 day')))->get(978);
            $eur_dis = $eur - $eur_old;

            View::share('key', 'value');
            View::share('manager_theme', '/usr/tpl/corp');
            View::share('manager_admin', '/usr/tpl/admin');
            View::share('manager', '/administration');
            View::share('isUser', Auth::isUser());
            View::share('isAdmin', Auth::isAdmin());
            View::share('user', Auth::user());
            View::share('body_class', '');
            View::share('NOW_COMPANY', check_company(true)); // Меняем дизайн сайта от компании для TWIG шаблонов ???
            View::share('victoria_click', $victoria->open_func());
            View::share('notif_numb', $notif_count);
            View::share('notif', $notifications);
            View::share('order_count', $order_count);
            View::share('client_all_count', $client_all_count);
            View::share('avatar', $avatar);
            View::share('foot', $foot);
            View::share('js_code', $js_code);
            View::share('messages_numb', 0);
            View::share('friends_numb', 0);
            View::share('price_us', $us);
            View::share('price_us_dis', number_format($us_dis, 4, '.', ' '));
            View::share('price_us_dis_color', $us_dis > 0 ? "amount_middle__under-g text-success" : "amount_middle__under-r text-danger");
            View::share('price_eur', $eur);
            View::share('price_eur_dis', number_format($eur_dis, 4, '.', ' '));
            View::share('price_eur_dis_color', $eur_dis > 0 ? "amount_middle__under-g text-success" : "amount_middle__under-r text-danger");
            View::share('task_count', $task_count);
            View::share('storage_count', $count_storage);
            View::share('recount_count', $count_recount);
            View::share('assembly_count', $count_assembly);
            View::share('transfer_count', $count_transfer);
            View::share('fcontrol', $fcontrol);
            View::share('fcontrol_pay', $fcontrol_pay);
            View::share('fcontrol_all', $fcontrol_all);
            View::share('supply_count', $count_type_all);
            View::share('supply_count_type_1', $count_type_1);
            View::share('supply_count_type_2', $count_type_2);
            View::share('chat_data', $chat->init_twig());
        });
    }
}
