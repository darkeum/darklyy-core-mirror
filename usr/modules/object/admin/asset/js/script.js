
	function loadObjectPanel(id, type){
        $.post("/ajax.php?do=loadObjectPanel", {id:id, type:type}, function(data){
            $("#objectPanel").html(data);
			
        });
		if(type == "economyBlock") 
		{
			setTimeout(function() {economyBlockList(id, '');}, 500);
		}		
    }
	
	function economyBlockList(id, type){
		var arr = []; 		
		if ($("#salesEconomy").is(':checked'))
		{
			if (type != 'salesEconomy')
			{
				arr.push('salesEconomy');
			}
		}		
		if ($("#realizationEconomy").is(':checked'))
		{
			if (type != 'realizationEconomy')
			{
				arr.push('realizationEconomy');
			}
		}
		if ($("#receiptsEconomy").is(':checked'))
		{
			if (type != 'receiptsEconomy')
			{
				arr.push('receiptsEconomy');
			}
		}
		if ($("#transferEconomy").is(':checked'))
		{
			if (type != 'transferEconomy')
			{
				arr.push('transferEconomy');
			}
		}
		
		if ($("#"+type).is(':checked'))
		{
			if (type != type)
			{
				arr.push(type);
			}
		}
		else
		{			
			arr.push(type);			
		}
		//alert(arr);
        $.post("/ajax.php?do=economyBlockList", {id:id, type:JSON.stringify(arr)}, function(data){
            $("#economyBlockList").html(data);
		
        });		
		setTimeout(calcEconomySum, 500);

    }
	function calcEconomySum(){
	
		var $economySum = $('#economySum');
		var $rowTotal = $('#rowTotal');
		var sum = 0;

		$('.rowTotal').each(function(){
    sum += parseFloat($(this).text());  // Or this.innerHTML, this.innerText
});

		$economySum.text(sum);
	}
	
