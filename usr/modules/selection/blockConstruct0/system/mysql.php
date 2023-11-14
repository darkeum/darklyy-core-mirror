<?php
class mySql {
    public $queryRow;
	 public $resultBD;
	
	
    public function query($sql){
	 $this->resultBD = new mysqli(DB_LOCAL,DB_USERNAME,DB_PASSWORD,DB_BASE);

        //если произошла ошибка то выводим сообщение
        $this->resultBD->ping();
            
        if (!$this->resultBD)
              throw new Exception('Не удалось подключиться к базе данных.');
        $this->queryRow = mysqli_query($this->resultBD, $sql) or die (mysqli_error('Произошла ошибка'));
        
        $rows = array();
        
        if($this->queryRow and gettype($this->queryRow) != 'boolean'){
            	
				while ($row = mysqli_fetch_assoc($this->queryRow)) {
					$rows[] = $row;
				}
        }else{
            $rows = $this->queryRow;
        }
        return $rows; 
    }   
}
?>