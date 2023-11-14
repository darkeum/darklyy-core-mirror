<?php
class restFilter extends controller{

    public static function filterGet($data,$value){

        if($value == 'number'){

            return intval($_GET[$data]);

        }elseif($value == 'str'){
			
			if(isset($_GET[$data]))
			{
            return addslashes(htmlspecialchars(strip_tags($_GET[$data])));
			}

        }else{

            return addslashes(htmlspecialchars(strip_tags($_GET[$data])));

        }


    }

    
    public static  function filterPost($data,$value){

        if($value == 'number'){

            return intval($_POST[$data]);

        }elseif($value == 'str'){

            return addslashes(htmlspecialchars(strip_tags($_POST[$data])));

        }elseif($value == 'strBr'){

            return str_replace("\n", '<br />',addslashes(htmlspecialchars(strip_tags($_POST[$data]))));

        }else{

            return addslashes(htmlspecialchars(strip_tags($_POST[$data])));

        }

    }

    
    public static function filertStr($data){

        return addslashes(htmlspecialchars(strip_tags($data)));

    }

    
}
?>