$("#formCorr").submit(function (e) {
    e.preventDefault();
	var data = $("#formCorr").serializeArray();
	const lw = new LW;
	lw.on();
    $.ajax({
        type: "POST",
        url: "/ajax.php?do=addCorr",
        data: data,
        success: function(response) {
			//Данные отправлены успешно			
			result = jQuery.parseJSON(response);
			if(result["type"] == 'success')
			{
				$("#jqCorr").trigger("reloadGrid");				
			}
			$('#modalAddCorr').modal('hide');
			$('#formCorr')[0].reset();
			lw.off();
			notif(result["type"], result["title"],  result["text"]);
        },
        error: function(response) {
			lw.off();
			alert("no");
        }
		
	});
}); 
	
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
			let rowIDRemove = $('#rowIDRemove').jqGrid('getGridParam', 'selrow');
			$('#rowIDRemove').val(rowIDRemove);
		}, 300);
		$('#idRemoveCorr').val(id);	
		$('#modalRemoveCorr').modal('show');
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
		
	});}); 
	
$("#formRemoveCorr").submit(function (e) {
    e.preventDefault();
    var data = $("#formRemoveCorr").serializeArray();
    $.ajax({
        type: "POST",
        url: "/ajax.php?do=delCorr",
        data: data,
        success: function(response) {
			//Данные отправлены успешно			
			result = jQuery.parseJSON(response);
			if(result["type"] == 'success')
			{
				$("#jqCorr").trigger("reloadGrid");
				//$grid.jqGrid('delRowData',result["data"]["rowID"]);				
			}
			$('#modalRemoveCorr').modal('hide');			
			$('#formRemoveCorr')[0].reset();
			notif(result["type"], result["title"],  result["text"]);
        },
        error: function(response) {
			alert("no");
        }
		
	});}); 
	
	
	