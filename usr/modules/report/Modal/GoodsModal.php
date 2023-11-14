<?php

namespace Report\Modal;

class GoodsModal extends Modal{
    static function template(){
        $func_name = static::getFunction();
        $victoria = new \Victoria;
        $victoria->link = '${link}';
        $link = router("admin.report.victoria_add", '@index');
        $func = $victoria->open_func();
        return <<<HTML
          <script>
            function {$func_name}(item, index){
              const link = `{$link}`.replace(`@index`, index);
              {$func}
            }
          </script>
HTML;
    }

    static function getPrefix(){
        return "goods";
    }

    static function getType(){
        return "custom";
    }

    static function getFunction(){
        return "goodsModalOpen";
    }
}