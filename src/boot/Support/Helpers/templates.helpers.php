<?php

#
if (!function_exists('display')) {
    function display($source, $parametr = [], bool $tpl = false)
    {
        $twig = app()->make('twig');

        // if ($twig instanceof  \Twig\Environment) {
        $sourceComplete = $source;
        if ($tpl) {
            $sourceComplete = ROOT.'usr/modules/'.$source;
        }
        $twig->display($sourceComplete, $parametr);
        // }
    }
}

#
