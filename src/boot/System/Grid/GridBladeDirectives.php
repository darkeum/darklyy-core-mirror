<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2022 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Boot\System\Grid;

use Boot\System\Grid\Controllers\GridAssets;
use Illuminate\Support\Str;

class GridBladeDirectives
{
    /**
     * Действие при обнаружение указанной директивы Blade
     *
     * @param  mixed  $expression
     * @return void
     */
    public static function grid($expression)
    {
        $cachedKey = "'".Str::random(7)."'";
        $pattern = "/,\s*?key\(([\s\S]*)\)/";
        $expression = preg_replace_callback($pattern, function ($match) use (&$cachedKey) {
            $cachedKey = trim($match[1]) ?: $cachedKey;
            return "";
        }, $expression);
        return "<?php echo app('grid')->mount($expression); ?>";
    }

    /**
     * Действие при обнаружение указанной директивы BladeAssets
     *
     * @param  mixed  $asset
     * @return string
     */
    public static function gridAssets($asset)
    {
        return GridAssets::source($asset);
    }
}
