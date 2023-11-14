<?php

class FastBar{
    static function script(){
        return '<script src="/usr/other/sub/fastBar/js/script.js?'.time().'"></script><script src="/usr/other/sub/fastBar/js/mine.js"></script>';
    }

    static function style(){
        return '<link rel="stylesheet" href="/usr/other/sub/fastBar/css/style.css?'.time().'"><link rel="stylesheet" href="/usr/other/sub/fastBar/css/mine.css">';
    }

    static function template($options = []){
        $data_attributes = [];
        foreach ($options as $k => $v) {
            if ($k != 'hotkeys') $data_attributes[] =  "data-{$k}=\"{$v}\"";
        } 
        $data_attributes = implode(" ", $data_attributes);
        $container = $options["container"] ?? "#tov_list";
        $copy_disabled = empty($options["copy_disabled"]) ? "false" : $options["copy_disabled"];

        $hotkeys = $options["hotkeys"] ?? "";
        return <<<HTML
            <div id="fastBar" data-fullvisable="0" {$data_attributes} data-container="{$container}" data-copy_disabled="{$copy_disabled}">
                <nav class="context-menu" id="clipboardMenu">
                    <ul class="context-menu__items">
                        <li class="context-menu__item">
                            <a href="javascript:void(0);" class="context-menu__link" id="context-menu__item-copy">
                                <i class="fa fa-files-o" aria-hidden="true"></i> Копировать
                            </a>
                        </li>
                        <li class="context-menu__item">
                            <a href="javascript:void(0);" class="context-menu__link" id="context-menu__item-paste">
                                <i class="fa fa-edit"></i> Вставить
                            </a>
                        </li>
                    </ul>
                </nav>
                <div class="flexRow">
                    <div class="hotkeys">
                        <div id="slideBar"><i class="fa fa-arrow-up" aria-hidden="true" id="slideBarArrow"></i></div>
                        {$hotkeys}
                    </div>
                </div>
                <div class="flexRow">
                    <input type="text" class="cmd" placeholder="Please enter the command...">
                </div>
                <div class="flexRow">
                    <div class="contentBar"></div>
                </div>
            </div>
HTML;
    }
}