var thisBlock,
    urlImageBlock = '',
    dropThis,
    dropUi,
    thisBlockCont,
    thisBlockContPodpitka,
    systemMessageIndex = 0,
    statusDelete = true,
    statusGVS = 1;
    blockVvodaType = 0,
    arrPoint = new Array(),
    arrPoint2 = new Array(),
    scetcikI = 0;


var cloneBlockVvoda = $('.blockVvodaDS').html(),
    cloneBlockOtopleniyaList = $('.blockOtopleniyaList').html(),
    blockPodpitkyList = $('.blockPodpitkyList').html();



$(document).ready(function(){

    $('#loading').fadeOut(200);

    var mapCloneLast = $('.MapStr:last');

    for(var i = 0; i < 1; i++){

        $('.MapStr .MapBlockImg:eq(1) .strongBlock:last').after('<div class="strongBlock"><div class="MapBlockDrop"></div><div class="blockSystem"></div></div>');

    }


    $('.MapStr:last').after('<div class="clear"></div>');



    $('.draggable').draggable({

        helper: "clone",

    });


    dropFn();




    $('.MapBlockDropList .MapBlockDrop').hover(

        function(){


            if($(this).html() == ''){

                if(statusGVS){

                    var htmlMenu = '<div class="clickDpMenu"><span onclick="modalGenerator(\'block_otopleniya\',\'\',\'\',$(this).parents(\'.MapBlockDrop\'))">Вставить блок отопления</span><span onclick="modalGenerator(\'block_gvs\',\'\',\'\',$(this).parents(\'.MapBlockDrop\'))">Вставить блок ГВС</span></div>';

                }else{

                    var htmlMenu = '<div class="clickDpMenu"><span onclick="modalGenerator(\'block_otopleniya\',\'\',\'\',$(this).parents(\'.MapBlockDrop\'))">Вставить блок отопления</span></div>';


                }

                $(this).html('<div class="insertBlockNew insertBlockNull" onclick="menuType()">'+htmlMenu+'<div class="clickDp"><div class="iconHover"></div><span>Вставить блок</span></div></div>');

                $(this).addClass('blockActiveHover');

            }


        },function(){


            if($(this).find('div').hasClass('insertBlockNull')){

                $(this).removeClass('blockActiveHover');

                $(this).html('');

            }


        }

    );


    $('#blockLeft').niceScroll({
        cursorcolor:"#555",
        cursorborder:"0px solid #000",
        cursoropacitymin: 1,
        background:"#000",
        cursorminheight:10,
        railalign: "left",
        railpadding: {top:2,right:2,left:2,bottom:2}
    });


});


function dropFn(){

    $('.MapBlockDrop').droppable({

        hoverClass: 'dropHere',

        drop: function (event, ui) {

            dropThis = $(this);
            dropUi = ui;


            var dataTypeDrop = $('img', dropUi.draggable).attr('data-type');


            // Работа с блоком ввода
            if(dataTypeDrop == 'block_vvoda' && $(this).hasClass('MapBlockDropInput')){


                modalGenerator(ui, dataTypeDrop, $(this));


            }else{


                if(dataTypeDrop == 'block_vvoda' && !$(this).hasClass('MapBlockDropInput')){


                    systemMessage('error', 'Вставьте блок ввода после тепловой сети');


                }else if(dataTypeDrop != 'block_vvoda' && $(this).hasClass('MapBlockDropInput')){


                    systemMessage('error', 'Вставьте <span class="mesNgReg">'+$('h3', dropUi.draggable).html()+'</span> после блока ввода');


                }else{


                    if(dataTypeDrop != 'block_otopleniya'  && $(this).hasClass('blockOtopleniyaList')){


                        systemMessage('error', 'Вставьте блок отопления');


                    }else if(dataTypeDrop != 'block_podpitky'  && $(this).hasClass('blockPodpitkyList')){


                        systemMessage('error', 'Вставьте блок подпитки');


                    }else if(dataTypeDrop == 'block_podpitky' && !$(this).hasClass('blockPodpitkyList')){

                        systemMessage('error', 'Вставьте блок подпитки под блок отопления');


                    }else {

                        modalGenerator(ui, dataTypeDrop, $(this));

                    }


                }


            }



        }

    });

}


// Генератор модального окна
function modalGenerator(ui, typeBlock, aThis, bThis) {


    if (statusDelete) {

        if (bThis) {

            dropThis = bThis;

        }


        if ($(bThis).html()) {

            var thisBPdp = $(bThis);

        } else {

            var thisBPdp = dropThis;

        }


        var c = $('<div class="box-modal" />');


        if (ui.draggable) {

            c.html($('#' + $('img', ui.draggable).attr('data-type')).html());

        } else {


            c.html($('#' + ui).html());


        }

        c.prepend('<div class="box-modal_close arcticmodal-close">закрыть</div>');
        $.arcticmodal({
            closeOnOverlayClick: false,
            content: c
        });


    } else {


        setTimeout(varTime, 1000);


    }

    if($('#st-i-nasos').val() == 1){
        
        $('.box-modal .carousel-element[data-id=23]').css('display','none');

    }


        if (typeBlock == 'block_vvoda') {

            if($('#st-i-nasos').val() == 1){

                $('.box-modal .carousel-element[data-line=1]').css('display','none');
                $('.box-modal .carousel-element[data-nasos=1]').css('display','table-cell');

            }


        if (blockVvodaType == 1) {


            $('.box-modal .carousel-element[data-line=0]').css('display', 'none');


        } else if (blockVvodaType == 2) {


            $('.box-modal .carousel-element[data-line=1]').css('display', 'none');


        }


    }


    typeBlock = $('.box-modal .modalCharacteristic').attr('data-type');

    var inputData = $(thisBPdp).find('span[data-type='+typeBlock+']').text();


    // Выставляем выделение карусели в окне
    if (thisBPdp.find('img').attr('data-id')) {


        $(".box-modal .carousel-element").removeClass('active');

        if($(".box-modal .carousel-element[data-id=" + thisBPdp.find('img').attr('data-id') + "]:visible")){

            $(".box-modal .carousel-element[data-id=" + thisBPdp.find('img').attr('data-id') + "]").addClass('active');

        }else{

            $(".box-modal .carousel-element:first").addClass('active');

        }



    } else {


        $(".box-modal .carousel-element:first").addClass('active');


    }



    if (typeBlock == 'block_podpitky') {


        $('.box-modal .modalCharacteristic[data-type=block_podpitky] .nameTypeBlock').html('Выбран <b>' + thisBPdp.parents('.strongBlockTop').prev().find('h3').text() + '</b>');


    }


    if (inputData != '') {

        var inputDataJSON = $.parseJSON(inputData);


        if (typeBlock == 'block_vvoda') {

            ////////////////////
            /////////////////////
            ////////////////////
            //////////////////



            $('.box-modal .modalCharacteristic[data-type=block_vvoda] select[name=qType] option:contains("' + inputDataJSON.block_vvoda.qType + '")').attr("selected", "selected").trigger('change');

            if (inputDataJSON.block_vvoda.uzel_ucheta == 'true') {
                $('.box-modal #uzel_ucheta').prop('checked', true).trigger('change');
            } else {
                $('.box-modal #uzel_ucheta').prop('checked', false).trigger('change');
            }

            if (inputDataJSON.block_vvoda.regul_perepad == 'true') {
                $('.box-modal #regul_perepad').prop('checked', true).trigger('change');
            } else {
                $('.box-modal #regul_perepad').prop('checked', false).trigger('change');
            }

            if (inputDataJSON.block_vvoda.bal_klapan == 'true') {
                $('.box-modal #bal_klapan').prop('checked', true).trigger('change');
            } else {
                $('.box-modal #bal_klapan').prop('checked', false).trigger('change');
            }


            // --- Шкафы. Заполнение полей ---

            if (inputDataJSON.block_vvoda.shkafPodpitka == 'true') {
                $('.box-modal #shkafPodpitka').prop('checked', true);
            } else {
                $('.box-modal #shkafPodpitka').prop('checked', false);
            }

            if (inputDataJSON.block_vvoda.regul_perepad == 'true') {
                $('.box-modal #regul_perepad').prop('checked', true).trigger('change');
            } else {
                $('.box-modal #regul_perepad').prop('checked', false);
            }

            if (inputDataJSON.block_vvoda.shkafDopObor == 'true') {
                $('.box-modal #shkafDopObor').prop('checked', true).trigger('change');
            } else {
                $('.box-modal #shkafDopObor').prop('checked', false);
            }


            $('.box-modal input[name=skafDi][value='+inputDataJSON.block_vvoda.skafDi+']').attr('checked',true).trigger('change');
            $('.box-modal input[name=skafDo][value='+inputDataJSON.block_vvoda.skafDo+']').attr('checked',true).trigger('change');



        }


        if (typeBlock == 'block_otopleniya') {


            $('.box-modal .modalCharacteristic[data-type=block_otopleniya] input[name=q]').val(inputDataJSON.block_otopleniya.q);
            $('.box-modal .modalCharacteristic[data-type=block_otopleniya] input[name=dp]').val(inputDataJSON.block_otopleniya.dp);
            $('.box-modal .modalCharacteristic[data-type=block_otopleniya] input[name=h]').val(inputDataJSON.block_otopleniya.h);
            $('.box-modal .modalCharacteristic[data-type=block_otopleniya] input[name=typeBlock][value='+inputDataJSON.block_otopleniya.typeBlock+']').attr('checked',true).trigger('change');
            $('.box-modal .modalCharacteristic[data-type=block_otopleniya] input[name=pch][value='+inputDataJSON.block_otopleniya.pch+']').attr('checked',true).trigger('change');
            $('.box-modal .modalCharacteristic[data-type=block_otopleniya] select[name=qType] option:contains("' + inputDataJSON.block_otopleniya.qType + '")').attr("selected", "selected").trigger('change');
            $('.box-modal .modalCharacteristic[data-type=block_otopleniya] select[name=kontr] option:contains("' + inputDataJSON.block_otopleniya.kontr + '")').attr("selected", "selected").trigger('change');
            $('.box-modal .modalCharacteristic[data-type=block_otopleniya] select[name=regKlapan] option:contains("' + inputDataJSON.block_otopleniya.regKlapan + '")').attr("selected", "selected").trigger('change');
            $('.box-modal .modalCharacteristic[data-type=block_otopleniya] select[name=t11] option:contains("' + inputDataJSON.block_otopleniya.t11 + '")').attr("selected", "selected").trigger('change');
            $('.box-modal .modalCharacteristic[data-type=block_otopleniya] select[name=t21] option:contains("' + inputDataJSON.block_otopleniya.t21 + '")').attr("selected", "selected").trigger('change');
            $('.box-modal .modalCharacteristic[data-type=block_otopleniya] select[name=shkafAvtomatiky] option:contains("' + inputDataJSON.block_otopleniya.shkafAvtomatiky + '")').attr("selected", "selected").trigger('change');
            $('.box-modal .modalCharacteristic[data-type=block_otopleniya] select[name=dpType] option:contains("' + inputDataJSON.block_otopleniya.dpType + '")').attr("selected", "selected").trigger('change');


        }


        if (typeBlock == 'block_gvs') {


            $('.box-modal .modalCharacteristic[data-type=block_gvs] input[name=typeNag][value='+inputDataJSON.block_gvs.typeNag+']').attr('checked',true).trigger('change');
            $('.box-modal .modalCharacteristic[data-type=block_gvs] select[name=qSelGVS] option:contains("' + inputDataJSON.block_gvs.qSelGVS + '")').attr("selected", "selected").trigger('change');
            $('.box-modal .modalCharacteristic[data-type=block_gvs] input[name=qGVS]').val(inputDataJSON.block_gvs.qGVS);
            $('.box-modal .modalCharacteristic[data-type=block_gvs] input[name=GVSgt1]').val(inputDataJSON.block_gvs.GVSgt1);
            $('.box-modal .modalCharacteristic[data-type=block_gvs] input[name=GVSt1]').val(inputDataJSON.block_gvs.GVSt1);
            $('.box-modal .modalCharacteristic[data-type=block_gvs] input[name=GVSt2]').val(inputDataJSON.block_gvs.GVSt2);
            $('.box-modal .modalCharacteristic[data-type=block_gvs] input[name=GVSt3]').val(inputDataJSON.block_gvs.GVSt3);
            $('.box-modal .modalCharacteristic[data-type=block_gvs] input[name=GVStv1]').val(inputDataJSON.block_gvs.GVStv1);
            $('.box-modal .modalCharacteristic[data-type=block_gvs] input[name=pb1]').val(inputDataJSON.block_gvs.pb1);
            $('.box-modal .modalCharacteristic[data-type=block_gvs] select[name=pb1SelGVS] option:contains("' + inputDataJSON.block_gvs.pb1SelGVS + '")').attr("selected", "selected").trigger('change');
            $('.box-modal .modalCharacteristic[data-type=block_gvs] input[name=shemaPodkl][value='+inputDataJSON.block_gvs.shemaPodkl+']').attr('checked',true).trigger('change');
            $('.box-modal .modalCharacteristic[data-type=block_gvs] select[name=kontrGVS] option:contains("' + inputDataJSON.block_gvs.kontrGVS + '")').attr("selected", "selected").trigger('change');
            $('.box-modal .modalCharacteristic[data-type=block_gvs] select[name=regKlapanGVS] option:contains("' + inputDataJSON.block_gvs.regKlapanGVS + '")').attr("selected", "selected").trigger('change');
            $('.box-modal .modalCharacteristic[data-type=block_gvs] select[name=shkafAvtomatikyGVS] option:contains("' + inputDataJSON.block_gvs.shkafAvtomatikyGVS + '")').attr("selected", "selected").trigger('change');


            if (inputDataJSON.block_gvs.dhodDBlockBOV == 'true') {
                $('.box-modal #dhodDBlockBOV').prop('checked', true);
            } else {
                $('.box-modal #dhodDBlockBOV').prop('checked', false);
            }


        }


        if (typeBlock == 'block_podpitky') {


            $('.box-modal .modalCharacteristic[data-type=block_podpitky] input[name=nasosStan][value='+inputDataJSON.block_podpitky.nasosStan+']').attr('checked',true).trigger('change');
            $('.box-modal .modalCharacteristic[data-type=block_podpitky] select[name=povNasos] option:contains("' + inputDataJSON.block_podpitky.povNasos + '")').attr("selected", "selected").trigger('change');
            $('.box-modal .modalCharacteristic[data-type=block_podpitky] select[name=klapanSol] option:contains("' + inputDataJSON.block_podpitky.klapanSol + '")').attr("selected", "selected").trigger('change');
            $('.box-modal .modalCharacteristic[data-type=block_podpitky] select[name=rasshirBak] option:contains("' + inputDataJSON.block_podpitky.rasshirBak + '")').attr("selected", "selected").trigger('change');


        }


    }

    if($('#i-data').text() != ''){

        var inputIDataJSON = $.parseJSON($('#i-data').text());

    }


    if($('#i-q').text() != '') {

        var jsonQ = $.parseJSON($('#i-q').text());

    }

    $('.box-modal .modalCharacteristic[data-type=block_vvoda] input[name=q]').val(jsonQ.iQ.q);

    if ((typeBlock == 'block_vvoda' && $('#i-data').text() != '') || (typeBlock == 'block_otopleniya' && $('#i-data').text() != '')) {


        /*$('.box-modal input[name=t1]').val(inputIDataJSON.iData.t1);*/
        $('.box-modal input[name=t2]').val(inputIDataJSON.iData.t2);

        $('.box-modal select[name=t1] option:contains("' + inputIDataJSON.iData.t1 + '")').attr("selected", "selected").trigger('change');


    }


    if ($('#i-data-general').text() != '') {

        var inputIDataGeneralJSON = $.parseJSON($('#i-data-general').text());

        $('.box-modal input[name=p1]').val(inputIDataGeneralJSON.iDataGeneral.p1);
        $('.box-modal input[name=p2]').val(inputIDataGeneralJSON.iDataGeneral.p2);

        $('.box-modal select[name="p1Type"] option:contains("' + inputIDataGeneralJSON.iDataGeneral.p1Type + '")').attr("selected", "selected").trigger('change');
        $('.box-modal select[name="p2Type"] option:contains("' + inputIDataGeneralJSON.iDataGeneral.p2Type + '")').attr("selected", "selected").trigger('change');


    }



    adapCarusel();


}


function adapCarusel(){


    var widthImg = 0;


    $('.box-modal .carousel-element img').each(function (indx){

        var loadImg = new Image();

        loadImg.src = $(this).attr('src');

        loadImg.onload = function() {

            // Расчитываем размеры карусели
            widthImg += loadImg.width;

            if($('.box-modal .carousel-element:visible img').length - 1 == indx){


                if($('.box-modal').width() < widthImg){


                    $('.carouselPrev, .carouselNext').css('display','block');
                    $('.carousel').removeClass('carouselLine');


                }else{


                    $('.carouselPrev, .carouselNext').css('display','none');
                    $('.carousel').addClass('carouselLine');


                }

            }


        }


    });


}


function menuType(){

    $('.clickDp').css('display','none');
    $('.clickDpMenu').fadeIn(200);

}


function varTime() {

    statusDelete = true;

}


// Генерация системных сообщений
function systemMessage(typeWindow, messageText){

    if(typeWindow == 'error'){


        var messageHtml = '<div id="MesIndex'+systemMessageIndex+'" class="systemMessageError">';
            messageHtml += '<i class="fa fa-times" onclick="messageClose(this)" aria-hidden="true"></i>';
            messageHtml += '<div class="systemMessageDiv">';
            messageHtml += '<div class="systemMessageIcon"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i></div>';
            messageHtml += '<h3>Ошибка</h3>';
            messageHtml += '<p>'+messageText+'</p>';
            messageHtml += '</div>';
            messageHtml += '</div>';

            $('#systemMessage').append(messageHtml);


    }

    if(typeWindow == 'info'){


        var messageHtml = '<div id="MesIndex'+systemMessageIndex+'" class="systemMessageInfo">';
        messageHtml += '<i class="fa fa-times" onclick="messageClose(this)" aria-hidden="true"></i>';
        messageHtml += '<div class="systemMessageDiv">';
        messageHtml += '<div class="systemMessageIcon"><i class="fa fa-info-circle" aria-hidden="true"></i></div>';
        messageHtml += '<h3>Предупреждение</h3>';
        messageHtml += '<p>'+messageText+'</p>';
        messageHtml += '</div>';
        messageHtml += '</div>';

        $('#systemMessage').append(messageHtml);


    }


    $("#MesIndex"+systemMessageIndex).css({opacity: "1"}).delay(10000).animate({opacity: "0"}, 1000);

    systemMessageIndex++;

}


// Конструктор линий
function conclusionLineStart(){

    var gvsLine = $('.MapBlockDropList .MapBlockDrop img[data-type=block_gvs]').attr('data-lile');

    $('#svg-line .line').remove();
    $('#svg-point').html('');
    arrPoint = [];
    arrPoint2 = [];
    thisBlockCont = '';
    thisBlockContPodpitka = '';


    if($('.MapBlockDropInput img').attr('data-id')){

        var vImage = $('.MapBlockDropInput img'),
            vImagePos = $('.MapBlockDropInput img').position();


        // Рисуем лини от тепловой сети
        var lineX1 = $('.MapBlock1 .heat').position().left + 24,
            lineY1 = $('.MapBlock1 .heat').position().top + 24,
            lineX2 = vImagePos.left + parseInt(vImage.attr('data-left')),
            lineY2 = vImagePos.top + parseInt(vImage.attr('data-h1'));

        conclusionLine('red', lineX1, lineY1, lineX2, lineY2);


        var lineX1 = $('.MapBlock1 .cold').position().left + 24,
            lineY1 = $('.MapBlock1 .cold').position().top + 37,
            lineX2 = vImagePos.left + parseInt(vImage.attr('data-left')),
            lineY2 = vImagePos.top + parseInt(vImage.attr('data-c1'));

        conclusionLine('blue', lineX1, lineY1, lineX2, lineY2);


        // Проходим блоки
        $('.MapBlockDropList .MapBlockDrop').each(function(){

            if($(this).find('img').attr('data-id')){


                if($(this).find('img').attr('data-type') == 'block_otopleniya'){


                    //Линии на третий контейнер

                    var blockDab = $(this).find('img');

                    var lineX1 = blockDab.position().left + blockDab.width(),
                        lineY1 = blockDab.position().top + parseInt(blockDab.attr('data-h1')),
                        lineX2 = blockDab.position().left + blockDab.width() + 80,
                        lineY2 = blockDab.position().top + parseInt(blockDab.attr('data-h1'));

                    conclusionLine('red', lineX1, lineY1, lineX2, lineY2);

                    var lineX1 = blockDab.position().left + blockDab.width(),
                        lineY1 = blockDab.position().top + parseInt(blockDab.attr('data-c1')),
                        lineX2 = blockDab.position().left + blockDab.width() + 80,
                        lineY2 = blockDab.position().top + parseInt(blockDab.attr('data-c1'));

                    conclusionLine('blue', lineX1, lineY1, lineX2, lineY2);


                }

                if($(this).find('img').attr('data-type') != 'block_podpitky'){


                    // Определяем высоту линии
                    thisBlockCont = $(this).find('img');


                    var lineX1 = vImagePos.left + vImage.width() + 110,
                        lineY1 = thisBlockCont.position().top + parseInt(thisBlockCont.attr('data-h1')),
                        lineX2 = thisBlockCont.position().left + parseInt(thisBlockCont.attr('data-left')),
                        lineY2 = thisBlockCont.position().top + parseInt(thisBlockCont.attr('data-h1'));

                    conclusionLine('red', lineX1, lineY1, lineX2, lineY2);



                    if(gvsLine == 1){


                        ///////////
                        // Блок ГВС 2 вариант
                        ///////////

                        if(thisBlockCont.attr('data-type') != 'block_gvs') {
                            var lineX1 = vImagePos.left + vImage.width() + 78,
                                lineY1 = thisBlockCont.position().top + parseInt(thisBlockCont.attr('data-c1')),
                                lineX2 = thisBlockCont.position().left,
                                lineY2 = thisBlockCont.position().top + parseInt(thisBlockCont.attr('data-c1'));

                            conclusionLine('blue', lineX1, lineY1, lineX2, lineY2);

                            arrPoint2.push(lineY2)

                        }


                    }else{


                        var lineX1 = vImagePos.left + vImage.width() + 45,
                            lineY1 = thisBlockCont.position().top + parseInt(thisBlockCont.attr('data-c1')),
                            lineX2 = thisBlockCont.position().left,
                            lineY2 = thisBlockCont.position().top + parseInt(thisBlockCont.attr('data-c1'));

                        conclusionLine('blue', lineX1, lineY1, lineX2, lineY2);

                        // Вставить принт
                        arrPoint2.push(lineY2);


                    }


                }else{


                    // Отрисовка линий подпитки
                    if($(this).find('img').attr('data-type') == 'block_podpitky'){


                        thisBlockContPodpitka = $(this).find('img');


                        var lineX1 = vImagePos.left + vImage.width() + 12,
                            lineY1 = thisBlockContPodpitka.position().top + parseInt(thisBlockContPodpitka.attr('data-h1')),

                            lineX2 = thisBlockContPodpitka.position().left + parseInt(thisBlockContPodpitka.attr('data-left')),
                            lineY2 = thisBlockContPodpitka.position().top + parseInt(thisBlockContPodpitka.attr('data-h1'));

                        conclusionLine('blue', lineX1, lineY1, lineX2, lineY2);

                        arrPoint.push(lineY2);


                    }


                    var vImagePrev = thisBlockContPodpitka.parents('.MapBlockDrop ').prev().find('img');

                    if(vImagePrev.attr('data-id')){


                        var lineX1 = vImagePrev.position().left + parseInt(vImagePrev.attr('data-podpitka')),
                            lineY1 = vImagePrev.position().top + parseInt(vImagePrev.height()) - 13,
                            lineX2 = vImagePrev.position().left + parseInt(vImagePrev.attr('data-podpitka')),
                            lineY2 = thisBlockContPodpitka.position().top + parseInt(thisBlockContPodpitka.attr('data-podpitka'));

                        conclusionLine('blue', lineX1, lineY1, lineX2, lineY2);


                    }


                    var vImagePrev = thisBlockContPodpitka.parents('.strongBlockTop ').prev().find('img');

                    if(vImagePrev.attr('data-id')){


                        var lineX1 = vImagePrev.position().left + parseInt(vImagePrev.attr('data-podpitka')),
                            lineY1 = vImagePrev.position().top + parseInt(vImagePrev.height()) - 13,
                            lineX2 = vImagePrev.position().left + parseInt(vImagePrev.attr('data-podpitka')),
                            lineY2 = thisBlockContPodpitka.position().top + parseInt(thisBlockContPodpitka.attr('data-podpitka'));

                        conclusionLine('blue', lineX1, lineY1, lineX2, lineY2);


                    }


                }


            }

        });




        if(thisBlockCont){

            var lineX1 = vImagePos.left + vImage.width(),
                lineY1 = vImagePos.top + parseInt(vImage.attr('data-h1')),
                lineX2 = vImagePos.left + vImage.width() + 110,
                lineY2 = vImagePos.top + parseInt(vImage.attr('data-h1'));

            conclusionLine('red', lineX1, lineY1, lineX2, lineY2);



            var lineX1 = vImagePos.left + vImage.width() + 110,
                lineY1 = vImagePos.top + parseInt(vImage.attr('data-h1')),
                lineX2 = vImagePos.left + vImage.width() + 110,
                lineY2 = thisBlockCont.position().top + parseInt(vImage.attr('data-h1'));

            conclusionLine('red', lineX1, lineY1, lineX2, lineY2);



            // Блок ГВС исключение
            if(gvsLine == 1) {


                var lineX1 = vImagePos.left + vImage.width() + 78,
                    lineY1 = vImagePos.top + parseInt(vImage.attr('data-c1')),
                    lineX2 = vImagePos.left + vImage.width() + 78,
                    lineY2 = thisBlockCont.position().top + parseInt(thisBlockCont.attr('data-t'));

                conclusionLine('blue', lineX1, lineY1, lineX2, lineY2);


                var lineX1 = vImagePos.left + vImage.width() + 78,
                    lineY1 = thisBlockCont.position().top + parseInt(thisBlockCont.attr('data-t')),
                    lineX2 = thisBlockCont.position().left,
                    lineY2 = thisBlockCont.position().top + parseInt(thisBlockCont.attr('data-t'));

                conclusionLine('blue', lineX1, lineY1, lineX2, lineY2);

                var lineX1 = vImagePos.left + vImage.width() + 45,
                    lineY1 = thisBlockCont.position().top + parseInt(thisBlockCont.attr('data-c1')),
                    lineX2 = thisBlockCont.position().left,
                    lineY2 = thisBlockCont.position().top + parseInt(thisBlockCont.attr('data-c1'));

                conclusionLine('blue', lineX1, lineY1, lineX2, lineY2);


            }



            // Линия обратки
            var lineX1 = vImagePos.left + vImage.width(),
                lineY1 = vImagePos.top + parseInt(vImage.attr('data-c1')),
                lineX2 = vImagePos.left + vImage.width() + 45,
                lineY2 = vImagePos.top + parseInt(vImage.attr('data-c1'));

            conclusionLine('blue', lineX1, lineY1, lineX2, lineY2);

            var lineX1 = vImagePos.left + vImage.width() + 45,
                lineY1 = vImagePos.top + parseInt(vImage.attr('data-c1')),
                lineX2 = vImagePos.left + vImage.width() + 45,
                lineY2 = thisBlockCont.position().top + parseInt(thisBlockCont.attr('data-c1'));

            conclusionLine('blue', lineX1, lineY1, lineX2, lineY2);


        }


        // Отрисовка линии подпитки
        if(thisBlockContPodpitka){


            var lineX1 = vImagePos.left + vImage.width(),
                lineY1 = vImagePos.top + parseInt(vImage.attr('data-t')),
                lineX2 = vImagePos.left + vImage.width() + 12,
                lineY2 = vImagePos.top + parseInt(vImage.attr('data-t'));

            conclusionLine('blue', lineX1, lineY1, lineX2, lineY2);


            var lineX1 = vImagePos.left + vImage.width() + 12,
                lineY1 = vImagePos.top + parseInt(vImage.attr('data-t')),
                lineX2 = vImagePos.left + vImage.width() + 12,
                lineY2 = thisBlockContPodpitka.position().top + parseInt(thisBlockContPodpitka.attr('data-h1'));

            conclusionLine('blue', lineX1, lineY1, lineX2, lineY2);


        }


        if(arrPoint){

            if(thisBlockCont) {


                var pointBlockCont = thisBlockCont.position().top + parseInt(thisBlockCont.attr('data-h1')),
                    pointBlockContLeft = vImagePos.left + vImage.width() + 110 - 7;

                for (var i = 0; arrPoint.length > i; i++) {


                    if (arrPoint[i] < pointBlockCont) {

                        var arrNumTop = arrPoint[i] - 6;

                        $('#svg-point').append('<div class="point" style="top: ' + arrNumTop + 'px; left: ' + pointBlockContLeft + 'px"></div>');


                        var pointBlockContLeftNum = pointBlockContLeft - 65;

                        $('#svg-point').append('<div class="point" style="top: ' + arrNumTop + 'px; left: ' + pointBlockContLeftNum + 'px"></div>');


                        // Блок ГВС исключение
                        if(gvsLine == 1) {

                            var pointBlockContLeftNum = pointBlockContLeft - 32;

                            $('#svg-point').append('<div class="point" style="top: ' + arrNumTop + 'px; left: ' + pointBlockContLeftNum + 'px"></div>');

                        }


                    }


                }


            }


        }


        if(arrPoint2){

            if(thisBlockCont) {


                var pointBlockCont = thisBlockCont.position().top + parseInt(thisBlockCont.attr('data-h1')),
                    pointBlockContLeft = vImagePos.left + vImage.width() + 110 - 7;

                for (var i = 0; arrPoint2.length > i; i++) {


                    if (arrPoint2[i] < pointBlockCont) {

                        var arrNumTop = arrPoint2[i] - 6;

                        $('#svg-point').append('<div class="point" style="top: ' + arrNumTop + 'px; left: ' + pointBlockContLeft + 'px"></div>');


                    }


                }


            }


        }


    }


}

// Функция отрисовки линии
function conclusionLine(typeLine, lineX1, lineY1, lineX2, lineY2){


    var line = document.createElementNS("http://www.w3.org/2000/svg", "line");

    line.setAttribute("x1",lineX1);
    line.setAttribute("y1",lineY1);
    line.setAttribute("x2",lineX2);
    line.setAttribute("y2",lineY2);
    line.setAttribute("stroke-width","2");

    if(typeLine == 'red'){

        line.setAttribute("class","line line-red");

    }else{

        line.setAttribute("class","line line-blue");

    }


    $('#svg-line line:last').after(line);


}


function caruselPrevNext(aThis, direction){

    if(direction == 'prev'){

        var sign = -400;

    }else if(direction == 'next'){

        var sign = 400;

    }

    var vCarusel = $(aThis).parents('.blockPsCarusel').find('.carousel');

    vCarusel.animate({scrollLeft: vCarusel.scrollLeft() + sign}, 200);

}

function messageClose(aThis){

    $(aThis).parents('.systemMessageError').remove();

}


function caruselClickActive(aThis){

    $(aThis).parent().find('.carousel-element').removeClass('active');
    $(aThis).addClass('active');


    var  caruselDataImg = $(aThis).find('img').attr('data-id');

    if(caruselDataImg == 13 || caruselDataImg == 14 || caruselDataImg == 16 || caruselDataImg == 16){

        $('.blockVvodaTeplovich').css('display','block');

    }else{

        $('.blockVvodaTeplovich').css('display','none');

    }


}

function inputCheck(aThis){

    $(aThis).val($(aThis).val().replace(/[^0-9.]/g, ''));

}


function modalSave(aThis){

    // Проверка полей
    var typeblockSave = $('.box-modal .modalCharacteristic').attr('data-type'),

        errorInputStatus = true;

    $('.box-modal .errorText').css('display','none');

    $('.box-modal .errorInput').removeClass('errorInput');

    if(typeblockSave == 'block_vvoda') {


        $('.box-modal input[name="q"], .box-modal input[name="t1"], .box-modal input[name="t2"], .box-modal input[name="p1"], .box-modal input[name="p2"]').each(function(){


            if($(this).val() == ''){

                $(this).addClass('errorInput');

                errorInputStatus = false;

            }


        });


    }

    if(typeblockSave == 'block_otopleniya') {

        $('.box-modal input[name="q"], .box-modal input[name="t1"], .box-modal input[name="t2"], .box-modal input[name="p1"], .box-modal input[name="p2"], .box-modal input[name="t11"], .box-modal input[name="t21"], .box-modal input[name="dp"], .box-modal input[name="h"]').each(function(){


            if($(this).val() == ''){

                $(this).addClass('errorInput');

                errorInputStatus = false;

            }


        });


    }


    if(typeblockSave == 'block_gvs') {

        $('.box-modal input[name="qGVS"], .box-modal input[name="GVSt1"], .box-modal input[name="GVSt2"], .box-modal input[name="p1"], .box-modal input[name="p2"], .box-modal input[name="GVSt3"], .box-modal input[name="GVStv1"], .box-modal input[name="pb1"]').each(function(){


            if($(this).val() == ''){

                $(this).addClass('errorInput');

                errorInputStatus = false;

            }


        });

    }


    if(!errorInputStatus){

        $('.box-modal .errorText').css('display','block');

        return false;

    }



    $.arcticmodal('close');

    var dropData = $(aThis).parents('.box-modal').find('.blockPsCarusel .active img');
        dropDataImage = urlImageBlock + dropData.attr('data-image'),
        dropDataH1 = dropData.attr('data-h1'),
        dropDataH2 = dropData.attr('data-h2'),
        dropDataC1 = dropData.attr('data-c1'),
        dropDataC2 = dropData.attr('data-c2'),
        dropDataId = dropData.attr('data-id'),
        dropDataLine = dropData.attr('data-line'),
        dropDataType = dropData.attr('data-type'),
        dropDataLeft = dropData.attr('data-left'),
        dropDataT = dropData.attr('data-t'),
        dropDataPodpitka = dropData.attr('data-podpitka'),

        dropDataImg = '<img  class="blockImg" src="'+dropDataImage+'" data-t="'+dropDataT+'" data-podpitka="'+dropDataPodpitka+'" data-left="'+dropDataLeft+'" data-lile="'+dropDataLine+'" data-type="'+dropDataType+'" data-id="'+dropDataId+'" data-h1="'+dropDataH1+'" data-h2="'+dropDataH2+'" data-c1="'+dropDataC1+'" data-c2="'+dropDataC2+'" alt=""/>';


            inputData = "";

            if(dropDataType == 'block_vvoda') {

                var inputDataJSON = '{"' + dropDataType + '": {"q": "' + $('.box-modal input[name=q]').val() + '",';
                inputDataJSON += '"id": "' + $('.box-modal .carousel-element.active').attr('data-id') + '",';
                inputDataJSON += '"qType": "' + $('.box-modal select[name=qType] option:selected').text() + '",';
                inputDataJSON += '"uzel_ucheta": "' + $('.box-modal #uzel_ucheta').prop("checked") + '",';
                inputDataJSON += '"regul_perepad": "' + $('.box-modal #regul_perepad').prop("checked") + '",';
                inputDataJSON += '"bal_klapan": "' + $('.box-modal #bal_klapan').prop("checked") + '",';
                inputDataJSON += '"shkafPodpitka": "' + $('.box-modal #shkafPodpitka').prop("checked") + '",';
                inputDataJSON += '"regul_perepad": "' + $('.box-modal #regul_perepad').prop("checked") + '",';
                inputDataJSON += '"shkafDopObor": "' + $('.box-modal #shkafDopObor').prop("checked") + '",';
                inputDataJSON += '"skafDi": "' + $('.box-modal input[name=skafDi]:checked').val() + '",';
                inputDataJSON += '"skafDo": "' + $('.box-modal input[name=skafDo]:checked').val() + '"';
                inputDataJSON += '}}';

                inputData = '<span class="spanHidden" data-type="' + dropDataType + '">' + inputDataJSON + '</span>';

                if($('#i-q').text() != ''){

                    var jsonQ = $.parseJSON($('#i-q').text());

                }


                var inputDataJSON = '{"iQ": {';

                if($('#i-q').text() != '') {

                    if (jsonQ.iQ) {

                        if (jsonQ.iQ.q) {
                        } else {

                            inputDataJSON += '"q": "' + $('.box-modal input[name=q]').val() + '"';

                        }

                    } else {

                        inputDataJSON += '"q": "' + $('.box-modal input[name=q]').val() + '"';

                    }

                }else{

                    inputDataJSON += '"q": "' + $('.box-modal input[name=q]').val() + '"';

                }


                inputDataJSON += '}}';

                $('#i-q').text(inputDataJSON);



            }


            if(dropDataType == 'block_otopleniya') {

                var inputDataJSON = '{"' + dropDataType + '": {';
                inputDataJSON += '"q": "' + $('.box-modal input[name=q]').val() + '",';
                inputDataJSON += '"id": "' + $('.box-modal .carousel-element.active').attr('data-id') + '",';
                inputDataJSON += '"qType": "' + $('.box-modal select[name=qType] option:selected').text() + '",';
                inputDataJSON += '"kontr": "' + $('.box-modal select[name=kontr] option:selected').text() + '",';
                inputDataJSON += '"regKlapan": "' + $('.box-modal select[name=regKlapan] option:selected').text() + '",';
                inputDataJSON += '"t11": "' + $('.box-modal select[name=t11] option:selected').text() + '",';
                inputDataJSON += '"t21": "' + $('.box-modal select[name=t21] option:selected').text() + '",';
                inputDataJSON += '"dpType": "' + $('.box-modal select[name=dpType] option:selected').text() + '",';
                inputDataJSON += '"shkafAvtomatiky": "' + $('.box-modal select[name=shkafAvtomatiky] option:selected').text() + '",';
                inputDataJSON += '"typeBlock": "' + $('.box-modal input[name=typeBlock]:checked').val() + '",';
                inputDataJSON += '"pch": "' + $('.box-modal input[name=pch]:checked').val() + '",';
                inputDataJSON += '"dp": "' + $('.box-modal input[name=dp]').val() + '",';
                inputDataJSON += '"h": "' + $('.box-modal input[name=h]').val() + '"';
                inputDataJSON += '}}';

                inputData = '<span class="spanHidden" data-type="' + dropDataType + '">' + inputDataJSON + '</span>';

            }


            if(dropDataType == 'block_podpitky') {

                var inputDataJSON = '{"' + dropDataType + '": {';
                inputDataJSON += '"povNasos": "' + $('.box-modal select[name=povNasos] option:selected').text() + '",';
                inputDataJSON += '"id": "' + $('.box-modal .carousel-element.active').attr('data-id') + '",';
                inputDataJSON += '"klapanSol": "' + $('.box-modal select[name=klapanSol] option:selected').text() + '",';
                inputDataJSON += '"rasshirBak": "' + $('.box-modal select[name=rasshirBak] option:selected').text() + '",';
                inputDataJSON += '"nasosStan": "' + $('.box-modal input[name=nasosStan]:checked').val() + '"';
                inputDataJSON += '}}';

                inputData = '<span class="spanHidden" data-type="' + dropDataType + '">' + inputDataJSON + '</span>';

            }


            if(dropDataType == 'block_gvs') {

                var inputDataJSON = '{"' + dropDataType + '": {';
                inputDataJSON += '"qGVS": "' + $('.box-modal input[name=qGVS]').val() + '",';
                inputDataJSON += '"id": "' + $('.box-modal .carousel-element.active').attr('data-id') + '",';
                inputDataJSON += '"qSelGVS": "' + $('.box-modal select[name=qSelGVS] option:selected').text() + '",';
                inputDataJSON += '"GVSgt1": "' + $('.box-modal input[name=GVSgt1]').val() + '",';
                inputDataJSON += '"GVSt1": "' + $('.box-modal input[name=GVSt1]').val() + '",';
                inputDataJSON += '"GVSt2": "' + $('.box-modal input[name=GVSt2]').val() + '",';
                inputDataJSON += '"GVSt3": "' + $('.box-modal input[name=GVSt3]').val() + '",';
                inputDataJSON += '"GVStv1": "' + $('.box-modal input[name=GVStv1]').val() + '",';
                inputDataJSON += '"pb1": "' + $('.box-modal input[name=pb1]').val() + '",';
                inputDataJSON += '"pb1SelGVS": "' + $('.box-modal select[name=pb1SelGVS] option:selected').text() + '",';
                inputDataJSON += '"kontrGVS": "' + $('.box-modal select[name=kontrGVS] option:selected').text() + '",';
                inputDataJSON += '"regKlapanGVS": "' + $('.box-modal select[name=regKlapanGVS] option:selected').text() + '",';
                inputDataJSON += '"typeNag": "' + $('.box-modal input[name=typeNag]:checked').val() + '",';
                inputDataJSON += '"shemaPodkl": "' + $('.box-modal input[name=shemaPodkl]:checked').val() + '",';
                inputDataJSON += '"shkafAvtomatikyGVS": "' + $('.box-modal select[name=shkafAvtomatikyGVS] option:selected').text() + '",';
                inputDataJSON += '"dhodDBlockBOV": "' + $('.box-modal #dhodDBlockBOV').prop("checked") + '"';
                inputDataJSON += '}}';


                inputData = '<span class="spanHidden" data-type="' + dropDataType + '">' + inputDataJSON + '</span>';

            }


            if($('.box-modal select[name=t1] option:selected').text()) {

                var inputDataJSON = '{"iData": {';
                inputDataJSON += '"t1": "' + $('.box-modal select[name=t1] option:selected').text() + '",';
                inputDataJSON += '"t2": "' + $('.box-modal input[name=t2]').val() + '"';
                inputDataJSON += '}}';

                $('#i-data').text(inputDataJSON);

            }


            if($('.box-modal input[name=p1]').val()) {

                var inputDataJSON = '{"iDataGeneral": {';
                inputDataJSON += '"p1": "' + $('.box-modal input[name=p1]').val() + '",';
                inputDataJSON += '"p2": "' + $('.box-modal input[name=p2]').val() + '",';
                inputDataJSON += '"p1Type": "' + $('.box-modal select[name=p1Type] option:selected').text() + '",';
                inputDataJSON += '"p2Type": "' + $('.box-modal select[name=p2Type] option:selected').text() + '"';
                inputDataJSON += '}}';

                $('#i-data-general').text(inputDataJSON);

            }





        var modalAttr = '';

        if(dropDataType == 'block_otopleniya' || dropDataType == 'block_gvs'){

            modalAttr = 'onclick="modalGenerator(\''+dropDataType+'\',\'\',\'\',this)"';

        }


        if(dropDataType == 'block_podpitky' && $(dropThis).prev().find('img').attr('data-lile') == '0') {


            systemMessage('error', 'Необходимо выбрать блок отопления с теплообменником');

            return false;


        }


        $(dropThis).html($('<div '+modalAttr+' class="dropBlock draggable"><h3 data-scid="'+ scetcikI +'"><div class="divBotPosition"><i class="fa fa-pencil"></i> <i class="fa fa-trash" onclick="deleteBlock(this)"></i></div>' + $(aThis).parents('.box-modal').find('h3').html() + '</h3>' + dropDataImg + inputData + '</div>'));

        scetcikI ++;


        if(dropDataType == 'block_otopleniya'){

            var pr = $(".box-modal .modalCharacteristic input[name=typeBlock]:checked").attr("data-name");



            if (!$(dropThis).next().html() && $(dropThis).next().html() != '') {

                $('.heatNetworkCls').each(function(){


                    $(this).find('.thermalNetwork').text('Система ' + $(this).parents('.blockSystem').prev().find('h3').text().substr(6));


                });



            }else{


                $(dropThis).next('.blockSystem').html('<div class="tpBlockStl"><div class="heatNetworkCls">' +
                    '                                <div class="joiningFeederLine"><div class="heatLine"></div><i>Присоединение к подающему</i> <span>трубопроводу по мосту</span></div>' +
                    '                                <span data-scid="' + scetcikI + '" class="thermalNetwork">Система ' + pr + '</span>' +
                    '                                <div class="attachReverseLine"><div class="coldLine"></div><i>Присоединение к обратному</i> <span>трубопроводу по мосту</span></div>' +
                    '                            </div></div>');


            }




        }


    if(dropDataType == 'block_vvoda'){



        if(dropDataLine == '1'){

            $('.blockPodpitkyList, .blockOtopleniyaList').css('display','table');

        }else{

            $('.blockPodpitkyList, .blockOtopleniyaList').css('display','none');

            $('.blockPodpitkyList').addClass('blockActive').html('<div class="insertBlockPodpitky"><div class="iconHover"></div><span>Вставить блок подпитки</span></div>');

            $('.blockOtopleniyaList').css('display','table');

        }


        $(dropThis).removeClass('blockActiveHover');

        $('.blockLeftCont[data-type=block_vvoda]').css('display','none');

        $('#blockLeft').getNiceScroll().resize();


    }


    if(dropDataType == 'block_otopleniya') {


        $(dropThis).removeClass('blockActive');


        if($(dropThis).parents('.strongBlock').next().hasClass('strongBlockTop')){


            $(dropThis).parents('.strongBlock').next().remove();


        }else{


            if($(dropThis).parents('.strongBlockTop').next().hasClass('blockPodpitkyList') && $(dropThis).find('img').attr('data-lile') == '0'){


                $(dropThis).parents('.strongBlockTop').next().css('display','none').addClass('blockActive').html('<div class="insertBlockPodpitky"><div class="iconHover"></div><span>Вставить блок подпитки</span></div>');


            }else{

                if($(dropThis).find('img').attr('data-lile') == '1'){

                    $(dropThis).parents('.strongBlockTop').next().css('display','table');

                }

            }



        }




        if(dropDataLine == 1) {


            $(dropThis).parents('.strongBlock').after('<div class="strongBlock strongBlockTop"><div class="MapBlockDrop blockPodpitkyList blockActive"  onclick="modalGenerator(\'block_podpitky\',\'\',\'\',this)">' + blockPodpitkyList + '</div></div>');

            dropFn();


        }


    }


    if(dropDataType == 'block_podpitky') {


        $(dropThis).removeClass('blockActive');


    }


    if(dropDataType == 'block_gvs') {


        $('.blockLeftCont[data-type=block_gvs]').css('display','none');

        $('#blockLeft').getNiceScroll().resize();


        if($('.strongBlock:last').next().hasClass('MapBlockDrop')){

            $('.strongBlock:last').next().remove();

        }

        $('.strongBlock:last').after('<div class="MapBlockDrop">'+$(dropThis).html()+'</div>');

        $(dropThis).html('');

        statusGVS = 0;


    }


    var loadImg = new Image();
    loadImg.src = dropDataImage;

    loadImg.onload = function() {

        conclusionLineStart();

    }



    blockVvodaType = 0;


    $('.strongBlock .blockImg, .blockOtopleniyaList img').each(function(){


        if($(this).attr('data-lile') == 1){


            blockVvodaType = 1;


        }else{


            if(blockVvodaType != 1){


                blockVvodaType = 2;


            }


        }


    });


    if(blockVvodaType == 1 && $('.blockVvodaDS img').attr('data-lile') != 1){


        systemMessage('info', 'Вставьте блок отопления с дополнительной линией подпитки');

        $('.blockVvodaDS').html(cloneBlockVvoda);

        dropFn();

        $('.blockLeftCont[data-type=block_vvoda]').css('display','block');

        $('#blockLeft').getNiceScroll().resize();


    }else if(blockVvodaType == 2 && $('.blockVvodaDS img').attr('data-lile') == 1){


        systemMessage('info', 'Вставьте блок отопления БЕЗ дополнительной линией подпитки');

        $('.blockVvodaDS').html(cloneBlockVvoda);

        dropFn();

        $('.blockLeftCont[data-type=block_vvoda]').css('display','block');

        $('#blockLeft').getNiceScroll().resize();


    }

    var jsonQSum = 0;

    $('.MapBlockDropList span[data-type="block_otopleniya"], .MapBlockDropList span[data-type="block_gvs"]').each(function(){

        var jsonq = $.parseJSON($(this).text());


        if(jsonq.block_otopleniya){

            jsonQSum += parseInt(jsonq.block_otopleniya.q);

        }else if(jsonq.block_gvs){

            jsonQSum += parseInt(jsonq.block_gvs.qGVS);

        }


    });

    // var jsonq = $.parseJSON($(this).text());
    //
    // jsonQSum += parseInt(jsonq.block_otopleniya.q);

    if(jsonQSum){

        $('.qSum').attr('disabled','disabled');

        var inputDataJSON = '{"iQ": {';
        inputDataJSON += '"q": "' + jsonQSum + '"';
        inputDataJSON += '}}';

        $('#i-q').text(inputDataJSON);

    }


    dropThis = "";


}



function removeMap(){

    $('.MapBlockDrop').empty();
    $('#svg-line .line').remove();
    thisBlock = '';

    $('.blockLeftCont').css('display','block');
    $('#blockLeft').getNiceScroll().resize();

    $('.blockOtopleniyaList, .blockPodpitkyList').addClass('blockActive').css('display','none');

    $('.blockOtopleniyaList').html('<div class="insertBlockOtopleniya"><div class="iconHover"></div><span>Вставить блок отопления</span></div>');
    $('.blockPodpitkyList').html('<div class="insertBlockPodpitky"><div class="iconHover"></div><span>Вставить блок подпитки</span></div>');
    $('.MapStr .blockVvodaDS').html(cloneBlockVvoda);


    if($('.strongBlock:last').next().hasClass('MapBlockDrop')){

        $('.strongBlock:last').next().remove();

    }

    if($('.strongBlock:last').hasClass('strongBlockTop')){

        $('.strongBlock:last').remove();

    }

    $('.blockSystem').html('');


    $('.blockLeftCont[data-type=block_vvoda], .blockLeftCont[data-type=block_gvs]').css('display','block');

    $('#blockLeft').getNiceScroll().resize();

    statusGVS = 1;

    conclusionLineStart();

}


function deleteBlock(aThis){

    statusDelete = false;

    if($(aThis).parents('.draggable').find('img').attr('data-type') == 'block_vvoda'){

        $(aThis).parents('.blockVvodaDS').html(cloneBlockVvoda);

        dropFn();

        $('.blockLeftCont[data-type=block_vvoda]').css('display','block');

        $('#blockLeft').getNiceScroll().resize();


    }else if ($(aThis).parents('.draggable').find('img').attr('data-type') == 'block_otopleniya'){


        if($(aThis).parents('.blockOtopleniyaList').hasClass("blockOtopleniyaList")){

            $(aThis).parents('.blockOtopleniyaList').addClass('blockActive').html(cloneBlockOtopleniyaList);

            dropFn();

        }else{

            if($(aThis).parents('.strongBlock').next().hasClass('strongBlockTop')){


                $(aThis).parents('.strongBlock').next().remove();


            }

            $(aThis).parents('.MapBlockDrop').html('');



        }


        $('.blockSystem').each(function(){


            if($(this).html() != ''){


                if(!$(this).prev().find('img').attr('src')){

                    $(this).html('');

                }


            }


        });


    }else if ($(aThis).parents('.draggable').find('img').attr('data-type') == 'block_podpitky'){

        $(aThis).parents('.blockPodpitkyList').addClass('blockActive').html(blockPodpitkyList);

        dropFn();




    }else if ($(aThis).parents('.draggable').find('img').attr('data-type') == 'block_gvs'){

        $(aThis).parents('.MapBlockDrop').remove();

        $('.blockLeftCont[data-type=block_gvs]').css('display','block');

        $('#blockLeft').getNiceScroll().resize();

        statusGVS = 1;


    }

    $('#svg-line .line').remove();

    conclusionLineStart();


}


// Чекбоксы в блоках ввода
function check(aThis){

    var arrFr = new Array(),
        du1, du2, du3;

    $('.box-modal .carousel-element').css('display','none');

    if($('.box-modal #uzel_ucheta').prop("checked")) {

        du1 = '[data-uzel_ucheta="1"]';

    }

    if($('.box-modal #regul_perepad').prop("checked")) {

        du2 = '[data-regul_perepad="1"]';

    }

    if($('.box-modal #bal_klapan').prop("checked")) {

        du3 = '[data-bal_klapan="1"]';

    }


    $('.box-modal .carousel-element'+du1+du2+du3).css('display','table-cell');



    adapCarusel();

}


function typeNag(aThis){


    $('.box-modal  .vhodDanie input[name=typeNag]').each(function(){


        if($(this).prop("checked")){


            if($(this).val() == '1'){

                $('.box-modal #rd1').css('display','inline-block');
                $('.box-modal #rd2').css('display','none');

            }else{

                $('.box-modal #rd1').css('display','none');
                $('.box-modal #rd2').css('display','inline-block');

            }


        }


    });


}


function shPodkl(aThis){


        if($(aThis).prop("checked")){


            if($(aThis).val() == '2'){


                $('.box-modal .carousel-element[data-id="17"]').css('display','none');
                $('.box-modal .carousel-element[data-id="18"]').css('display','table-cell');


            }else if($(aThis).val() == '1'){


                $('.box-modal .carousel-element[data-id="17"], .box-modal .carousel-element[data-id="18"]').css('display','table-cell');


            }else if($(aThis).val() == '3'){


                $('.box-modal .carousel-element[data-id="17"]').css('display','table-cell');
                $('.box-modal .carousel-element[data-id="18"]').css('display','none');


            }


        }


}


function typeBlock(){


    $('.box-modal  .vhodDanie input[name=typeBlock]').each(function(){


        if($(this).prop("checked")){


            var dataName = $(this).attr('data-name');

            $('.box-modal .modalCharacteristic[data-type=block_otopleniya]').prev('h3').text('Блок '+ dataName);
            $('.box-modal #t11').attr('tooltip','Температура в подающем трубопроводе ' + dataName);
            $('.box-modal #t21').attr('tooltip','Температура в обратном трубопроводе ' + dataName);
            $('.box-modal #dp').attr('tooltip','Потери давления в системе ' + dataName);

            if($(this).val() == '1'){

                $('.box-modal #hVs').attr('tooltip','Высота здания');

            }else if($(this).val()  == '2'){

                $('.box-modal #hVs').attr('tooltip','Максимальная высота установки, калорифера');

            }else if($(this).val()  == '3'){

                $('.box-modal #hVs').attr('tooltip','Максимальная высота установки технологического оборудования');

            }


        }


    });


}


function sravOtop(){


    var t1 = $('.box-modal select[name=t1] option:selected').text(),
        t11 = $('.box-modal select[name=t11] option:selected').text();

    if(t1 && t11){

        if(t1 == t11){

            $('.box-modal .carousel-element[data-id=20]').css('display','none');
            $('.box-modal .carousel-element[data-id=21]').css('display','table-cell');


        }else{


            $('.box-modal .carousel-element[data-id=20]').css('display','table-cell');
            $('.box-modal .carousel-element[data-id=21]').css('display','none');


        }

    }else{


        $('.box-modal .carousel-element[data-id=20], .box-modal .carousel-element[data-id=21]').css('display','table-cell');


    }

    adapCarusel();

}



// --- Расчеты ---

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
                res += '<div><a class="btnClassic" href="/selection/getPZ/'+json["group"]["id"]+'">Получить ПЗ</a><a class="btnClassic" href="/catalog_v2/specification/selectionGroup/'+json["group"]["id"]+'">Получить общую спецификацию</a><a class="btnClassic" href="/download/'+json["group"]["aid_scheme"]+'">Получить общую схему</a></div>';
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

function calculateCost(){

    if(!$('.blockVvodaDS img').attr('src')){


        systemMessage('error', 'Вставьте блок ввода');

        return false;


    }

    var mStatus = 0;

    $('.MapBlockDropList .dropBlock img').each(function(){

        if($(this).attr('src')){

            mStatus = 1;

        }

    });

    if(mStatus == 0){


        systemMessage('error', 'Вставьте блок отопления или блок ГВС');

        return false;


    }

    mStatus = 0;


    if(!$('.dropBlock img').attr('src')){


        systemMessage('error', 'Вставьте блок ввода');

        return false;


    }


    calculateCostZp();


}

function selT11(aThis){

    var selectedOption = aThis.options[aThis.selectedIndex];

    if(selectedOption.value == '95' ){

        $('.box-modal .modalCharacteristic[data-type=block_otopleniya] select[name=t21] option:contains("70")').prop('selected', true);

    }else if(selectedOption.value == '90' ){

        $('.box-modal .modalCharacteristic[data-type=block_otopleniya] select[name=t21] option:contains("65")').prop('selected', true);

    }else if(selectedOption.value == '85' ){

        $('.box-modal .modalCharacteristic[data-type=block_otopleniya] select[name=t21] option:contains("60")').prop('selected', true);

    }else if(selectedOption.value == '80' ){

        $('.box-modal .modalCharacteristic[data-type=block_otopleniya] select[name=t21] option:contains("60")').prop('selected', true);

    }

    return false;

}

function selT21(aThis){

    var t11Select = $('.box-modal select[name=t11] option:selected').text();

    var selectedOption = aThis.options[aThis.selectedIndex];

    if(selectedOption.value == '70' ){


        $('.box-modal .modalCharacteristic[data-type=block_otopleniya] select[name=t11] option:contains("95")').prop('selected', true);


    }else if(selectedOption.value == '65' ){


        $('.box-modal .modalCharacteristic[data-type=block_otopleniya] select[name=t11] option:contains("90")').prop('selected', true);


    }else if(selectedOption.value == '60' ){

        if(t11Select != '85' && t11Select != '80' ){


            $('.box-modal .modalCharacteristic[data-type=block_otopleniya] select[name=t11] option:contains("85")').prop('selected', true);


        }

    }

    return false;
}

// Показать скрыть блок с теплообменником при температуре 95
function selT1(aThis){

    var t11Select = $('.box-modal select[name=t11] option:selected').text();

    var selectedOption = aThis.options[aThis.selectedIndex];

    if(selectedOption.value == '95' ){

        $('.box-modal .carousel-element[data-id="19"]').css('display','none');

    }else{

        $('.box-modal .carousel-element[data-id="19"]').css('display','table-cell');

    }

}



// --- Работа со шкафами ---

function shkafDpInter(aThis){

    if($(aThis).prop("checked")){


        $('.box-modal #skafDi').css('display', 'inline-block');


    }else{


        $('.box-modal #skafDi').css('display', 'none');


    }

}


function shkafDpOborudovanie(aThis){


    if($(aThis).prop("checked")){


        $('.box-modal #shkafDpo').css('display', 'inline-block');


    }else{


        $('.box-modal #shkafDpo').css('display', 'none');


    }


}


function qgvs(aThis){

    $('.box-modal .carousel-element').css('display', 'table-cell');

    var qblockOtopleniyaJson,
        qgvs = 0;

    $('.spanHidden[data-type="block_otopleniya"]').each(function(){
        qblockOtopleniyaJson = $.parseJSON($(this).text());

        qgvs += Number(qblockOtopleniyaJson.block_otopleniya.q);

    });


    if($(aThis).val() != ''){


        if(0.2 <= (Number($(aThis).val()) / qgvs) && (Number($(aThis).val()) / qgvs) <= 1){


            $('.box-modal .modalCharacteristic[data-type=block_gvs] input[name=shemaPodkl][value=3]').attr('checked',true).trigger('change');


        }


    }


}

function prNasos(aThis){


    var prH = $('.box-modal .modalCharacteristic[data-type="block_otopleniya"] input[name="h"]'),
        prP2 = $('.box-modal .modalCharacteristic[data-type="block_otopleniya"] input[name="p2"]'),
        prP2Tp = 0;

        if(prH.val() != '' && prP2.val() != ''){

            if($('.box-modal select[name=p2Type] option:selected').text() == 'кгс/см²'){

                prP2Tp = Number(prP2.val()) * 10;

            }else if($('.box-modal select[name=p1Type] option:selected').text() == 'МПа'){

                prP2Tp = Number(prP2.val()) * 100;

            }else{

                prP2Tp = Number(prP2.val());

            }



            if(Number(prH.val()) - prP2Tp > 5){

                //alert(1);

                $('#st-i-nasos').val('1');

            }else{

                //alert(2);

                $('#st-i-nasos').val('0');

            }


        }


}






// Функция вывода объектов на экран
function alertObj(obj) {
    var str = "";
    for(k in obj) {
        str += k+": "+ obj[k]+"\r\n";
    }
    alert(str);
}