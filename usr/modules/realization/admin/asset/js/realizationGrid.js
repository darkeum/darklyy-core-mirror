$(function(){
    $(document).on("click", ".universalBtn", function(){
        let realization_id = $(this).data("id");
        $("#modal-active-"+realization_id).magnificPopup("close");
        $.post("/ajax.php?do=realization_active_ajax", {realization_id:realization_id}, function(data){
            $("#jqRealization").trigger("reloadGrid");
            switch(data){
                case '1':
                    notif("success", "Информация", "Документ поступления успешно проведен");
                break;
                case '2':
                    notif("system", "Информация", "Проведение документа поступления успешно отменено");
                break;
                case '3':
                    notif("danger", "Ошибка", "Что-то пошло не так...");
                break;
				case '4':
                    notif("danger", "Ошибка", "Чтобы провести поступление необходимо приложить оригинал документа поступления!");
                break;
            }
        });
    });
    
});

function open_modal(id, type = true){
    if(type){
        check_minus_count(id).then(data => {
            modal_o('#modal-active-'+id);
        }, (data) => {
            swal('Ошибка!', `Нельзя отгружать в минус! (${data})`, 'error');
        });
    } else {
        modal_o('#modal-active-'+id);
    }
}

$(function(){
    $(document).on("click", ".syncBtn", function(){
        let realization_id = $(this).data("id");
        $("#modal-active-"+realization_id).magnificPopup("close");
        $.post("/ajax.php?do=realization_sync", {realization_id:realization_id}, function(data){
            $("#jqRealization").trigger("reloadGrid");
            switch(data){
                case '1':
                    notif("success", "Информация", "Документ успешно зарегистрирован");
                break;
                case '2':
					notif("danger", "Ошибка", "Что-то пошло не так...");
                break;
            }
        });
    });
    
});

function modalRc(id)
{				
	$('#idRc').val(id);	
	$('#modalRc').modal('show');
}

$("#formRc").submit(function (e) {
    e.preventDefault();
    var data = $("#formRc").serializeArray();
    $.ajax({
        type: "POST",
        url: "/ajax.php?do=createRc",
        data: data,
        success: function(response) {
			//Данные отправлены успешно			
			result = jQuery.parseJSON(response);
			if(result["type"] == 'success')
			{
				//$("#jqCorr").trigger("reloadGrid");
				//$grid.jqGrid('delRowData',result["data"]["rowID"]);				
			}
			$('#modalRc').modal('hide');			
			$('#formRc')[0].reset();
			notif(result["type"], result["title"],  result["text"]);
        },
        error: function(response) {
			alert("no");
        }
		
	});}); 
	