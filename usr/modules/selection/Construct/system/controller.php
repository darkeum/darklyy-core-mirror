<?php
class controller{

   

    
    public function __construct() {
        
        header('Content-Type: text/html; charset=utf-8');
        mb_internal_encoding("UTF-8");
        
       


		$restFilter = new restFilter; 
		
        if($restFilter->filterGet('modul', 'str') == ''){

            require_once(ROOT_CONTRUCTOR.'/modul/constructor.php');
			$constructor = new constructor;
			$constructor->fnConstructor();
 
        }else{

            header('Location: /404.php');

        }
        
    }
    
}

?>