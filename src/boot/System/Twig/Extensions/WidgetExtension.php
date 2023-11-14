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
use Twig\TwigFunction;
use \Boot\System\Categories\Categories;
use \Boot\Support\Facades\Url;
use \Boot\Support\Cache;

class WidgetExtension extends AbstractExtension
{
    /** @var \Twig_Environment */
    private $twig;

    /**
     * @param \Twig_Environment $twig
     */
    // public function __construct(\Twig\Environment $twig)
    // {
    //     $this->twig = $twig;
    // }

    /**
     * @return array
     */
    public function getFunctions(): array
    {
        $calendar = new TwigFunction('calendar', [$this, 'renderCalendar'], ['is_safe' => ['html']]);
        $categories = new TwigFunction('categories', [$this, 'renderCategories'], ['is_safe' => ['html']]);
        return array(
            'calendar' => $calendar,
            'categories'   => $categories
        );
    }

    /**
     * @return string
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function renderCalendar($module): string
    {
    //    $calContent = Cache::get('calendar_block_ru');
    //     if (empty($calContent)) {
    //         if (!function_exists('calendar')) {
    //             require ROOT . 'usr/plugins/calendar.plugin.php';
    //         }
    //         $calContent = calendar('', '');
    //         Cache::set('calendar_block_ru', $calContent, 12000);
    //     }
        return '$calContent';
    }

    public function renderCategories($module, $all = 'Все новости и статьи'): string
    {    
        $categories = new Categories;
        // Url::current();
        $cat = $categories->getModuleCategories($module);
        $category = '';
        $category .= '<ul><li class="article '.''.'"><a href="/' . $module . '/">'.$all.'</a></li>';
        if (!empty($cat)){
            foreach($cat as $info)
            {
                $category .= '<li class="article '. ''.'"><a id="cat_'.$info['id'].'" href="/' . $module . '/' . $info['altname'] . '">' . $info['name'] . '</a></li>';           
            }
        }
        $category .= '</ul>';
        return $category;
    }
}
