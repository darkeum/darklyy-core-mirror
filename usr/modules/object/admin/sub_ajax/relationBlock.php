<?php
    function relationBlock($id = null, $task = false)
	{
		global $adminTpl, $config, $core, $admin_conf, $db, $lang;
		if(isset($id))
		{
			
				
		echo '<div class="panel-heading" ><span class="panel-icon"><i class="fa fa-trophy"></i></span><span class="panel-title"> Связи по объекту</span></div>
							<div class="panel-body">
								
							
		';	
		
		
		
		global $recursiveArray;
		$recursiveArray = [];
		recursiveOutput(0, $id, 'object');
		
		recursiveOutputGet();
		
		
		
		
		

		echo' 
		
		</div>
		
		';
			
		}
	}
	