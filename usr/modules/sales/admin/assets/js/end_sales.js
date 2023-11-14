async function end_sales_modal(id = false){
    let sales_id = id;
    if(!sales_id){
        sales_id = $("#mainId").val();
    }
	const lw = new LW;
	lw.on();
	const form = new FormData;
	form.append("sales_id", sales_id);
	const middleware = await middleware_end_sales(form);
	if(middleware.status){
		lw.off();
		const end_sales_modal_status_text = $("#end_sales_modal_status_text");
		const end = $("#end");
		check_all_realization_and_transfer(sales_id).then(() => {
			end_sales_modal_status_text.html("успешно");
			$("#end_sales_modal_textarea").removeAttr("required");
			end.val(1);
		}, () => {
			end_sales_modal_status_text.html("не успешно");
			$("#end_sales_modal_textarea").prop("required", true);
			end.val(2);
		}).then(() => {
			$("#end_id").val(sales_id);
			$("#end_sales_modal").modal("show");
		});
	} else {
		lw.off();
		swal("Внимание", `Прежде чем закрыть продажу: ${middleware.error[0]}`, "warning");
	}
}

async function middleware_end_sales(form){
	return await ( await fetch(`/administration/module/sales/middleware_end_sales`, {
		method: "POST",
		body: form,
	}) ).json();
}

function end_sales_modal2(){
	event.preventDefault();
	const data = $("#end_sales_modal_form").serialize();
	end_sales(data).then(() => {
		updateBody();
		updateBtn();
	});
	$("#end_sales_modal").modal("hide");
	$("#end_sales_con").remove();
	swal("Успешно", "Продажа закрыта!", "success");
}

function end_sales_modal3(){
    event.preventDefault(); 
	const data = $("#end_sales_modal_form").serialize();
	end_sales(data).then(() => {
		$('#jqSales').trigger('reloadGrid');
    });
    $("#end_sales_modal").modal("hide");
	swal("Успешно", "Продажа закрыта!", "success");
}

function end_sales(data){
	return new Promise((resolve, reject) => {
		$.post("/ajax.php?do=end_sales", data, function(){
			resolve();
		});
	});
}

function check_all_realization_and_transfer(id){
	return new Promise((resolve, reject) => {
		$.post("/ajax.php?do=check_all_realization_and_transfer", {id}, function(data){
			if(data === ""){
				resolve();
			} else {
				reject();
			}
		});
	});
}

function start_sales_modal(request){
	swal({
		title: "Вы уверены?",
		text: "Возродить продажу",
		icon: "info",
		buttons: true,
		dangerMode: true,
	  })
	  .then((revive) => {
		if (revive) {
			const lw = new LW;
			lw.on();
			const data = {
				id: request.id, end: 0
			}
			end_sales(data).then(() => {
				switch(request.mod){
					case "grid":
						$('#jqSales').trigger('reloadGrid');
					break;
					case "detail":
						updateBtn();
					break;
				}
				
				lw.off();
				swal("Успешно", "Продажа возрождена!", "success");
			});
		}
	  });
}