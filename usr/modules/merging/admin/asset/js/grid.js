	function modalCompl(id)
	{
		setTimeout(function () 
		{
			let rowID = $('#jqCorr').jqGrid('getGridParam', 'selrow');
			$('#rowID').val(rowID);
		}, 300);		
		$('#idComplCorr').val(id);	
		$('#modalComplCorr').modal('show');
	}	
	
	function modalRemove(id)
	{			
		setTimeout(function () 
		{
			let rowIDRemove = $('#jqMerging').jqGrid('getGridParam', 'selrow');
			$('#rowIDRemove').val(rowIDRemove);
		}, 300);
		$('#idRemove').val(id);	
		$('#modalRemove').modal('show');
	}
	
$("#formComplCorr").submit(function (e) {
    e.preventDefault();
    var data = $("#formComplCorr").serializeArray();
    $.ajax({
        type: "POST",
        url: "/ajax.php?do=complCorr",
        data: data,
        success: function(response) {
			//Данные отправлены успешно			
			result = jQuery.parseJSON(response);
			if(result["type"] == 'success')
			{
				//$("#jqCorr").trigger("reloadGrid");
				$grid.setRowData(result["data"]["rowID"], {'status':result["data"]["status"], 'date_end':result["data"]["date_end"]});
				
			}
			$('#modalComplCorr').modal('hide');			
			$('#formComplCorr')[0].reset();
			notif(result["type"], result["title"],  result["text"]);
        },
        error: function(response) {
			alert("no");
        }
		
	});
}); 
	
$("#formRemove").submit(function (e) {
    e.preventDefault();
    var data = $("#formRemove").serializeArray();
    $.ajax({
        type: "POST",
        url: "/ajax.php?do=delMerging",
        data: data,
        success: function(response) {
			//Данные отправлены успешно			
			result = jQuery.parseJSON(response);
			if(result["type"] == 'success')
			{
				//$("#jqMerging").trigger("reloadGrid");
				$grid.jqGrid('delRowData',result["data"]["rowID"]);					
			}
			$('#modalRemove').modal('hide');			
			$('#formRemove')[0].reset();
			notif(result["type"], result["title"],  result["text"]);
        },
        error: function(response) {
			alert("no");
        } 
		
	});
}); 

function modalRestore(id)
{			
	setTimeout(function () 
	{
		let rowIDRestore = $('#jqMerging').jqGrid('getGridParam', 'selrow');
		$('#rowIDRestore').val(rowIDRestore);
	}, 300);
	$('#idRestore').val(id);	
	$('#modalRestore').modal('show');
}
	
$("#formRestore").submit(function (e) {
    e.preventDefault();
    var data = $("#formRestore").serializeArray();
    $.ajax({
        type: "POST",
        url: "/ajax.php?do=restoreMerging",
        data: data,
        success: function(response) {
			//Данные отправлены успешно			
			result = jQuery.parseJSON(response);
			if(result["type"] == 'success')
			{
				$grid.jqGrid('delRowData',result["data"]["rowID"]);					
			}
			$('#modalRestore').modal('hide');			
			$('#formRestore')[0].reset();
			notif(result["type"], result["title"],  result["text"]);
        },
        error: function(response) {
			alert("no");
        }
		
	});
}); 
	
	
	