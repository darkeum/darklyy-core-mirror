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

use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;
use Twig\TwigFunction;
use \Boot\Support\Facades\Auth;
use \Boot\Support\Facades\Router;
use \Boot\System\Twig\Enqueue;
use \Boot\System\Categories\Categories;

use \Boot\System\Twig\TokenParsers\ExitTokenParser;

class FrameworkExtension extends AbstractExtension implements GlobalsInterface
{
    protected $theme = '/usr/tpl/dev2';
    protected $lang = 'ru';

    public function getGlobals(): array
    {
        global $db;
        $globals = [];
        $globals['theme'] = $this->theme;
        $globals['manager_theme'] = '/usr/tpl/corp';
        $globals['body_class'] = '';
        $globals['manager'] = '/administration';
        $globals['isUser'] = Auth::isUser();
        $globals['isAdmin'] = Auth::isAdmin();
        $globals['user'] = Auth::user();
        $categories = new Categories;
        $parse = $categories->getModuleCategories('catalog_v2');
        // dd($parse);
        $date_file = date("dmY");
        $file_cache = ROOT . '/tmp/cache/menu_' . $date_file . '.cache';
        if (!file_exists($file_cache)) {
            $menu = get_menu_main($parse, 0, 0, 9, true);
            $files = glob(ROOT . '/tmp/cache/menu_*.cache');
            foreach ($files as $file) {
                if (is_file($file))
                    @unlink($file);
            }
            file_put_contents($file_cache, $menu);
        } else {
            $menu = file_get_contents($file_cache);
        }
        $file_cache_all = ROOT . '/tmp/cache/menu_all_' . $date_file . '.cache';
        if (!file_exists($file_cache_all)) {
            $menu_all = get_menu_main($parse, 0, 0);
            $files = glob(ROOT . '/tmp/cache/menu_all_*.cache');
            foreach ($files as $file) {
                if (is_file($file))
                    unlink($file);
            }
            file_put_contents($file_cache_all, $menu_all);
        } else {
            $menu_all = file_get_contents($file_cache_all);
        }


        // $query = $db->query("SELECT * FROM `" . DB_PREFIX . "_tagcode` WHERE `active` = '1'");
        // if ($db->numRows($query) > 0) {
        //     while ($rows = $db->getRow($query)) {
        //         if ($rows['tag'] && $rows['active'] == 1) {
        //             $tagcode = str_replace(array('\n', '\n', '""', '""', '&lt;', '&gt;'), array('<br>', '<br/>', '\p', '\n', '<', '>'), $rows['desc']);
        //         } else {
        //             $tagcode = '';
        //         }
        //         $globals[$rows['tag']] = $tagcode;
        //     }
        // }
        // dd($globals);
        $globals['menu'] = $menu;
        $globals['menu_all'] = $menu_all;
        return $globals;
    }


    public function getFunctions(): array
    {
        $enqueue = new TwigFunction('enqueue', function ($mode, $key, $type = 'css') {
            $matches = ['1' => $mode, $key, $type];
            echo Enqueue::enqueueFile($matches);
        });

        $enqueue = new TwigFunction('env', function ($key, $default = null) {

            echo env($key, $default);
        });

        $router = new TwigFunction('router', function ($name, $params = null) {
            if(is_string($params)) {
                $params = ['id' => $params];
            }
            // dump($name, $params);
            echo route($name, $params);
        });

        $static = new TwigFunction('static', function ($name, $params = null) {
            echo call_user_func($name);
        });

        $currentRouter = new TwigFunction('currentRouter', function ($name, $isTrue = null) {
            // $nameArr = [];
            // if(!is_array($name)) {
            //     $nameArr[] = $name;
            // } else {
            //     $nameArr = $name;
            // }
            // $currentRouterName = Router::getCurrentName();
            // // dd($currentRouterName);
            // // var_dump( $name);
            // $flag = false;
            // foreach ($nameArr as $value) {
            //    if($currentRouterName == $value) {
            //       $flag = true;
            //       break;
            //    }
            // }
            // if(empty($isTrue))    {
            //     echo $flag;
            // }  else {
            //     if($flag) {
            //        echo $isTrue;
            //     }
            // }
            echo '';

        });

        $lang = new TwigFunction('lang', function ($key) {
            global $core, $lang;
            $lang = array();
            $tpl_now =  str_replace("/usr/tpl/", '', $this->theme);
            // $tpl_now = substr($tpl_now, 0, -1);
            if (file_exists(ROOT . 'langs/' . $this->lang . '/tpl/' . $this->lang . '.' . $tpl_now . '.lng')) {
                include(ROOT . 'langs/' . $this->lang . '/tpl/' . $this->lang . '.' . $tpl_now . '.lng');
            }
            echo isset($lang[$key]) ? $lang[$key] : '';;
        });

        $langConst = new TwigFunction('langConst', function ($key) {
            echo constant($key);
        });


        return array(
            'enqueue' => $enqueue,
            'lang'   => $lang,
            'langConst'   => $langConst,
            'router'   => $router,
            'static'   => $static,
            'currentRouter'   => $currentRouter,
            new \Twig\TwigFunction('die', array($this, 'killRender')),
        );
    }

    /**
     * @param string|null $message
     */
    public function killRender($message = null)
    {
        die($message);
        return '';
    }


    public function getTokenParsers(): array
    {
        return [
            new ExitTokenParser(),
        ];
    }

    /**
     * Возвращает название расширения.
     * @return string
     */
    public function getName()
    {
        return 'framework_extension';
    }
}
