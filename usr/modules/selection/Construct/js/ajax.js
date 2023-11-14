function calculateCostZp(){

    $('.raschetStoimostyLoading').css('display','block');
    $('.raschetStoimostyResult').css('display','none');

    var c = $('<div class="box-modal" />');
    c.html($('#raschetStoimosty').html());
    c.prepend('<div class="box-modal_close arcticmodal-close">закрыть</div>');
    $.arcticmodal({
        content: c
    });

    var postDataAjax = '';

    $('#constructMap .spanHidden').each(function(ind){

        if($(this).text()) {

            if (ind) {postDataAjax += '||'}
            postDataAjax += 'a' + ind + ':' + $(this).text();

        }


    });
	let iUrl = window.location.href.split("/");
	iUrl = iUrl[iUrl.length-1];
    $.post(
        "https://atri-energo.ru/ajax.php?do=selectCode",
        {

            iData: $('#i-data').text(),
            iDataGeneral: $('#i-data-general').text(),
			iUrl: iUrl,
            postData: postDataAjax,
            iq: $('#i-q').text()

        },
        function onAjaxSuccess(data) {

            //$('.raschetStoimostyResult').html(data);
            $('.raschetStoimostyLoading').css('display','none');
            $('.raschetStoimostyResult').css('display','block');

            let res = '<div class="titleRas"><h1>Результаты расчетов</h1></div><div>';

            let json = JSON.parse(data);

            res += '<div class="groupBlock"><div><h3>Блочный тепловой пункт (БТП: '+json["group"]["code"]+')</h3></div>';

            if(json["group"]["id"] == -1){
                res += '<div class="info-msg1"><i class="fa fa-info-circle"></i> По данному БТП информация отсутствует. Наши сотрудники подготовят данные в течении 1 часа. Результат выполнения вы можете посмотреть в разделе мои расчеты.</div>';
            } else {
                res += '<div><a class="btnClassic" href="/selection/print/pz/'+iUrl+'">Получить ПЗ</a><a class="btnClassic" href="/selection/specification_all/'+iUrl+'">Получить общую спецификацию</a><a class="btnClassic" href="/download/'+json["group"]["aid_scheme"]+'">Получить общую схему</a></div>';
            }
            res += '</div>';
            res += '<div class="groupBlock">';
            for(let i=0;i<json["blocks"].length;i++){
                res += '<div class="hrBlock"><div><h3>'+json["blocks"][i]["type"]+' - '+json["blocks"][i]["code"]+'</h3></div>';
                if(json["blocks"][i]["id"] == -1){
                    res += '<div class="info-msg1"><i class="fa fa-info-circle"></i> По данному блоку информация отсутствует. Наши сотрудники подготовят данные в течении 1 часа. Результат выполнения вы можете посмотреть в разделе мои расчеты.</div>';
                } else {
                    res += '<div>';
                    if(json["blocks"][i]["aid_scheme"] != -1){
                        res += '<a href="/download/'+json["blocks"][i]["aid_scheme"]+'" class="btnClassic">Получить схему</a>';
                    }
                    if(json["blocks"][i]["aid_size"] != -1){
                        res += '<a href="/download/'+json["blocks"][i]["aid_size"]+'" class="btnClassic">Получить размеры</a>';
                    }
                    res += '<a href="/catalog_v2/specification/selection/'+json["blocks"][i]["id"]+'" class="btnClassic">Получить спецификацию</a>';
                    res +='</div>';
                }
                res +='</div>';
            }
            res +='</div>';
            res +='</div>';
            res += '<div class="modalBottom"><button type="button" class="btn btn-dark arcticmodal-close">Отмена</button><button type="button" onclick="window.open(\'/selection/my\');" class="btn btn-danger">Мои расчеты</button></div>';

            $('.raschetStoimostyResult').html(res);
        }
    );


}