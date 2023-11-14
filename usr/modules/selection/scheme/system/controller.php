<?php
class controller{

    public $resultBD;

    
    public function __construct() {
        
        header('Content-Type: text/html; charset=utf-8');
        mb_internal_encoding("UTF-8");
        
        if(restFilter::filterGet('modul', 'str') == ''){
        
            require_once(ROOT_SCHEME.'modul/service.php');
            service::fnService();
 
        }else{

            header('Location: /404.php');

        }
        
    }
    
}

?>