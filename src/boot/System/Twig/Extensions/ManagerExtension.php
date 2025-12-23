<?php

/*
 * This file is part of Twig.
 *
 * (c) Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Boot\System\Twig\Extensions;


use Twig\TwigFunction;
use Boot\Support\Currency;
use Chat\Support\ChatSupport;
use Task\Support\TaskSupport;
use \Boot\Support\Facades\Auth;
use Order\Support\OrderSupport;
use Supply\Support\SupplySupport;
use Control\Support\ControlSupport;
use Storage\Support\StorageSupport;
use Twig\Extension\GlobalsInterface;
use Twig\Extension\AbstractExtension;
use Notifications\Support\NotificationsSupport;

class ManagerExtension extends AbstractExtension implements GlobalsInterface
{



    /**
     * Метод определяет глобальные переменные
     *
     * @return array
     */
    public function getGlobals(): array
    {
        $uid = Auth::id();
        $task_count =  TaskSupport::getTaskCount();

        $fcontrol = ControlSupport::getControlCount();
        $fcontrol_pay = ControlSupport::getControlPayCount();
        $fcontrol_all = $fcontrol + $fcontrol_pay;

        $count_recount = StorageSupport::getStorageCount();
        $count_assembly = StorageSupport::getStorageAssemblyCount();
        $count_transfer = StorageSupport::getStorageTransferCount();
        $count_storage = $count_recount + $count_assembly + $count_transfer;

        $count_type_1 = SupplySupport::getSupplyCount(1);
        $count_type_2 = SupplySupport::getSupplyCount(2);
        $count_type_all = $count_type_1 + $count_type_2;



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
                $notifications .=  '<li style="cursor:pointer" onclick="location.href=\'/' . ADMIN . '/module/notif/redirect/' . $value['id'] . '\';" class="list-group-item"><span class="pull-left mg-t-xs mg-r-md"><img src="' . $icon . '" class="avatar avatar-sm img-circle" alt=""></span><div class="m-body show pd-t-xs"><span>' . $value['title'] . '</span><br>' . ee($value['text']) . '</div></li>';
            }
        }
        $avatar = avatar($uid);
        $foot = '';

        $body_class = '';

        $us =  Currency::get_exchange()->get(840);
        $us_old = Currency::get_exchange( date("Y-m-d", strtotime('-1 day')))->get(840);
        $us_dis = $us - $us_old;
        $eur = Currency::get_exchange()->get(978);
        $eur_old = Currency::get_exchange( date("Y-m-d", strtotime('-1 day')))->get(978);
        $eur_dis = $eur - $eur_old;

        $victoria = new \Victoria;
        $victoria->link = "/administration/fastCart";

        $globals = [];
        $globals['victoria_click'] = $victoria->open_func();
        $globals['notif_numb'] = $notif_count;
        $globals['notif'] = $notifications;
        $globals['order_count'] =  $order_count;
        $globals['client_all_count'] =  $client_all_count;
        $globals['avatar'] = $avatar;
        $globals['foot'] = $foot;
        $globals['body_class'] = $body_class;
        $globals['NOW_COMPANY'] = check_company(true);  // Меняем дизайн сайта от компании для TWIG шаблонов
        //!!! удалить
        $globals['messages_numb'] = 0;
        $globals['friends_numb'] = 0;

        $globals['price_us'] = $us;
        $globals['price_us_dis'] = number_format($us_dis, 4, '.', ' ');
        $globals['price_us_dis_color'] = $us_dis > 0 ? "amount_middle__under-g" : "amount_middle__under-r";
        $globals['price_eur'] = $eur;
        $globals['price_eur_dis'] = number_format($eur_dis, 4, '.', ' ');
        $globals['price_eur_dis_color'] = $eur_dis > 0 ? "amount_middle__under-g" : "amount_middle__under-r";

        $globals['task_count'] = $task_count;

        $globals['storage_count'] = $count_storage;
        $globals['recount_count'] = $count_recount;
        $globals['assembly_count'] = $count_assembly;
        $globals['transfer_count'] = $count_transfer;

        $globals['fcontrol'] = $fcontrol;
        $globals['fcontrol_pay'] = $fcontrol_pay;
        $globals['fcontrol_all'] = $fcontrol_all;

        $globals['supply_count'] = $count_type_all;
        $globals['supply_count_type_1'] = $count_type_1;
        $globals['supply_count_type_2'] = $count_type_2;

        $globals['chat_data']['template'][] = ChatSupport::oldInterface();

        return $globals;
    }


    /**
     * Метод определяет глобальные функции
     *
     * @return array
     */
    public function getFunctions(): array
    {
        require_once ROOT . 'root/functions.php';

        $menu_open = new TwigFunction('menu_open', function ($key) {
            $matches = ['1' => $key];
            echo $this->openMenu($matches);
        });
        $menu_choose = new TwigFunction('menu_choose', function ($key) {
            $matches = ['1' => $key];
            echo $this->chooseMenu($matches);
        });
        $menu_choose_module = new TwigFunction('menu_choose_module', function ($key, $sub) {
            $matches = ['1' => $key, $sub];
            echo $this->chooseMenuModule($matches);
        });
        $check_active = new TwigFunction('check_active', function ($value) {
            $matches = ['1' => $value];
            echo $this->checkActive($matches);
        });
        $langConst = new TwigFunction('langConst', function ($key) {
            if (defined($key)) {
                echo constant($key);
            }
        });


        return array(
            'menu_open' => $menu_open,
            'menu_choose' => $menu_choose,
            'menu_choose_module' => $menu_choose_module,
            'check_active' => $check_active,
            'langConst'   => $langConst
        );
    }

    /**
     * Возвращает имя расширения
     * @return string
     */
    public function getName()
    {
        return 'framework_extension';
    }

    protected function getUrl()
    {
        $url = explode('/', \Boot\Support\Facades\Request::path());
        return $url;
    }

    protected function openMenu($matches)
    {
        $url = $this->getUrl();
        $type = $matches[1];
        $typeArray = explode(',', $type);
        $ccount = 1;
        if (!empty($url[1])) {
            if ($url[1] == 'module') {
                $ccount = 2;
            }
            if (in_array($url[$ccount], $typeArray)) {
                return 'menu-open';
            }
        }
    }

    protected function chooseMenu($matches)
    {
        $url = $this->getUrl();
        $type = $matches[1];
        $typeArray = explode(',', $type);
        $ccount = 1;
        if (!empty($url[1])) {
            if ($url[1] == 'module') {
                $ccount = 2;
            }
        } else {
            $ccount = 0;
        }
        if ((in_array($url[$ccount], $typeArray)) || (($type == 'main') && (empty($url[1])))) {
            return 'class="active"';
        }
    }

    protected function chooseMenuModule($matches)
    {
        $url = $this->getUrl();
        $type = $matches[2];
        $module = $matches[1];
        $typeArray = explode(',', $type);
        $ccount = 1;
        if (!empty($url[1])) {
            if (($url[1] == 'module') && ($url[2] == $module) && (isset($url[3])) && in_array($url[3], $typeArray)) {
                return 'class="active"';
            }
            if (($url[1] == 'module') && ($url[2] == $module) && ($typeArray[0] == 'main') && (!isset($url[3]))) {
                return 'class="active"';
            }
        }
    }

    protected function checkActive($matches)
    {
        global $core;
        $content = $matches[1];

        $url = $this->getUrl();
        if (isset($url[1])) {
            if ($url[1] == 'update') {
                return false;
            }
            if (isset($url[2])) {
                if ($url[2] == 'changelog') {
                    return false;
                }
            }
            if ($url[1] == 'module') {
                if ($core->checkModule($url[2]) == '1') {
                    return stripslashes($content);
                }
            } else {
                return stripslashes($content);
            }
        } else {

            return '<header id="topbar" class="alt" style="padding: 30px 20px">
          <div class="topbar-left pull-left">
            <h1 class="mn" >Корпоративный портал</h1><span class="text-shady-lady fs15">version ' . VERSION_ID . '</span>
          </div>
<div class="topbar-right">
							<a class="btn btn-system" href="' . ADMIN . '/calendar"><span class="mr10 icon fal fa-calendar"></span>Календарь событий</a>
							<a class="btn btn-system" href="javascript:void(0);" onclick="editDashboard();"><span class="icon fal fa-cog"></span></a>
						  </div>
        </header>';
        }
    }
}
