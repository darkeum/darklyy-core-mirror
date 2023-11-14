$(function(){
    fastTaskInit();
})

function getFastTaskLink(){
	return {
		id: vm.sales.id,
		main: $("#fastTask"),
		content: $("#fastTaskContent"),
		buttons: $("#fastTaskButtons"),
	}
}

async function fastTaskInit(){
	const link = getFastTaskLink();
	link.main.after(FastTask5.modal({
		async end(){
			const data = await FastTask5.get_data(link.id);
			link.content.html(viewTaskDraw(data));
		}
	}));
	const data = await FastTask5.get_data(link.id);
	const btn_new = $(`<button type="button" class="btn btn-sm btn-primary">Новая задача</button>`);
	btn_new.on("click", function(){
		FastTask5.modal_open(link.id);
	});
	const buttons = [btn_new];
	if(data.count == 0){
		link.content.html(`<h4>Задачи не найдены!</h4>`);
	} else {
		link.content.html(viewTaskDraw(data));
        if(data.count > 1){
			const btn_all_text = "Все задачи";
			const btn_all = $(`<button type="button" class="btn btn-sm btn-info" data-type="1">${btn_all_text}</button>`);
			btn_all.on("click", async function(){
				link.content.html("Загрузка...");
				switch(+this.dataset.type){
					case 1:
						this.dataset.type = 2;
						this.innerHTML = "Последняя задача";
						const items = await FastTask5.get_data_all(link.id);
						link.content.html("");
						items.forEach(item => {
							link.content.append(viewTaskDraw(item));
						});
					break;
					case 2:
						this.dataset.type = 1;
						this.innerHTML = btn_all_text;
						const data = await FastTask5.get_data(link.id);
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
	return FastTask5.view_task_draw(data, {
		async end_task(){
			const link = getFastTaskLink();
			const data = await FastTask5.get_data(link.id);
			link.content.html(viewTaskDraw(data));
		}
	});
}