window.onbeforeunload = function() {
	if($(".dataSave").length > 0){
		return "Уйти?";
	}
};

$(async function(){
	let id = $(".title").data("id");
	$(".title").editable({
		type: 'text',
		pk: id,
		url: '/ajax.php?do=changeTitleSales',
		emptytext: 'Отсутствует',
		title: 'Введите наименование',
	});

	start_load();

	// updateBody();

	$priceTypeSelect.link = "/ajax.php?do=getPriceTypeSelectDataSales";
	$priceTypeSelect.set_price = function(data){
		const id = data.id;
		const link = get_link(id);
		input_price_type(id, data.type);
		if(data.type != 4){
			link.s_price.html(priceSet(data.data.price));
		}
		changeTotalId(id);
		changeNdsId(id);
		changePriceOnceDiscount(id);
	}

	fastTaskInit();
});

//смена наименования

function get_link(id){
	const elem = $("#tr_"+id);
	const result = {};

	result.item = elem;
	result.s_price = elem.find('.price');
	result.price_once_discount = elem.find('.price_once_discount');
	result.count = elem.find('.count');
	result.discount = elem.find('.discount');
	result.discount_view = elem.find('.discount');
	result.discount_icon = elem.find('.discount_icon');
	result.nds = elem.find('.nds');
	result.type_nds = elem.find('.typeNds');
	result.total = elem.find('.total');
	result.nds_includes = $("#includesNds");
	result.price_type = elem.find('.priceTypeSelect');
	result.position = elem.find('.position');
	result.count_assembly = elem.find('.count_assembly');
	result.s_name = elem.find('.s_name');
	result.reserv = elem.find('.reserv');

	return result;
}

function get_data(id){
	
	const result = {};

	const link = get_link(id);

	result.id = id;
	result.s_price = Price.string_to_number(link.s_price.text());
	result.price_once_discount = Price.string_to_number(link.price_once_discount.text());
	result.count = Price.string_to_number(link.count.text());
	result.discount = Price.string_to_number(link.discount.data('discount'));
	result.discount_view = Price.string_to_number(link.discount_view.text());
	result.nds = Price.string_to_number(link.nds.text());
	result.type_nds = Price.string_to_number(link.type_nds.text());
	result.total = Price.string_to_number(link.total.text());
	result.nds_includes = link.nds_includes.prop("checked") ? 1 : 0;
	result.price_type = Price.string_to_number(link.price_type.val());
	result.position = Price.string_to_number(link.position.text());
	result.cid = Price.string_to_number(link.position.data("cid"));
	result.s_name = link.s_name.text();

	return result;
}

function get_data_price(id){
	const data = get_data(id);
	const price = new Price;
	return price.get_all({
		price: data.s_price, 
		count: data.count, 
		discount: data.discount, 
		nds_includes: data.nds_includes, 
		nds_proc: data.type_nds,
	});
}

function redZeroSum(){
	$(".total").each((i, elem) => {
		let total = +$(elem).html().replace(" ", "").replace("руб.", "");
		if(total === 0){
			$(elem).addClass("zero-sum");
		} else {
			$(elem).removeClass("zero-sum");
		}
	});
	$(".purchasePrice").each((i, elem) => {
		let purchasePrice = +$(elem).html().replace(" ", "").replace("руб.", "");
		if(purchasePrice === 0){
			$(elem).addClass("zero-sum");
		} else {
			$(elem).removeClass("zero-sum");
		}
	});
}

function allNds(){
	let allNds = 0;
	$(".sales_item").each((i, elem) => {
		const id = $(elem).data("id");
		const data = get_data(id);
		allNds += data.nds;
	});
	$("#nds_total").html(priceSet(allNds));
}
	
	function allSumPurchasePrice(){
		let total = 0;
		let allPurchasePrice = $(".purchasePrice");
		let allCount = $(".count");
        for(let i=0;i<allPurchasePrice.length;i++){
			let localPrice = +$(allPurchasePrice[i]).text().replace(/ /g, "").replace("руб.", "");
			let count = +$(allCount[i]).text().replace(/ /g, "");
			total += localPrice * count;
        }
		total.toFixed(2);
		$(".allSumPrice").html(priceSet(total));
    }
    
    
	function changeTotal(parent){
		const id = $(parent).children(0).data("mainid");
		changeTotalId(id);
	}

	function changeTotalId(id){
		const link = get_link(id);
		const data = get_data_price(id);
		link.total.html(Price.priceSet(data.total));
		$("#total_"+id).remove();
		$("#tablesForm").append('<input type="hidden" class="totalSave dataSave" id="total_'+id+'" value="'+data.total+'">');

		$("#price_"+id).remove();
		$("#tablesForm").append('<input type="hidden" class="priceSave dataSave" id="price_'+id+'" value="'+data.price+'">');
        
		AllSum();
	}
	
	function AllSum(){
		let allSumTotal = $(".total");
		let allSum = 0;
		for(let i=0;i<allSumTotal.length;i++){
			allSum += +$(allSumTotal[i]).html().replace(" руб.", "").replace(/\s+/g, "");
		}
		allSum = allSum.toFixed(2)
		$(".allSum").html(priceSet(allSum));
		redZeroSum();
		allNds();
    }
    
    function changeNds(parent){
		const id = $(parent).children(".position").data("mainid");
		return changeNdsId(id);
	}

	function changeNdsId(id){
		const link = get_link(id);
		const data = get_data_price(id);
		link.nds.html(Price.priceSet(data.nds));
		$("#nds_"+id).remove();
		$("#tablesForm").append(`<input type="hidden" class="ndsSave dataSave" id="nds_${id}" value="${data.nds}">`);
		allNds();
		return data.nds;
	}
	
	async function updateBody(){
		const mainId = $("#mainId").val();
		const lw = new LW;
		lw.on();
		try{
			const data = await $.post("/administration/module/sales/ajax/order_tovar_list/"+mainId);
			// console.log(data);
			$("#tov_list").html(data);
			start_load();
		} catch(error){
			swal("Ошибка", "Много позиций для отображения", "error");
		}
		lw.off();
	}

	async function load_max_discount(){
		const mainId = $("#mainId").val();
		const json = await $.post("/administration/module/sales/load_max_discount/"+mainId);
		const data = JSON.parse(json);
		data.forEach((value, index, array) => {
			const link = get_link(value.id);
			link.s_price.attr("title", `Максимально допустимая скидка: ${value.max_allowable_discount}`);
		});
	}

	function start_load(){
		clearSave();
		redZeroSum();
		allNds();
		x_local_date_delivery();
		load_max_discount();
		load_count_assembly();
		// get_checkbox_reserv();

		// if(Cookies.get('purchase_price') == undefined){
		// 	get_checkbox_purchase_date();
		// }
	} 

	async function load_count_assembly(){
		const mainId = $("#mainId").val();
		const json = await $.post("/administration/module/sales/get_count_assembly/"+mainId);
		const data = JSON.parse(json);
		data.forEach(item => {
			const link = get_link(item.id);
			link.count_assembly.html(item.count);
		});
	}

	function updateHead(){
		return new Promise(function(resolve, reject){
			let mainId = $("#mainId").val();
			$.post("/administration/module/sales/ajax/order_data/"+mainId, function(data){
				$("#orderData").html(data);
				resolve();
			});
		});
	}

	function updateBtn(){
		return new Promise(function(resolve, reject){
			let mainId = $("#mainId").val();
			$.post("/administration/module/sales/ajax/order_btn/"+mainId, function(data){
				$("#ajaxLoadBtn").html(data);
				resolve();
			});
		});
	}

	function updateTableHead(){
		return new Promise(function(resolve, reject){
			let mainId = $("#mainId").val();
			$.post("/administration/module/sales/ajax/order_table_head/"+mainId, function(data){
				$("#table_thead").html(data);
				resolve();
			});
		});
	}

	function get_local_date_delivery_source(){
		const source = JSON.parse($("#local_date_delivery").val());
		const new_source = [];
		for(key in source){
			const item = source[ key ];
			new_source.push({value: item.index, text: item.name});
		}
		return new_source;
	}

	function x_local_date_delivery(){
		const source = get_local_date_delivery_source();
		$(".local_date_delivery_span").editable({
			type: 'select',
			url: '/ajax.php?do=changeSalesDetailX',
			title: 'Выберите срок',
			emptytext: 'Не проставлено',
			name: "local_date_delivery",
			source,
		});
	}
	
	$(document).on("click", ".count", function(){
		//console.log("~");
		let save = $(this).html();
		$(this).removeClass("count");
		$(this).addClass("changeCount");
		$(this).html('<input type="number" style="width:80px;" class="form-control ruberInp" min=1 id="changeCount" value="'+save+'">');
		$("#changeCount").focus();
		$("#changeCount").select();
    });
	
	$(document).on("keydown", "#changeCount, #changePrice, #changeDiscount, #changeTotal, .ruberInp", function(event){
		if(event.keyCode == 13){
			$(this).blur();
        }
    });
	
	$(document).on("blur", "#changeCount", function(){
		let id = $(".changeCount").parent().children(0).data("mainid");
		//console.log(id);
		if(id != undefined){
			let save = $(this).val();
			save = save == 0 ? 1 : save;
			$(".changeCount").html(save);
			$("#count_"+id).remove();
			$("#tablesForm").append('<input type="hidden" class="countSave dataSave" id="count_'+id+'" value="'+save+'">');
			$(".changeCount").addClass("count");
			$(".count").removeClass("changeCount");
			changeTotalId(id);
			changeNdsId(id);
			changePriceOnceDiscount(id);
        }
	});
	
	$(document).on("click", ".total", function(){
		const id = $(this).data("id");
		const data = get_data(id);
		if(data.price_type == 4){
			let save = Number($(this).html().replace(" руб.", "").replace(/\s+/g, ""));
			$(this).removeClass("total");
			$(this).addClass("changeTotal");
			$(this).html('<input type="number" class="form-control ruberInp" min=1 style="width:' +((save.length + 3) * 8)+ 'px;" id="changeTotal" value="'+save+'">');
			$("#changeTotal").focus();
			$("#changeTotal").select();
		} else {
			alert("Для изменения цены, измените тип цены");
		}
	});

	$(document).on("blur", "#changeTotal", function(){
		let id = $(".changeTotal").parent().children(0).data("mainid");
		//console.log(id);
		if(id != undefined){
			let save = $(this).val();
			const data = get_data(id);
			const changeTotal = $(".changeTotal");
			if(data.count == 0 || data.s_price == 0){
				changeTotal.html(priceSet(0));
				$(".changeTotal").addClass("total");
				$(".total").removeClass("changeTotal");
				swal("Ошибка", "Цена или количество ноль!", "error");
				AllSum();
				allNds();
			} else {
				changeTotal.html(priceSet(save));
				$("#total_"+id).remove();
				$("#tablesForm").append('<input type="hidden" class="totalSave dataSave" id="total_'+id+'" value="'+save+'">');
				$(".changeTotal").addClass("total");
				$(".total").removeClass("changeTotal");
				getDiscountForTotal(id);
				allNds();
			}
        }
	});

	function getDiscountForTotal(id){
		const link = get_link(id);
		const data = get_data(id);
		const price = data.s_price;
        const count = data.count;
		const total = data.total;
		const discount = (100*(price * count - total)) / (price * count);

		link.discount.html(`${discount.toFixed(2)}%`);

		saveDiscount(id, discount);
		changeNdsId(id);
	}
	
	$(document).on("click", ".price", function(){
		const id = $(this).data("id");
		const data = get_data(id);
		if(data.price_type == 4){
			let save = Number($(this).html().replace(" руб.", "").replace(/\s+/g, ""));
			$(this).removeClass("price");
			$(this).addClass("changePrice");
			$(this).html('<input type="number" class="form-control ruberInp" min=1 style="width:' +((save.length + 3) * 8)+ 'px;" id="changePrice" value="'+save+'">');
			$("#changePrice").focus();
			$("#changePrice").select();
		} else {
			alert("Для изменения цены, измените тип цены");
        }
	});
	
	$(document).on("keydown", ".ruberInp", function(){
		$(this).width(($(this).val().length + 4)*8);
    });
	
	$(document).on("blur", "#changePrice", function(){
		let id = $(".changePrice").parent().children(0).data("mainid");
		//console.log(id);
		if(id != undefined){
			let save = $(this).val();
			$(".changePrice").html(priceSet(save));
			$("#price_"+id).remove();
			$("#tablesForm").append('<input type="hidden" class="priceSave dataSave" id="price_'+id+'" value="'+save+'">');
			$(".changePrice").addClass("price");
			$(".price").removeClass("changePrice");
			changeTotalId(id);
			changeNdsId(id);
			changePriceOnceDiscount(id);
        }
    });
	
	// $(document).on("change", ".priceTypeSelect", function(){
	// 	changePriceType($(this));
	// });
	
	function changePriceType(myThis){
		const id = myThis.data("id");
		changePriceTypeId(id);
	}

	function changePriceTypeId(id){
		const link = get_link(id);
		const data = get_data(id);
		const cid = data.cid;

		$priceTypeSelect.init_price(link.price_type[0], {id, cid});

		// const value = data.price_type;

		// input_price_type(id, value);

		// if(value != 4){
		// 	$("#price_"+id).remove();
		// 	$.post("ajax.php?do=get_price_item", {cid, price_type: value, id}, function(new_data){
		// 		link.s_price.html(priceSet(new_data));
		// 		changeTotalId(id);
		// 		changeNdsId(id);
		// 		changePriceOnceDiscount(id);
        //     });
		// } else {
		// 	changeTotalId(id);
		// 	changeNdsId(id);
		// 	changePriceOnceDiscount(id);
		// }
	}

	function input_price_type(id, value){
		$("#priceType_"+id).remove();
		$("#tablesForm").append(`<input type="hidden" class="priceTypeSave dataSave" id="priceType_${id}" value="${value}">`);
	}
	
	$(document).on("click", ".discount", function(){
		//console.log("~");
		let save = $(this).html().replace("%", "");
		$(this).removeClass("discount");
		$(this).addClass("changeDiscount");
		$(this).html('<input type="number" style="width:80px;" class="form-control" min=1 max=100 id="changeDiscount" value="'+save+'">');
		$("#changeDiscount").focus();
		$("#changeDiscount").select();
    });
	
	$(document).on("blur", "#changeDiscount", function(){
		const id = $(this).parent().data("id");
		if(id != undefined){
			let save = $(this).val();
			if(save === ""){
				save = 0;
			}
			$(".changeDiscount").html(save+"%");
			// $(".changeDiscount").data("discount", save);
			$(".changeDiscount").addClass("discount");
			$(".discount").removeClass("changeDiscount");

			saveDiscount(id, save);
			changeTotalId(id);
			changeNdsId(id);
			changePriceOnceDiscount(id);
        } else {
			throw {message: `id is undefined`};
		}
	});

	function changePriceOnceDiscount(id){
		const link = get_link(id);
		const data = get_data(id);

		const value = data.total == 0 || data.count == 0 ? 0 : data.total / data.count;
		link.price_once_discount.html(Price.priceSet( value ));
	}
	
	function saveDiscount(id, value){
		$("#discount_"+id).remove();
		$("#tablesForm").append('<input type="hidden" class="discountSave dataSave" id="discount_'+id+'" value="'+value+'">');
		$("#tr_"+id).find('.discount').data('discount', value);
		//changeTotal(parent);
	}
	
	$(document).on("click", ".saveChange", async function(){
		$(this).prop("disabled", true);
		await saveAll();
		$(this).prop("disabled", false);
    });
	
	async function saveAll(){
		let saveId = $("#saveId").val();
        
		let count = $(".countSave");
		let typePrice = $(".priceTypeSave");
		let price = $(".priceSave");
		let discount = $(".discountSave");
		let total = $(".totalSave");
		let nds = $(".ndsSave");
	
		let mainId = $("#mainId").val();
	
		const json = {};
		
		for(let i=0;i<total.length;i++){
			json[$(total[i]).attr("id").slice(6)] = {};
			json[$(total[i]).attr("id").slice(6)]["total"] = $(total[i]).val();
		}
		for(let i=0;i<count.length;i++){
			json[$(count[i]).attr("id").slice(6)]["count"] = $(count[i]).val();
		}
		for(let i=0;i<typePrice.length;i++){
			json[$(typePrice[i]).attr("id").slice(10)]["typePrice"] = $(typePrice[i]).val();
		}
		for(let i=0;i<price.length;i++){
			json[$(price[i]).attr("id").slice(6)]["price"] = $(price[i]).val();
		}
		for(let i=0;i<discount.length;i++){
			json[$(discount[i]).attr("id").slice(9)]["discount"] = $(discount[i]).val();
		}
		for(let i=0;i<nds.length;i++){
			json[$(nds[i]).attr("id").slice(4)]["nds"] = $(nds[i]).val();
		}
		//console.log(json);
		let json_new = JSON.stringify(json);
		let result_json;
		const flag_lw = saveId != "";
		const lw = new LW;
		try {
			if(flag_lw) lw.on();
			result_json = await $.post("ajax.php?do=save_change_sales", {json: json_new, saveId: saveId});
			const result = JSON.parse(result_json);
			if(result.error == 0){
				notif("success", "Успешно", "Товар успешно обновлены");
				clearSave();
			} else {
				// notif("error", "Ошибка", "Товар не обновлены");
				swal("Ошибка", result_json, "error");
			}
			if(flag_lw) await updateBody();
		} catch (error) {
			swal("Ошибка", result_json, "error");
			// notif("error", "Ошибка", `Товар не обновлены ${error}`);
		} finally{
			if(flag_lw) lw.off();
		}
		
		$("body").css("overflow-y", "scroll");
	}

	function clearSave(){
		$(".countSave").remove();
		$(".priceTypeSave").remove();
		$(".priceSave").remove();
		$(".discountSave").remove();
		$(".totalSave").remove();
		$(".ndsSave").remove();
		$("#saveId").val("");
	}

	function check_assembly(id){
		$.post("/ajax.php?do=check_assembly_sales", {id}, function(data){
			var con = document.createElement("div");
			con.innerHTML = `Данные товары были отправлены на сборку и изменены: ${data} Вы хотите синхронизировать данные товары со складом?`;
			if(data !== ""){
				swal({
					title: "Внимание!",
					  content: con,
					  icon: "warning",
					buttons : {
						sync: {
							text: "Синхронизировать",
							value: true,
							visible: true,
							className: "",
							closeModal: true,
						},
						cancel: {
							text: "Закрыть",
							value: false,
							visible: true,
							className: "",
							closeModal: true,
						},
					}
				}).then(sync => {
					if(sync){
						send_sync_assembly(id);
					}
				});
			}
		});
	}

	function send_sync_assembly(id){
		$.post("/ajax.php?do=sync_assembly_sales", {id}, function(){
			swal("Успешно!", "Данные успешно синхронизированы", "success");
		});
	}
	
	
	
	//****************ChangeGoods****************
	
	let saveValue = "";
	$(document).on("dblclick", ".changeGoods", async function(){
		const id = $(this).parent().children(0).data("mainid");
		const lw = new LW;
		lw.on();
		const ccd = await check_can_delete(id);
		lw.off();
		if(ccd.status){
			change_goods_open(id);
		} else {
			error_modal_show_move(ccd.data);
		}
	});
	
	function check_realization_active(id = null, sales_id = null){
		return new Promise((resolve, reject) => {
			$.post("/ajax.php?do=check_realization_active", {id, sales_id}, function(error_id){
				if(error_id === ""){
					resolve();
				} else {
					reject(error_id);
				}
			});
		});
	}
	
	function error_modal_show(error_id){
		error_modal_show_move({
			error: "Требуется распроведение реализации!",
			link: `/administration/module/realization/order/${error_id}`,
		});
	}

	function error_modal_show_move(data){
		swal({
			title: "Внимание!",
			  text: data.error,
			  icon: "warning",
			buttons : {
				go: {
					text: "Перейти",
					value: true,
					visible: true,
					className: "",
					closeModal: true,
				},
				cancel: {
					text: "Закрыть",
					value: false,
					visible: true,
					className: "",
					closeModal: true,
				},
			}
		}).then((go) => {
			if(go){
				window.open(data.link);
			}
		});
	}

	let flagList = true;
	function getSearchList(){
		$.ajax({
			url: "ajax.php?do=getSearchListSales",
			type: "POST",
			data: {search: $(".inputChange").val(), counter: $("#counter").val()},
			success: function(data){
				$("#ajaxResult").append(data);
				$("#counter").val(+$("#counter").val() + 50);
				flagList = true;
            }
        });
    }
	
	$(document).on("input cut copy paste click", ".inputChange", function(){
		if(flagList){
			if($(this).val().length > 2){
				flagList = false;
				$("#counter").val(0);
				$("#ajaxResult").html("");
				getSearchList();
				$("#ajaxResultBlock").fadeIn();
				
				if($("#helpDiv").length > 0){
					$("html, body").animate({ scrollTop: $(document).height() }, "slow");
                }
            }
        }
    });
	
	$(document).on("click", ".loadMore", function(){
		$(".loadMore").remove();
		getSearchList();
    });
	
	let flagCheck = true;
	
	$(document).on("mouseleave", "#ajaxResultBlock", function(){
		flagCheck = true;
		$(".inputChange").focus();
    });   
	
	$(document).on("mouseenter", "#ajaxResultBlock", function(){
		flagCheck = false;
    });  
	
	$(document).on("click", ".elemList", function(){
		let id = $(this).attr("id").slice(3);
		let saveParentId = $(this).parent().data("id");
		let saveParent = $("#tr_"+saveParentId);
		let parentId = $(saveParent).children(".position").data("mainid");
		
		let num = $(saveParent).children(".position").html();
		let count = $(saveParent).children(".count").html();
		let discount = $(saveParent).children(".discount").html().replace("%", "");

		getCatProductSales(id, saveParent, parentId, num, count, discount);
	});    
	
	function getCatProductSales(id, saveParent, parentId, num, count, discount){
		$.ajax({
			type: "POST",
			url: "/administration/module/sales/ajax/order_tovar_list_min/"+parentId, 
			data: {num, cid:id, count, discount},
			success: function(data1){
				$(saveParent).html(data1);
				flagCheck = true;
				
				let json = $("#saveId").val();
				if(json.length > 0){
					json = JSON.parse(json);
					} else {
					json = {};
                }
				json[parentId] = id;
				json = JSON.stringify(json);
				$("#saveId").val(json);
				$("#priceType_"+parentId).remove();
				
				$("#helpDiv").remove();
				
				//AllSum();
				allSumPurchasePrice();
				saveDiscount(parentId, 0);
				changeTotal(saveParent);
				changePriceOnceDiscount(id);
				// let myThis = $(saveParent).children().children(".priceType");
				// changePriceType(myThis);
				changePriceTypeId(parentId);
				
            }
        });
	}

	function replace_sales_item(id, cid){
		return new Promise((resolve, reject) => {
			const link = get_link(id);
			const data = get_data(id);
			$.post("/administration/module/sales/ajax/order_tovar_list_min/"+id, 
			{num: data.position, cid, count: data.count, discount: data.discount}, 
			function(item_template){
				link.item.html(item_template);
				const saveId = $("#saveId");
				let json = saveId.val();
				if(json.length > 0){
					json = JSON.parse(json);
				} else {
					json = {};
				}
				json[ id ] = cid;
				json = JSON.stringify(json);
				saveId.val(json);
				saveDiscount(id, 0);
				// changeTotalId(id);
				changePriceTypeId(id);
				resolve();
			});
		});
	}
	
	$(document).on("blur", ".inputChange", function(){
		if(flagCheck){
			let elem = $(this).parent().parent().children(".changeElem");
			$(elem).html(saveValue);
			$(elem).addClass("changeGoods");
			$("#helpDiv").remove();
        } 
    });
    
    //Тип продажи
    $(document).on('click', '#sales_type', async function(){
		const pk = $("#mainId").val();
		const sales_type = $('#sales_type');
		sales_type.editable('destroy');
		const data = await ( await fetch(`/administration/module/sales/check_documents_held/${pk}`) ).json();
		const source = [];
		const source_data = JSON.parse($("#sales_type_source").val());
		source_data.forEach(item => {
			source.push({value: item.value, text: item.text});
		})
		if(data.check){
			sales_type.editable({
				type: 'select',
				pk,
				url: '/ajax.php?do=fastChangeSales',
				ajaxOptions:{
					success: function(){
						updateHead();
						updateBody();
						updateBtn();
						updateTableHead();
					}
				},
				source,
			});
			sales_type.editable('show');
		} else {
			swal("Ошибка", "Прежде чем менять, распроведите документы", "warning")
		}
    });

	function init_xe_source(id){
		$(document).on('click', `#${id}`, function(){
			const source_ = JSON.parse($(`#${id}_source`).val());
			const source = [];
			source_.forEach((value, index, array) => {
				source.push({value: index, text: value});
			});
			$(`#${id}`).editable({
				type: 'select',
				pk: $("#mainId").val(),
				url: '/ajax.php?do=fastChangeSales',
				source,
			});
			$(`#${id}`).editable('show');
		});
	}

    init_xe_source("source_appeal");
    init_xe_source("market_segment");
    init_xe_source("by_field_activity");
    

    //Категория продажи
    $(document).on('click', '#sales_cat', function(){
        $('#sales_cat').editable({
            type: 'select',
            pk: $("#mainId").val(),
            url: '/ajax.php?do=fastChangeSales',
            ajaxOptions:{
                success: function(){
                    let id = $("#mainId").val();
                    $.post("/administration/module/sales/ajax/order_data/"+id, function(data){
                        $("#orderData").html(data);
                    });
                }
            },
            source: [
                {value: 1, text:'Действующий контрагент'},
                {value: 2, text:'Новый контрагент'},
                {value: 3, text:'Сделка сопровождения'},
            ]
        });
        $('#sales_cat').editable('show');
    });
    
    //Организация
    function getSourceOrg() {
        const url = "/ajax.php?do=getOrgListSales";
        return $.ajax({
            type: 'POST',
            url: url,
        });
    }
    
    $(document).on('click', '#org', function(){
		const id = $("#mainId").val();
		check_realization_active(null, id).then(() => {
			getSourceOrg().done(function(result){
				$('#org').editable({
					type: 'select',
					pk: $("#mainId").val(),
					url: '/ajax.php?do=fastChangeSales',
					ajaxOptions:{
						success: function(){
							const lw = new LW;
							lw.on();
							const job = [updateHead(), updateTableHead(), updateBody()];
							Promise.all(job).then(() => {
								lw.off();
							});
						}
					},
					source: result,
				});
				$('#org').editable('show');
			});
		}, (error_id) => {
			error_modal_show(error_id);
		});
	});

	function getSourceStock(id){
		const url = "/ajax.php?do=getStockSales";
        return $.ajax({
			type: 'POST',
			data: {id},
			url,
        });
	}

    $(document).on('click', '#stock_id', function(){
		const id = $("#mainId").val();
		check_realization_active(null, id).then(() => {
			getSourceStock(id).done(function(result){
				$('#stock_id').editable({
					type: 'select',
					pk: id,
					url: '/ajax.php?do=fastChangeSales',
					ajaxOptions:{
						success: function(){
							updateHead();
							updateBody();
						}
					},
					source: result,
				});
				$('#stock_id').editable('show');
			});
		}, (error_id) => {
			error_modal_show(error_id);
		});
	});

	function get_stock_id_view(){
		return "stock_id_view";
	}

    $(document).on('click', '#stock_id_view', function(){
		const id = $("#mainId").val();
		getSourceStock().done(function(result){
			const stock_id_view = $('#stock_id_view');
			const name = get_stock_id_view();
			stock_id_view.editable({
				type: 'select',
				source: result,
			});
			stock_id_view.editable('show');
			stock_id_view.on("save", function(e, params){
				Cookies.set(name, params.newValue);
				const lw = new LW;
				lw.on();
				Promise.all([updateHead(),updateBody()]).then(() => {
					lw.off();
				});
			});
		});
	});
	
	//удаление 
	$(document).on("click", "#modalFDId", function(){
		const id = $(this).data("id");
		const sales_id = $("#mainId").val();
		$.post("/administration/module/sales/ajax/order_divece_del", {id, sales_id}, function(){
			updateBody();
		});
	});


//резервация
function reservModal(This){
	//console.log("~");
	$("#reservModal").modal("show");
	let pid = $(This).data("pid");
	let cid = $(This).data("cid");
	$("#reservBtn").data("pid", pid);
	$("#reservBtn").data("cid", cid);
	let count = $(This).parent().parent().parent().parent().parent().children(".count").html();
	$("#countReserv").val(count);
	$("#countReserv").select();
	$("#countReserv").focus();
}

$(document).on("click", "#reservBtn", function(){
	let pid = $(this).data("pid");
	let cid = $(this).data("cid");
	let count = $("#countReserv").val();
	let mainId = $("#mainId").val();
	$.post("/ajax.php?do=setReserv", {pid:pid, cid:cid, count:count, module: "sales", stock_id:"1"}, function(res){
		updateBody();
		switch(res){
			case "1":
				notif("success", "Успешно", "Успешно добавлено в резерв");
			break;
			case "2":
				notif("danger", "Ошибка", "Количество необходимого товара превышен!");
			break;
		}
		
	});
});

//Список резервов
function reservListModal(This){
	let pid = $(This).data("pid");
	let uid = $("#mainUid").val();
	$.post("/ajax.php?do=getReservList", {pid:pid, module:"sales", stock_id:"1", uid: uid}, function(data){
		$("#reservList").html(data);
		$("#reservListModal").modal("show");
	});
}

$(document).on("click", "#reservDelBtn", function(){
	let checkbox = $("#reservList").find("input:checked");
	let id = [];
	let mainId = $("#mainId").val();
	for(let i=0;i<checkbox.length;i++){
		id.push($(checkbox[i]).data("id"));
	}
	$.post("/ajax.php?do=delReserv", {id:id}, function(){
		updateBody();
	})
});
/*
$(document).on("click", ".ndr", function(){
	let cid = $(this).data("cid");
	$.post("/ajax.php?do=getReservAllList", {cid: cid}, function(data){
		$("#reservListAll").html(data);
		$("#reservListAllModal").modal("show");
	});
});
*/
function allowShipmentModal(){
	$("#allowShipmentModal").modal("show");
}

function allowShipmentModal2(){
	$("#allowShipmentModal2").modal("show");
}

$(document).on("click", "#allowShipmentBtn", function(){
	let mainId = $("#mainId").val();
	$(".allowShipmentHide").toggle();
	$.post("/ajax.php?do=setAllowShipmenSales", {id:mainId}, function(){
		
	});
});

//модалка с комментарием
function salesCommentModal(This){
	$("#files1").html('<li class="text-muted text-center empty">No files uploaded.</li>');
	$("#debug1").html("");
	let id = $(This).data("id");
	$("#changeCommentSales").data("id", id);
	const lw = new LW;
	lw.on();
	$.post("/ajax.php?do=getCommentSalesDetail", {id}, function(json){
		const data = JSON.parse(json);
		$("#sales_text_comment").val(data.comment);
		const sales_comment_files = $("#sales_comment_files");
		sales_comment_files.html(``);
		data.files.forEach(file => {
			sales_comment_files.append(`<div><a href="/download/${file.id}">${file.name}</a></div>`);
		})
		$("#sales_comment_modal").modal("show");
		lw.off();
	});
}

function modalViewComment(This){
	let id = $(This).data("id");
	$("#modal-comm-"+id).modal("show");
}

$(document).on("click", "#changeCommentSales", function(){
	let data = $("#comment_form").serialize();
	let id = $(this).data("id");
	let mainId = $("#mainId").val();
	$.post("/administration/module/sales/commSaveDetail/"+id+"/"+mainId, data, function(){
		updateBody();
	});
});

function priceSet(data){
	let price = Number.prototype.toFixed.call(parseFloat(data) || 0, 2),
		//заменяем точку на запятую
		//price_sep = price.replace(/(\D)/g, ","),
		price_sep = price;
		//добавляем пробел как разделитель в целых
		price_sep = price_sep.replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1 ");

	return price_sep + ' руб.';
}

function purchasePriceChange(This){
	let step = $(This).data("step");
	step++;
	if(step == 4){
		step = 1;
	}
	$(This).data("step", step);
	let count = $("#tov_list").find(".count");
	let purchasePrice = $("#tov_list").find(".purchasePrice");
	let localTotal = $("#tov_list").find(".total");
	let total = 0;
	
	switch(step){
		case 1:
			$(This).children().eq(0).html("Цена закупки");
			let save = localStorage.getItem("savePurchasePrice");
			save = JSON.parse(save);
			save.forEach(function(val, i){
				$(purchasePrice[i]).html(priceSet(val));
				let localCount = +$(count[i]).text();
				total += +val * +localCount;
			});
		break;
		case 2:
			$(This).children().eq(0).html("Сумма закупки");
			let json = [];
			$(purchasePrice).each(function(i, elem){
				let val = +$(elem).text().replace(/ /g, "").replace(/	/g, "").replace("руб.", "");
				json.push(val);

				let localCount = +$(count[i]).text();
				let sum = localCount * val;
				$(elem).html(priceSet(sum));
				total += +sum;
			});
			json = JSON.stringify(json);
			localStorage.setItem("savePurchasePrice", json);
		break;
		case 3:
			$(This).children().eq(0).html("Разница");
			$(purchasePrice).each(function(i, elem){
				let val =  +$(elem).text().replace(/ /g, "").replace("руб.", "");

				let localTotalOne = +$(localTotal[i]).text().replace(/ /g, "").replace("руб.", "");
				let raz = localTotalOne - val;
				$(elem).html(priceSet(raz));
				total += +raz;
			});
		break;
	}

	$(".allSumPrice").html(priceSet(total));
}

let flagChangePriceProcent = true
$(document).on("focus", "#changePriceProcent", function(){
	if(flagChangePriceProcent){
		$(this).after('<div id="priceProcentView" style="padding: 5px; display:flex; justify-content: space-between; position: absolute; width: '+$(this).outerWidth()+'px; height: '+$(this).height()+'px; background-color: white; border: 1px black solid; border-radius: 0 0 5px 5px;"><div id="priceProcentViewValue"></div><i style="cursor: pointer;" class="fa fa-chevron-up" aria-hidden="true" id="closePriceProcent"></i></div>');
		flagChangePriceProcent = false;
	}
});

$(document).on("click", "#closePriceProcent", function(){
	$("#priceProcentView").remove();
	flagChangePriceProcent = true;
});

$(document).on("keyup input click", "#changePriceProcent", function(){
	let value = $(this).val();
	if(value.length > 0){
		if(!isNaN(value)){
			let sum = +$(".allSumPrice").html().replace(/ /g, "").replace("руб.", "");
			$("#priceProcentViewValue").html(priceSet(sum * value / 100));
		}
	} else {
		$("#priceProcentViewValue").html("");
	}
});

$(document).on("click", "#counterparty", function(){
	let id = $(this).data("id");
	let sales_id = $("#mainId").val();
	$("#counterpartyModalBody").html("Загрузка данных...");
	$("#counterpartyModal").modal("show");
	$.post("/ajax.php?do=getInfoOrgSales", {id:id, sales_id: sales_id}, function(data){
		$("#counterpartyModalBody").html(data);
	});
});

$(document).on("click", ".activeComment", function(){
	let comment = $(this).data("comment");
	$("#commentViewModalBody").html(comment);
	$("#commentViewModal").modal("show");
});

$(document).on("click", ".uoif", function(){
	if(!$(this).hasClass("uoifActive")){
		let id = $(this).data("id");
		let sales_id = $("#mainId").val();
		$(".uoif").removeClass("uoifActive");
		$(".uoif").removeClass("cp");
		$(".uoif").addClass("cp");
		$(this).addClass("uoifActive");
		$(this).removeClass("cp");
		$.post("/ajax.php?do=setUserOrgIdFavorite", {id:id, sales_id:sales_id}, function(){
	
		});
	}
});

$(document).on("click", "#sendMess", function(){
	$("#sendMessModalForm").html("Загрузка данных...");
	$("#sendMessModal").modal("show");
	let id = $("#counterparty").data("id");
	$.post("/ajax.php?do=listUserOrg", {id:id}, function(data){
		$("#sendMessModalForm").html(data);
	});
});

$(document).on("change", ".userListOrg", function(){
	let uid = $(this).data("uid");
	$("#sendMessModalBtn").data("uid", uid);
	let str = '';
	str += '<div class="checkbox"><label><input type="checkbox" value="SMS" id="roleSMS" name="role[]">SMS</label></div>';
	str += '<div class="checkbox"><label><input type="checkbox" value="Email" id="roleEmail" name="role[]">E-mail</label></div>';
	let disabled = 'disabled';
	if($(this).data("viber") == true){
		disabled = '';
	}
	str += '<div class="checkbox"><label><input type="checkbox" value="Viber" id="roleViber" name="role[]" '+disabled+'>Viber</label></div>';
	disabled = 'disabled';
	if($(this).data("vk") == true){
		disabled = '';
	}
	str += '<div class="checkbox"><label><input type="checkbox" value="VK" id="roleVK" name="role[]" '+disabled+'>VK</label></div>';
	$("#sendMessModalForm").html(str);
	$("#sendMessModalBtn").show();
});

$(document).on("click", "#sendMessModalBtn", function(){
	let dataForm = $("#sendMessModalForm").serialize();
	let uid = $(this).data("uid");
	let sales_id = $("#mainId").val();
	$.post("/ajax.php?do=sendAllMessSales", "uid="+uid+"&sales_id="+sales_id+"&"+dataForm, function(){

	});
});

$(document).on("click", ".purchasePriceLocal", function(){
	let value = +$(this).prev(".purchasePrice").text().replace(/ /g, "").replace("руб.", "");
	let id = $(this).parents("tr").data("id");
	$("#priceChangeModalClear").data("id", id);
	$("#priceChangeModalSend").data("id", id);
	$("#priceChangeModalInp").val(value);
	$("#priceChangeModal").modal("show");
});

$(document).on("click", "#priceChangeModalSend, #priceChangeModalClear", function(){
	let id = $(this).data("id");
	let value = $("#priceChangeModalInp").val();
	let mainId = $("#mainId").val();
	let status = +$(this).data("status");
	if(status === 1){
		value = null;
	}
	$.post("/ajax.php?do=priceChangeLocalSales", {id, value}, function(data){
		//updateBody();
		if(data != "") swal("Ошибка!", data, "error");
		change_purchase_price_date(id);
	});
});

async function change_purchase_price_date(id){
	const data = await get_purchase_price_date_sales(id);
	change_view_purchase_price_date(id, data);
}

function change_view_purchase_price_date(id, data){
	let parent = $("#tr_"+id).children();
	parent.children(".purchasePrice").html(priceSet(data.price));
	parent.children(".purchasePrice").css({color: data.color});
	parent.children(".date_purchse_price").html(data.datePrice);
	parent.children(".purchasePrice").prop("onclick", null);
	if(data.flagSup){
		parent.children(".purchasePrice").click(function(){
			window.open(data.fileUrl, '1552240782632','width=700,height=500,toolbar=0,menubar=0,location=0,status=1,scrollbars=1,resizable=1,left=0,top=0');
			return false;
		});
		parent.children(".purchasePrice").removeClass("link-no-style");
		parent.children(".purchasePrice").addClass("link-style");
	} else {
		parent.children(".purchasePrice").addClass("link-no-style");
		parent.children(".purchasePrice").removeClass("link-style");
	}

	parent.find(".purchase_date").html((data.date_post == "" || data.date_post == null) ? "Не проставлено" : data.date_post);
}

function get_purchase_price_date_sales(id){
	return new Promise((resolve, reject) => {
		$.post("/ajax.php?do=get_purchase_price_date_sales_ajax", {id:id}, function(data){
			resolve(JSON.parse(data));
		});
	});
}

$(document).on("click", ".datePostLocal", function(){
	if(false){
		let date = $(this).prev().text();
		if(date === 'Не проставлено'){
			date = "";
		} else {
			date = date.split(".");
			date = date[2]+"-"+date[1]+"-"+date[0];
		}
		let id = $(this).parents("tr").data("id");
		$(".datePostLocalModalBtn").data("id", id);
		$("#datePostLocalModalInp").val(date);
		$("#datePostLocalModal").modal("show");
	}
});

$(document).on("click", ".datePostLocalModalBtn", function(){
	let status = $(this).data("status");
	let id = $(this).data("id");
	let value = $("#datePostLocalModalInp").val();
	if(status === 1){
		value = null;
	}
	let mainId = $("#mainId").val();
	$.post("/ajax.php?do=datePostLocaSales", {id:id, value:value}, function(){
		updateBody();
	});
});

function loadUl(id, array){
	$("#"+id).empty();
	for(let i=0;i<array.length;i++){
		$("#"+id).append('<li>'+array[i]+'</li>');
	}
}

$(document).on("click", "#sub", async function(){
	const lw = new LW;
	let checks = $(".checks:checked");
	const checks_value = [];
	let act = $("#act").val();
	let arrayCheck = [];
	for(let i=0;i<checks.length;i++){
		const value = $(checks[i]).val();
		if(value !== "on"){
			checks_value.push(value);
		}
		let elem = $("#tr_"+value).children(".changeGoods").text();
		if(elem !== ""){
			arrayCheck.push(elem);
		}
	}

	switch(act){
		case "del":
			lw.on();
			const ccd = await check_can_delete(checks_value);
			lw.off();
			if(ccd.status){
				loadUl("masDelModalUl", arrayCheck);
				$("#masDelModal").modal("show");
			} else {
				error_modal_show_move(ccd.data);
			}
		break;
		case "type_price":
			//loadUl("masTypePriceModalUl", arrayCheck);
			$("#masTypePriceModal").modal("show");
		break;
		case "discount":
			$("#masDiscountInp").val("");
			$("#masDiscountModal").modal("show");
		break;
		case "reserv":
			loadUl("masReservModalUl", arrayCheck);
			$("#masReservModal").modal("show");
		break;
		case "reserv_del":
			check_reserv_uid(checks_value).then(() => {
				loadUl("masReservDelModalUl", arrayCheck);
				$("#masReservDelModal").modal("show");
			}, () => {
				swal("Ошибка!", "Выбран не ваш резерв", "error");
			});
		break;
		case "assembly":
			// loadUl("assemblyModalUl", arrayCheck);
			const assembly_ul = $("#assemblyModalUl");
			assembly_ul.html('<li>Загрузка...</li>');
			$.post('/ajax.php?do=get_assemply_list_sales', {checks:checks_value}, function(list){
				assembly_ul.html(list);
			});
			$("#assemblyModal").modal("show");
		break;
		case "update_checked_price":
			swal({
                title: "Вы уверены?",
                text: "Обновление цен у выделенных позиций",
                icon: "info",
                buttons: {
                    confirm: {
                        text: "Выполнить",
                        value: true,
                        visible: true,
                        className: "",
                        closeModal: true
                      },
                    cancel: {
                        text: "Отмена",
                        value: false,
                        visible: true,
                        className: "",
                        closeModal: true,
                    },
                },
              })
              .then((update) => {
                if (update) {
                    update_checked_price();
                }
              });
		break;
		case "discount_oid":
			swal({
                title: "Вы уверены?",
                text: "Проставить скидки по контрагенту",
                icon: "info",
                buttons: {
                    confirm: {
                        text: "Выполнить",
                        value: true,
                        visible: true,
                        className: "",
                        closeModal: true
                      },
                    cancel: {
                        text: "Отмена",
                        value: false,
                        visible: true,
                        className: "",
                        closeModal: true,
                    },
                },
              })
              .then((update) => {
                if (update) {
                    set_discount_items(checks_value);
                }
              });
		break;
	}
	
});

function check_reserv_uid(id){
	return new Promise((resolve, reject) => {
		$.post("/administration/module/sales/check_reserv_uid", {id}, function(json){
			const data = JSON.parse(json);
			if(data.length > 0){
				reject();
			} else {
				resolve();
			}
		});
	});
}

async function update_checked_price(){
	const jobs = [];
    $(".checks:checked").each((i, elem) => {
        const id = $(elem).val();
        if(id != "on"){
			const data = get_data(id);
			const link = get_link(id);
			jobs.push($priceTypeSelect.init_price(link.price_type[0], {id: data.id, cid: data.cid}));
        }
    });
	const lw = new LW;
	lw.on();
	await Promise.all(jobs);
	lw.off();
}

function changeMasTypePrice(){
	let type_price = $("#masTypePriceModalSel").val();
	let array = [];
	$(".checks:checked").each(function(i, elem){
		let id = $(elem).val();
		if(!isNaN(id)){
			const link = get_link(id);
			link.price_type.val(type_price);
			link.price_type.change();
			// $("#tr_"+id).children().children(".priceType").val(type_price);
			// $("#tr_"+id).children().children(".priceType").change();
		}
	});
}

async function set_discount_items(items){
	const lw = new LW;
	lw.on();
	const data = await get_discount_oid(items);
	if(data?.error){
		swal("Ошибка", data.error, "error");
	} else {
		if(data.items){
			data.items.forEach(item => {
				const id = item.id;
				const link = get_link(id);
				const discount = item.discount;
				// if(item.price != null){
				// 	link.s_price.html(priceSet(item.price));
				// }
				if(discount === null){
					link.discount_icon.html(`⚠️`);
					link.discount_icon.prop(`title`, `Скидка не проставлена`);
				} else {
					link.discount_icon.html(``);
					link.discount_icon.prop(`title`, ``);
					link.discount.html(discount+"%");
					saveDiscount(id, discount);
					changeTotalId(id);
					changeNdsId(id);
					changePriceOnceDiscount(id);
				}
			});
		}
	}
	lw.off();
}

async function get_discount_oid(id){
	const sales_id = $("#mainId").val();
	const data = await $.post("/administration/module/sales/get_discount_oid", {sales_id, id});
	try{
		return JSON.parse(data);
	} catch(e){
		return {error: data};
	}
}

function masReserv(){
	let array = [];
	const sales_id = $("#mainId").val();
	$(".checks:checked").each(function(i, elem){
		let id = $(elem).val();
		let count = $("#tr_"+id).children(".count").text();
		let cid = $(elem).data("cid");
		if(!isNaN(id)){
			//{id:id, count:count, cid:cid}
			array.push(id);
		}
	});
	$.post("/ajax.php?do=add_reserv_sales", {id:array, sales_id}, function(){
		updateBody();
	});
}

function masReservDel(){
	let id = [];
	$(".checks:checked").each(function(i, elem){
		let id_l = $(elem).val();
		if(!isNaN(id_l)){
			id.push(id_l);
		}
	});
	const sales_id = $("#mainId").val();
	$.post("/ajax.php?do=del_reserv_sales", {id, sales_id}, function(){
		updateBody();
	});
}

function setMasDiscount(){ 
	const discount = $("#masDiscountInp").val();
	$(".checks:checked").each(function(i, elem){
		const id = $(elem).val();
		if(!isNaN(id)){
			const link = get_link(id);
			link.discount.html(discount+"%");
			saveDiscount(id, discount);
			changeTotalId(id);
			changeNdsId(id);
			changePriceOnceDiscount(id);
		}
	});
}

$(document).on("click", "#masDelModalBtn", function(){
	let checks = $(".checks:checked");
	let array = [];
	for(let i=0;i<checks.length;i++){
		if($(checks[i]).val() !== "on"){
			array.push($(checks[i]).val());
		}
	}
	let mainId = $("#mainId").val();
	$.post("/ajax.php?do=masDelSales", {array:array, sales_id:mainId}, function(){
		updateBody();
	});
});

//реализация
$(document).on("change", ".checks", function(){
	//console.log("~");
	let flag = false;
	$(".checks").each(function(i, elem){
		if($(elem).prop("checked")) flag = true;
	});
	if(flag) {
		$("#myCheck").addClass("holder-active");
		$("#sub").prop("disabled", false);
	} else {
		$("#myCheck").removeClass("holder-active");
		$("#sub").prop("disabled", true);
	}
});

$(document).on("click", "#myCheck", function(){
	let flag = false;
	let flagCheckZeroSum = false;
	$(".checks").each(function(i, elem){
		if($(elem).prop("checked") && $(elem).val() !== "on") flag = true;
	});
	if(flag){
		const msg = $("#tablesForm").serializeArray();
		let dataSave = $(".dataSave");
		if(dataSave.length > 0){
			swal({
				title: "Вы уверены?",
				text: "Изменения не сохранены, вы точно хотите создать реализацию?",
				icon: "warning",
				buttons: {
					cancel:{
						text: "Отмена",
						visible: true,
						className: "salesAlertFont",
						closeModal: true,
						value: null,
					},
					continueSales: {
						text: "Продолжить",
						className: "salesAlertFont",
						value: true,
					},
					saveSales: {
						text: "Сохранить и продолжить",
						className: "salesAlertFont",
						value: "save",
					},
				},
				dangerMode: true,
			  }).then((willDelete) => {
				if (willDelete) {
					if(willDelete === "save"){
						saveAll().then(function(){
							modalCheck(msg);
						});
					} else {
						modalCheck(msg);
					}
				}
			  });
		} else {
			modalCheck(msg);
		}
	}
	else{
		notif("danger", "Предупреждение!", "Выбирите товары");  
	}
});

function modalCheck(msg){
	get_sales_data().then(data => {
		const object = data[0].object;
		if(object == null || object == ""){
			swal("Ошибка!", "Объект не указан!", "error");
		} else {
			const id = [];
			msg.forEach(value => {
				if(value.name == "checks[]"){
					id.push(value.value);
				}
			});
			checkZeroSum({id}).then(() => {
				checkSumMore(msg).then(
					name => {
						if(name == ""){
							$("#checkModal").modal("show");
						} else {
							swal("Внимание!", "Цена товара "+name+" меньше закупочной цены!", "warning").then(() => {
								$("#checkModal").modal("show");
							});
						}
					});
			}, name => {
				swal("Ошибка!", "Сумма товара "+name+" равна 0!", "error");
			});
		}
	});
}

function checkSumMore(msg){
	return new Promise(function(resolve, reject){
		$.post("/ajax.php?do=checkSumMoreSales", msg, function(data){
			resolve(data);
		});
	});
}

$(document).on("click", "#checkBtn", function(){
	let msg = $("#tablesForm").serialize();
	let mainId = $("#mainId").val();
	$.ajax({
		type: "POST",
		url: "/ajax.php?do=confirmRealization/"+mainId,
		data: msg,
		success: function(echoStr) {
			if(echoStr === ""){
				notif("danger", "Ошибка!", "Выбранные товары реализованы");
			} else {
				if(echoStr.length > 50){
					swal("Ошибка!", echoStr, "error");
				} else {
					window.open("/administration/module/realization/order/"+echoStr);
				}
			}
		},
	});
});

$(document).on("click", ".sku-units", function(){
	let type = $(this).data("type");
	if(+type === 1){
		$(this).children().eq(0).html("Ед. изм.");
		$(this).data("type", 2);
	}
	if(+type === 2){
		$(this).children().eq(0).html("Артикул");
		$(this).data("type", 1);
	}
	$(".sku").toggle();
	$(".units").toggle();
});

$(document).on("click", "#date", function(){
	let id = $("#mainId").val();
	$("#date").editable({
		type: 'date',
		pk: id,
		url: '/ajax.php?do=fastChangeSales',
		viewformat: 'dd.mm.yyyy',
		emptytext: 'Отсутствует',
		title: 'Введите дату',
	});
	$("#date").editable("show");
});

$(document).on("click", "#pay", function(){
	$("#payBody").html("Загрузка данных");
	$("#payModal").modal("show");
	let id = $("#mainId").val();
	$.post("/ajax.php?do=getPayDetailSales", {id:id}, function(data){
		$("#payBody").html(data);
	});
});

function checkChecks(){
	let count = 0;
	if($("#all").prop("checked")){
		count++;
	}
	if($(".checks:checked").length - count > 0){
		return true;
	} else {
		return false;
	}
}

function placeOrder(){
	if(checkChecks()){
		$(".placeOrderModal_main").show();
		$(".placeOrderModal_child").hide();
		$("#sendPurchaseSend").html("");
		$("#placeOrderModal").modal("show");
	} else {
		swal("Ошибка!", "Позиции не выбраны!", "error");
	}
}

function main_slide(con, btn = null){
	if(btn === null){
		btn = con+"Btn";
	}
	$(".placeOrderModal_main").slideUp(500, function(){
		$(".placeOrderModal_child").slideUp(500, function(){
			$(con).slideDown(500, function(){
				$(btn).show();
			});
		});
	});
}

function actualizeSupplyModal(){
	main_slide(".actualizeSupply");
}

function sendPurchaseModal(){
	checkReserv();
	get_send_tov().then(elem => {
		$("#sendPurchaseSend").html(elem);
	});
	main_slide(".sendPurchase");
}

function sendPurchaseModal2(){
	checkReserv();
	get_send_tov().then(elem => {
		$("#sendPurchaseSend2").html(elem);
	});
	main_slide(".sendPurchase2");
}

function sendMainCompany(){
	main_slide(".sendMainCompany");
}

function actualizeSupplyNoNotifModal(){
	main_slide(".actualizeSupplyNoNotif");
}

function checkProp(check, id){
	return new Promise(function(resolve, reject){
		$.post("/administration/module/sales/actualize_check/"+id, check, function(data){
			if(data === ""){
				resolve();
			} else {
				let error = "";
				switch(+data){
					default:
						error = "Неизвестная ошибка";
					break;
					case 1:
						error = "Товары не выбраны";
					break;
					case 2:
						error = "Ответственный отсутствует";
					break;
					case 3:
						error = "Объект отсутствует";
					break;
					case 4:
						error = "Организация отсутствует";
					break;
				}
				reject(error);
			}
		});
	});
}

function sendActualizeSupply(){
	sas();
}

function sendActualizeSupplyNoNotif(){
	sas(false);
}

function sas(notif = true){
	const check = $("#tablesForm").serialize();
	const id = $("#mainId").val();
	const result = notif ? check : "notif_no=true&"+check;
	checkProp(check, id).then(function(){
		$.post("/administration/module/sales/actualize_supply/"+id, result, function(data){
			swal("Успешно", "Заявка успешно отправлена", "success");
			updateBody();
		});
	}, function(error){
		swal("Ошибка", error, "error");
	});
}

function checkReserv(){
	$(".checks:checked").each((i, elem) => {
		let value = $(elem).val();
		if(value !== "on" && value !== ""){
			let reserv = $("#tr_"+value).find(".reservNumber").html();
			if(reserv === "0"){
				swal("Внимание!", "Резерв не проставлен!", "warning");
				return false;
			}
		}
	});
}

function get_send_tov(){
	return new Promise((resolve, reject) => {
		const check = $("#tablesForm").serialize();
		$.post("/ajax.php?do=get_send_tov_sales", check, function(data){
			resolve(data);
		})
	});
}

function sendPurchase(data = []){
	const check = $("#tablesForm").serialize();
	const id = $("#mainId").val();
	data.push(check);
	const data_str = data.join("&");
	checkProp(check, id).then(function(){
		$.post("/administration/module/sales/send_order2/"+id, data_str, function(){
			updateBody();
			swal("Успешно", "Заявка успешно отправлена", "success");
		});
	}, function(error){
		swal("Ошибка", error, "error");
	});
}

$(document).on("submit", "#assemblyModalForm", function(e){
	e.preventDefault();
	$("#assemblyModal").modal("hide");
	const id = $("#mainId").val();
	const check_count = $(this).serialize();
	const lw = new LW;
	lw.on();
	$.post("/ajax.php?do=assemblySales", "sales_id="+id+"&"+check_count, function(){
		swal("Успешно", "Заявка успешно отправлена", "success");
		updateBody();
		lw.off();
	});
});

// function assemblyModalSend(id){
// 	// const check = $("#tablesForm").serialize();
// 	const check_count = $("#assemblyModalForm").serialize();
// 	const lw = new LW;
// 	lw.on();
// 	$.post("/ajax.php?do=assemblySales", "sales_id="+id+"&"+check_count, function(){
// 		swal("Успешно", "Заявка успешно отправлена", "success");
// 		updateBody();
// 		lw.off();
// 	});
// }

function doMoveModal(){
	let checks = 0;
	$(".checks:checked").each(function(i, elem){
		if($(elem).val() !== "" && $(elem).val() !== "on"){
			checks++;
		}
	});
	if(checks > 0){
		$("#doMoveModal").modal("show");
	} else {
		swal("Ошибка", "Выберите товары", "error");
	}
}

function doMove(){
	let checks = $("#tablesForm").serialize();
	let sales_id = $("#mainId").val();
	
	$.post("/ajax.php?do=sendMoveSales", "sales_id="+sales_id+"&"+checks, function(data){
		swal({
			title: "Успешно",
			  text: 'Перемещение создано',
			icon: "success",
			buttons: {
				cancel: {
					text: "Закрыть",
					value: false,
					visible: true,
					className: "",
					closeModal: true,
				  },
				  confirm: {
					text: "Перейти",
					value: true,
					visible: true,
					className: "",
					closeModal: true
				  }
			}
		}).then(function(link){
			try {
				const d = JSON.parse(data);
				if(link){
					window.open('/administration/module/storage/transfer/detail/'+d.transfer_id);
				}
			} catch (error) {
				swal("Ошибка", data, "error");
			}
		});
	});
}

function fixSupplyModal(){
	$("#fixSupplyModal").modal("show");
}

function fixSupply(id){
	$.post("/ajax.php?do=fixSupply", {id:id}, function(){
		swal("Успешно!", "Уравнено!", "success");
	});
}

function add_reserv_modal(id){
	$("#reserv_modal").modal("show");
	$("#reserv_modal_btn").data("id", id);
}

async function add_reserv(id){
	if(id){
		const sales_id = $("#mainId").val();
		await $.post("/ajax.php?do=add_reserv_sales", {id, sales_id});
		get_reserve(id);
	}
}

function del_reserv_modal(id){
	$("#del_reserv_modal").modal("show");
	$("#del_reserv_modal_btn").data("id", id);
}

async function del_reserv(id){
	if(id){
		const sales_id = $("#mainId").val();
		await $.post("/ajax.php?do=del_reserv_sales", {id, sales_id});
		get_reserve(id);
	}
}

function file_view_request(){
	let array_data = [];
	for(let i = 0; i < arguments.length; i++){
		array_data.push(arguments[i]);
	}
	$("#file_request").modal("show");
	$("#file_request_body").html("Загрузка файлов...");
	$.post("/ajax.php?do=file_request_sales", {array_data}, function(data){
		$("#file_request_body").html(data);
	});
}

function open_status_modal(id){
	$("#status_modal").modal("show");
	$("#status_modal_body").html("Загрузка данных");
	get_status_log(id).then((data) => {
		$("#status_modal_body").html(data);
	});
}

function get_status_log(id){
	return new Promise((resolve, reject) => {
		$.post("/ajax.php?do=get_status_log", {id}, function(data){
			resolve(data);
		});
	});
}

function realization_view_modal(id){
	$("#realization_view_modal_body").html("Загрузка реализаций");
	$("#realization_view_modal").modal("show");
	$.post("/ajax.php?do=get_realization_sales", {id}, function(data){
		$("#realization_view_modal_body").html(data);
	});
}

function purchasePriceColumnToggle(This){
	let checkbox = $(This).prop("checked");
	if(checkbox){
		purchasePriceColumnEnable();
	} else {
		purchasePriceColumnDisable();
	}
	updateBody();
	updateTableHead();
}

function purchasePriceColumnEnable(){
	Cookies.set('purchase_price', true, { expires: 365*10});
}

function purchasePriceColumnDisable(){
	Cookies.remove('purchase_price');
}

function view_supply_order_position(supply_order){
	$("#tov_list").children().removeClass("view_supply_order");
	$.post("/ajax.php?do=view_supply_order_position", {supply_order}, function(data){
		let json = JSON.parse(data);
		json.forEach(val => {
			$("#tr_"+val).addClass("view_supply_order");
		});
	});
}

function supply_order_confirm_modal(id){
	$("#supply_order_confirm_modal").modal("show");
	$("#supply_order_confirm_modal_btn").data("id", id);
} 

function supply_order_cancel_modal(id){
	$("#supply_order_cancel_modal").modal("show");
	$("#supply_order_cancel_modal_btn").data("id", id);
} 

function supply_order_confirm(This){
	let id = $(This).data("id");
	supply_order_send_control(id, 2);
	remove_supply_order_block(id);
}

function supply_order_cancel(This){
	let id = $(This).data("id");
	let comment = $("#supply_order_cancel_modal_textarea").val();
	supply_order_send_control(id, 3, comment);
	remove_supply_order_block(id);
}

function remove_supply_order_block(id){
	$("#danger_block_"+id).remove();
}

function supply_order_send_control(id, status, comment = null){
	let sales_id = $("#mainId").val();
	$.post("/ajax.php?do=supply_order_send_control", {sales_id, id, status, comment}, function(result){
		if(result == ""){
			swal("Успешно", "Данные успешно отправлены", "success");
		} else {
			swal("Ошибка", result, "error");
		}
	});
}

function so_btn(This){
	let len = $(This).val().length;
	$("#supply_order_cancel_modal_btn").prop("disabled", (len === 0));
}

function reestr_modal(){
	$("#reestr_modal").modal("show");
	reestr_load();
	$("#reestr_modal_btn").prop("disabled", true).show();
	$("#reestr_modal_create_btn").prop("disabled", false).show();
	$("#reestr_modal_create2_btn").prop("disabled", false).hide();
}

function dis_off_reestr(){
	$("#reestr_modal_btn").prop("disabled", false);
}

function reestr_load(){
	return new Promise((resolve, reject) => {
		const id = $("#mainId").val();
		$.post("/ajax.php?do=reestr_load_sales", {id}, function(data){
			reestr_modal_body_form_html(data);
			resolve();
		});
	});
}

function reestr_modal_body_form_toggle(){
	return new Promise((resolve, reject) => {
		$("#reestr_modal_body_form").slideToggle(function(){
			resolve();
		});
	});
}

function reestr_modal_body_form_html(html){
	$("#reestr_modal_body_form").html(html);
}

function reestr_modal_save(){
	const radio = $("#reestr_modal_body_form").serializeArray();
	const module_in = "reestr";
	const pid_in = radio[0]["value"];
	const module_out = "sales";
	const pid_out = $("#mainId").val();
	const data_add = {module_in, pid_in, module_out, pid_out};
	const data_delete = {module_in, pid_out, module_out};

	relations_change(data_delete, data_add).then(() => {
		updateHead();
	});
}

function relations_change(data_delete, data_add){
	return new Promise((resolve, reject) => {
		const relations = new Relations;
		relations.delete(data_delete).then(() => {
			relations.add(data_add).then(() => {
				resolve();
			});
		});
	});
}

function reestr_modal_create(){
	$("#reestr_modal_btn").hide();
	$("#reestr_modal_create_btn").hide();
	$("#reestr_modal_create2_btn").show();
	reestr_modal_body_form_toggle().then(() => {
		check_reestr_create().then(() => {
			return `<div>Вы точно хотите создать договор?</div>`;
		}, data => {
			$("#reestr_modal_create2_btn").prop("disabled", true);
			return data;
		}).then((text) => {
			const data = [];
			data.push(text);
			reestr_modal_body_form_html(data.join(''));
			reestr_modal_body_form_toggle();
		});
	});
}

function check_reestr_create(){
	return new Promise((resolve, reject) => {
		const id = $("#mainId").val();
		$.post("/ajax.php?do=check_reestr_create_sales", {id}, function(data){
			if(data === ""){
				resolve();
			} else {
				reject(data);
			}
		})
	});
}

function reestr_modal_create2(){
	const lw = new LW;
	lw.on();
	const sales_id = $("#mainId").val();
	reestr_create().then(reestr_id => {
		const contract_data = {sales_id, reestr_id,};

		Promise.all([reestr_add_doc(reestr_id, 1), create_contract_doc(contract_data)]).then(data => {
			update_field_reestr_doc(data[0], 'doc', data[1]).then(() => {

				const module_in = "reestr";
				const pid_in = reestr_id;
				const module_out = "sales";
				const pid_out = sales_id;
				const data_add = {module_in, pid_in, module_out, pid_out};
				const data_delete = {module_in, pid_out, module_out};

				relations_change(data_delete, data_add).then(() => {
					updateHead().then(() => {
						lw.off();
						reestr_end_modal(reestr_id, "Ваш договор успешно создан");
					});
				});

			});
		});
	});
}

function reestr_end_modal(id, text){
	swal({
		title: "Успешно!",
		text,
		icon: "success",
		buttons: {
			move: {
				text: "Перейти",
				value: true,
				visible: true,
				className: "",
				closeModal: true,
			},
			cancel: {
				text: "Закрыть",
				value: false,
				visible: true,
				className: "",
				closeModal: true,
			},
		},
	}).then(move => {
		if(move){
			window.open("/administration/module/reestr/view/"+id);
		}
	});
}

function reestr_create(){
	return new Promise((resolve, reject) => {
		// const sales_id = $("#mainId").val();
		get_sales_data().then(data => {
			const sales = data[0];
			const data_send = {
				org: sales.org,
				rtype: 5,
				oid: sales.oid,
				type_doc: "out",
				object: sales.object,
			};
			$.post("/administration/module/reestr/create", data_send, function(reestr_id){
				resolve(reestr_id);
			});
		});
	});
}

//создание договора
function create_contract_doc(data){
	return new Promise((resolve, reject) => {
		$.post("/administration/module/sales/get_contract", data, function(request){
			//id договора в attach
			resolve(request);
		});
	});
}

//создаем договор в реестр
function reestr_add_doc(reestr_id, type){
	return new Promise((resolve, reject) => {
		$.post("/administration/module/reestr/addDoc", {reestr_id, type}, function(doc_id){
			resolve(doc_id);
		});
	});
}

//изменение полей в реестре документов(добавляем договор в реестр)
function update_field_reestr_doc(id, field, value){
    return new Promise((resolve, reject) => {
        $.post("/administration/module/reestr/update_reestr_doc", {id, field, value}, function(){
            resolve();
        });
    });
}

function reestr_spec_modal_create(){
	const lw = new LW;
	lw.on();
	const form = $("#reestr_spec_modal_body_form").serializeArray();
	const f = {};
	form.forEach(elem => {
		f[elem.name] = elem.value;
	});

	reestr_add_doc(f.reestr_id, 2).then(doc_id => {
		f.reestr_doc_id = doc_id;
		create_spec_doc(f).then(attach_id => {
			update_field_reestr_doc(doc_id, 'doc', attach_id).then(() => {

				const module_in = "reestr_spec";
				const pid_in = doc_id;
				const module_out = "sales";
				const pid_out = f.sales_id;
				const data_add = {module_in, pid_in, module_out, pid_out};
				const data_delete = {module_in, pid_out, module_out};
	
				relations_change(data_delete, data_add).then(() => {
					updateHead().then(() => {
						lw.off();
						reestr_end_modal(f.reestr_id, "Ваша спецификация успешно создана");
					});
				});
	
			});
		})
	})
}

//создание спецификации
function create_spec_doc(data){
	return new Promise((resolve, reject) => {
		$.post("/administration/module/sales/get_spec", data, function(request){
			//id спецификации в attach
			resolve(request);
		});
	});
}

function get_sales_data(){
	return new Promise((resolve, reject) => {
		const id = $("#mainId").val();
		$.post("/ajax.php?do=get_sales_data", {id}, function(data){
			resolve(JSON.parse(data));
		})
	});
}

function auto_create_supply(id){
	return new Promise((resolve, reject) => {
		$.post("/administration/module/supply/auto_create_supply", {id}, function(){
			resolve();
		});
	});
}

function acsModal(){
	main_slide(".sendSupplyOrder");
}

function countEtcChange(This){
	const elem = $(This);
	let step = +elem.data("step");
	const countRow = $(".countRow");
	const step_count = countRow.length / 2;
	step++;
	if(step >= step_count){
		step = 0;
	}
	console.log(step);
	console.log(countRow);
	console.log(step_count);

	elem.data("step", step);
	const count_realization = $(".count_realization");
	const count_assembly = $(".count_assembly");
	count_realization.hide();
	count_assembly.hide();
	countRow.hide().eq(step).show();
	switch(step){
		case 0:
			count_realization.show();
		break;
		case 1:
			count_assembly.show();
		break;
	}
}

function assembly_view_modal(id){
	const body = $("#assembly_view_modal_body");
	body.html("Загрузка сборок");
	$("#assembly_view_modal").modal("show");
	$.post("/ajax.php?do=get_assembly_sales", {id}, function(data){
		body.html(data);
	});
}

function transfer_view_modal(id){
	const body = $("#transfer_view_modal_body");
	body.html("Загрузка перемещений");
	$("#transfer_view_modal").modal("show");
	$.post("/ajax.php?do=get_transfer_sales", {id}, function(data){
		body.html(data);
	});
}

function append_chat(){
	const lw = new LW;
	const pid = $("#mainId").val();
	const request = {
		module: "sales",
		pid
	}
	swal({
		title: "Добавиться в чат?",
		text: "Вы действительно хотите добавиться в чат?",
		icon: "info",
		buttons: true,
		dangerMode: true,
	  })
	  .then((willAppend) => {
		if (willAppend) {
			lw.on();
			get_sales_data().then(data => {
				request.title = `Продажа №${data[0].numb}`;
				CHAT.find_append_or_create(request).then(() => {
					lw.off();
					swal("Успешно!", "Вы добавлены в чат!", "success");
				});
			})
		}
	  });
}

function automatic_sales_modal(){
	swal({
		title: "Вы уверены?",
		text: "Реализовать на склад партнёра",
		icon: "info",
		buttons: true,
		dangerMode: true,
	  })
	  .then((willSend) => {
		if (willSend) {
		  const lw = new LW;
		  lw.on();
		  automatic_sales().then(data => {
			if(data.error){
				lw.off();
				const error = document.createElement(`div`);
				error.innerHTML = data.error;
				swal({
					title: "Ошибка!",
					content: error,
					icon: "error",
				});
			} else {
				updateBody().then(() => {
					lw.off();
					swal("Успешно!", "Товары перепроданы", "success");
				})
			}
		  });
		}
	});
}

function automatic_sales(){
	return new Promise((resolve, reject) => {
		const sales_id = $("#mainId").val();
		const form = $("#tablesForm").serialize();
		$.post("/administration/module/sales/automatic_sales/"+sales_id, form, function(data){
			try{
				resolve(JSON.parse(data));
			} catch(e) {
				//e.message
				resolve({error: `Не удалось получить данные! (${data})`});
			}
		});
	});
}

function broken_block(){
	let counter = 0;
	
	return () => {
		return counter++;
	}
}

const counter_func = broken_block();

$(".sales_block__lock-disable").on("click", function(){
	const value = counter_func();
	if(value == 2){
		$("#sales_block").hide();
	}
});

function clear_stock_id_view(){
	swal({
		title: "Вы уверены?",
		text: "Очисить сохраение выбранного склада",
		icon: "info",
		buttons: true,
		dangerMode: true,
	  })
	  .then((willSend) => {
		if (willSend) {
		  const lw = new LW;
		  lw.on();
		  const name = get_stock_id_view();
		  Cookies.remove(name);
		  Promise.all([updateHead(), updateBody()]).then(() => {
			lw.off();
		  });
		}
	});
}

function toggle_table_column(t){
	const item = $(t);
	const nameList = item.find(".nameList");
	const len = nameList.children().length;
	const type = +item.data("type");
	const count = (type + 1) >= len ? 0 : (type + 1);
	item.data("type", count);

	const list = $("#tov_list");
	nameList.children().each((i, elem) => {
		const el = $(elem);
		const column = el.data("column");
		const lfc = list.find(`.${column}`);
		if(i == count){
			el.show();
			lfc.show();
		} else {
			el.hide();
			lfc.hide();
		}
	});
}

async function check_can_delete(id){
	const form = new FormData;
	const id_array = Array.isArray(id) ? id : [id];
	id_array.forEach(id => {
		form.append("id[]", id);
	})
	return await( await fetch(`/administration/module/sales/check_can_delete`, {
		method: "POST",
		body: form,
	}) ).json();
}

async function modal_del_gen(id){
	const lw = new LW;
	lw.on();
	const ccd = await check_can_delete(id);
	lw.off();
	if(ccd.status){
		const data = get_data(id);
		$(".modalFDName").html(data.s_name);
		$("#modalFDId").data("id", id);
		$("#modal-form-delete").modal("show");
	} else {
		error_modal_show_move(ccd.data);
	}
}

async function get_reserve_data(object){
	const form = new FormData;
	for(key in object){
		form.append(key, object[ key ]);
	}
	return await( await fetch("/administration/module/sales/get_reserve", {
		method: "POST",
		body: form,
	}) ).json();
}

async function get_reserve(id){
	const data = await get_reserve_data({id});
	insert_reserve(data);
}

function get_checkbox_reserv(){
	$(".checks").each((i, item) => {
		const value = $(item).val();
		if(value != "on") get_reserve(value);
	});
}

async function get_checkbox_purchase_date(){
	const jobs = [];
	$(".checks").each((i, item) => {
		const id = $(item).val();
		if(id != "on") jobs.push(change_purchase_price_date(id));
	});
	await Promise.all(jobs);
	allSumPurchasePrice();
}

async function get_all_reserv(){
	const sales_id = $("#mainId").val();
	const data = await get_reserve_data({sales_id});
	insert_reserve(data);
}

function insert_reserve(data){
	data.forEach((value, index, array) => {
		const link = get_link(value.id);
		const icon = reserv_icon(value.ndr);
		link.reserv.html(icon);
	});
}

function reserv_icon(ndr){
	const no_reserv = '<span class="text-danger reservNumber" aria-hidden="true" title="Резерв пуст">0</span>';
	let reserv_icon = "";
	if(ndr["r_this"] == 0 && ndr["r_after_this"] == 0) return no_reserv;

	if(ndr["r_this"] > 0){
		reserv_icon = `<span class="text-success reservNumber" aria-hidden="true" title="Резерв">${ndr["r_this"]}</span>`;
		if(ndr["r_after_this"] > 0){
			reserv_icon += '<span class="reservNumber">+</span>';
		}
	}

	if(ndr["r_after_this"] > 0){
		reserv_icon += `<span class="text-warning reservNumber" aria-hidden="true" title="Резерв по поступлению">${ndr["r_after_this"]}</span>`;
	}
	return reserv_icon;
}

async function smart_reserve(id){
	const json = await $.post("/administration/module/sales/smart_reserve/"+id);
	const data = JSON.parse(json);
	switch(data.action){
		case "add":
			add_reserv_modal(id);
		break;
		case "delete":
			del_reserv_modal(id);
		break;
		default:
			swal("Информация", "Действия отсутствуют", "info");
		break;
	}
}

function status_client_block(data){
	swal({
		title: "Вы уверены?",
		text: "Согласовать заявку клиента",
		icon: "info",
		buttons: true,
		dangerMode: true,
	  })
	  .then(async (willDelete) => {
		if (willDelete) {
			const sales_id = $("#mainId").val();
			const lw = new LW;
			lw.on();
			await update_sales(sales_id, "status_client", data.status);
			$(".status_client_block").remove();
			lw.off();
		}
	  });
}

async function update_sales(pk, name, value){
	await $.post('/ajax.php?do=fastChangeSales', {pk, name, value});
}

async function update_sales_detail(pk, name, value){
	await $.post("/ajax.php?do=changeSalesDetailX", {pk, name, value});
}

async function sales_users_modal(){
	await sales_users_list();
	$("#modal-sales-users").modal("show");
}

async function sales_users_list(){
	const id = $("#mainId").val();
	const list = await( await fetch(`/administration/module/sales/sales_users_list/${id}`) ).json();
	const tbody = $("#modal-sales-users-tbody");
	tbody.html("");
	list.forEach(item => {
		const tr = $(`<tr></tr>`);
		tr.append(`<td>${item.user.full_user_name}</td>`);
		tr.append(`<td>${item.type_view}</td>`);
		const btn_del = $(`<button type="button" class="btn btn-xs btn-danger">Удалить</button>`);
		btn_del.on("click", async function(){
			const lw = new LW;
			lw.on();
			await fetch(`/administration/module/sales/sales_users_list_delete/${item.id}`);
			await sales_users_list();
			lw.off();
		});
		tr.append($(`<td></td>`).append(btn_del));
		tbody.append(tr);
	});
}

async function salesUsersAdd(){
	const lw = new LW;
	lw.on();
	const form = new FormData();
	form.append("sales_id", $("#mainId").val());
	form.append("uid", $("#modal-sales-users-select").val());
	form.append("type", "CLIENT");
	const data = await (await fetch(`/administration/module/sales/sales_users_list_add`, {
		method: "POST",
		body: form,
	})).json();
	await sales_users_list();
	lw.off();
}

function getFastTaskLink(){
	return {
		id: $("#mainId").val(),
		main: $("#fastTask"),
		content: $("#fastTaskContent"),
		buttons: $("#fastTaskButtons"),
	}
}

async function fastTaskInit(){
	const link = getFastTaskLink();
	link.main.after(FastTask.modal({
		async end(){
			const data = await FastTask.get_data(link.id);
			link.content.html(viewTaskDraw(data));
		}
	}));
	const data = await FastTask.get_data(link.id);
	const btn_new = $(`<button type="button" class="btn btn-xs btn-primary">Новая задача</button>`);
	btn_new.on("click", function(){
		FastTask.modal_open(link.id);
	});
	const buttons = [btn_new];
	if(data.count == 0){
		link.content.html(`<h4>Задачи не найдены!</h4>`);
	} else {
		link.content.html(viewTaskDraw(data));
        if(data.count > 1){
			const btn_all_text = "Все задачи";
			const btn_all = $(`<button type="button" class="btn btn-xs btn-info" data-type="1">${btn_all_text}</button>`);
			btn_all.on("click", async function(){
				link.content.html("Загрузка...");
				switch(+this.dataset.type){
					case 1:
						this.dataset.type = 2;
						this.innerHTML = "Последняя задача";
						const items = await FastTask.get_data_all(link.id);
						link.content.html("");
						items.forEach(item => {
							link.content.append(viewTaskDraw(item));
						});
					break;
					case 2:
						this.dataset.type = 1;
						this.innerHTML = btn_all_text;
						const data = await FastTask.get_data(link.id);
						link.content.html(viewTaskDraw(data));
					break;
				}
                
			});
			buttons.push(btn_all);
        }
	}
	link.buttons.html(``);
	link.buttons.append(...buttons);
}

function viewTaskDraw(data){
	return FastTask.view_task_draw(data, {
		async end_task(){
			const link = getFastTaskLink();
			const data = await FastTask.get_data(link.id);
			link.content.html(viewTaskDraw(data));
		}
	});
}