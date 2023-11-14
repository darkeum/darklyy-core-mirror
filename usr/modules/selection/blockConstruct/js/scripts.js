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
    scetcikI = 0,
    inputOtopleniebvNasos = '';


var cloneBlockVvoda = $('.blockVvodaDS').html(),
    cloneBlockOtopleniyaList = $('.blockOtopleniyaList').html(),
    blockPodpitkyList = $('.blockPodpitkyList').html();

var reghimTp = $.cookie('reghim');

var first_error_text = $(".errorText").eq(0).html();



$(document).ready(function() {

    $('#loading').fadeOut(200);

    var mapCloneLast = $('.MapStr:last');

    for (var i = 0; i < 2; i++) {

        $('.MapStr .MapBlockImg:eq(1) .strongBlock:last').after('<div class="strongBlock"><div class="MapBlockDrop"></div><div class="blockSystem"></div></div>');

    }


    $('.MapStr:last').after('<div class="clear"></div>');



    $('.draggable').draggable({

        helper: "clone",

    });


    dropFn();



 
    $('.MapBlockDropList .MapBlockDrop').hover(

        function() {


            if ($(this).html() == '') {

                if (statusGVS) {

                    if ($('.MapBlockDropList .blockImg[data-type="block_otopleniya"]').length >= 2) {

                        var htmlMenu = '<div class="clickDpMenu"><span onclick="modalGenerator(\'block_gvs\',\'\',\'\',$(this).parents(\'.MapBlockDrop\'))">Вставить блок ГВС</span></div>';

                    } else {

                        var htmlMenu = '<div class="clickDpMenu"><span onclick="modalGenerator(\'block_otopleniya\',\'\',\'\',$(this).parents(\'.MapBlockDrop\'))">Вставить блок отопления</span><span onclick="modalGenerator(\'block_gvs\',\'\',\'\',$(this).parents(\'.MapBlockDrop\'))">Вставить блок ГВС</span></div>';

                    }


                } else {

                    var htmlMenu = '<div class="clickDpMenu"><span onclick="modalGenerator(\'block_otopleniya\',\'\',\'\',$(this).parents(\'.MapBlockDrop\'))">Вставить блок отопления</span></div>';


                }

                $(this).html('<div class="insertBlockNew insertBlockNull" onclick="menuType()">' + htmlMenu + '<div class="clickDp"><div class="iconHover"></div><span>Вставить блок</span></div></div>');

                $(this).addClass('blockActiveHover');

            }


        },
        function() {

            if ($(this).find('div').hasClass('insertBlockNull')) {

                $(this).removeClass('blockActiveHover');

                $(this).html('');

            }


            // if ($(this).find('div').hasClass('insertBlockNull')) {

            //     if (!$(this).parent().find('div').hasClass('mmc') || ($('.blockVvodaDS').find('div').find('div').hasClass('insertBlockNull')) || !$(this).parent().find('div').hasClass('insertBlockNull')) {

            //         $(this).removeClass('blockActiveHover');

            //         $(this).html('');
            //     }

            // }


        }

    );


    $('#blockLeft').niceScroll({
        cursorcolor: "#555",
        cursorborder: "0px solid #000",
        cursoropacitymin: 1,
        background: "#000",
        cursorminheight: 10,
        railalign: "left",
        railpadding: { top: 2, right: 2, left: 2, bottom: 2 }
    });

    //$.cookie('reghim', null, { expires: 7, path: '/' });

    reghim();

});


function reghim() {

    if ($.cookie('reghim') == null) {

        $('#blockOneRightWrap').css('display', 'none');
        $('#blockRightWrap').css('display', 'block');

    } else {

        $('#blockRightWrap').css('display', 'none');
        $('#blockOneRightWrap').css('display', 'block');

    }

}


function dropFn() {


    $('.mapOneBlockDrop').droppable({

        hoverClass: 'dropHere',

        drop: function(event, ui) {

            dropThis = $(this);
            dropUi = ui;


            var dataTypeDrop = $('img', dropUi.draggable).attr('data-type');

            modalGenerator(ui, dataTypeDrop, $(this));

        }

    });



    $('.MapBlockDrop').droppable({

        hoverClass: 'dropHere',

        drop: function(event, ui) {

            dropThis = $(this);
            dropUi = ui;


            var dataTypeDrop = $('img', dropUi.draggable).attr('data-type');


            // Работа с блоком ввода
            if (dataTypeDrop == 'block_vvoda' && $(this).hasClass('MapBlockDropInput')) {


                modalGenerator(ui, dataTypeDrop, $(this));


            } else {


                if (dataTypeDrop == 'block_vvoda' && !$(this).hasClass('MapBlockDropInput')) {


                    systemMessage('error', 'Вставьте блок ввода после тепловой сети');


                } else if (dataTypeDrop != 'block_vvoda' && $(this).hasClass('MapBlockDropInput')) {


                    systemMessage('error', 'Вставьте <span class="mesNgReg">' + $('h3', dropUi.draggable).html() + '</span> после блока ввода');


                } else {


                    if (dataTypeDrop != 'block_otopleniya' && $(this).hasClass('blockOtopleniyaList')) {


                        systemMessage('error', 'Вставьте блок отопления');


                    } else if (dataTypeDrop != 'block_podpitky' && $(this).hasClass('blockPodpitkyList')) {


                        systemMessage('error', 'Вставьте блок подпитки');


                    } else if (dataTypeDrop == 'block_podpitky' && !$(this).hasClass('blockPodpitkyList')) {

                        systemMessage('error', 'Вставьте блок подпитки под блок отопления');


                    } else {

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


    var proverkaNasosov = 0;

    $('.MapBlockDropList .spanHiddenNasos').each(function() {

        proverkaNasosov += parseInt($(this).text());

    });


    if ($('#st-i-nasos').val() == 1) {

        $('.box-modal .carousel-element[data-id=23]').css('display', 'none');

    }


    if (typeBlock == 'block_vvoda') {


        if ($('#st-i-nasos').val() == 1) {

            $('.box-modal .carousel-element[data-line=1]').css('display', 'none');
            $('.box-modal .carousel-element[data-nasos=1]').css('display', 'table-cell');

        }


        if (blockVvodaType == 1) {


            $('.box-modal .carousel-element[data-line=0]').css('display', 'none');


        } else if (blockVvodaType == 2) {


            $('.box-modal .carousel-element[data-line=1]').css('display', 'none');


        }


        if ($('.blockVvodaDS .insertBlockNew span').text() != 'Вставить блок ввода') {


            if (proverkaNasosov >= 2) {


                $('.box-modal .blockPsCarusel img').each(function() {


                    if ($(this).attr('data-line') == '1') {

                        $(this).attr('src', $(this).attr('src').replace('/block_vvoda/', '/block_vvoda_n/'));

                    }


                });

            } else {

                // $('.box-modal .blockPsCarusel .blockImg').attr('src', $('.box-modal .blockPsCarusel .blockImg').attr('src').replace('/block_vvoda_n/', '/block_vvoda/'));

            }


        }




    }
    check();

    typeBlock = $('.box-modal .modalCharacteristic').attr('data-type');

    var inputData = $(thisBPdp).find('span[data-type=' + typeBlock + ']').text();


    // Выставляем выделение карусели в окне
    if (thisBPdp.find('img').attr('data-id')) {


        $(".box-modal .carousel-element").removeClass('active');

        if ($(".box-modal .carousel-element[data-id=" + thisBPdp.find('img').attr('data-id') + "]:visible")) {

            $(".box-modal .carousel-element[data-id=" + thisBPdp.find('img').attr('data-id') + "]").addClass('active');

        } else {

            $(".box-modal .carousel-element:first").addClass('active');

        }



    } else {


        $(".box-modal .carousel-element:first").addClass('active');


    }



    if (typeBlock == 'block_podpitky') {


        blPodpitSogSp(0, 1);

        $('.box-modal .modalCharacteristic[data-type=block_podpitky] .nameTypeBlock').html('Выбран <b>' + thisBPdp.parents('.strongBlockTop').prev().find('h3').text() + '</b>');

        if ($('#st-i-nasos').val() == 0) {

            $('.box-modal .carousel-element[data-id="32"]').css('display', 'none');

        }


        inputOtopleniebvNasos = $(dropThis).parents('.strongBlock').prev().find('.spanHiddenNasos').text();

        if (inputOtopleniebvNasos == '1') {


        } else {

            /*$('.box-modal .carousel-element[data-id="23"]').css('display','none');
            $('.box-modal .carousel-element[data-id="22"]').css('display','table-cell');*/

        }

        $('.box-modal .carousel-element[data-id="23"]').css('display', 'table-cell');
        $('.box-modal .carousel-element[data-id="22"]').css('display', 'none');


        if (proverkaNasosov >= 2) {


            $('.box-modal .carousel-element[data-id="23"]').css('display', 'none');
            $('.box-modal .carousel-element[data-id="22"]').css('display', 'table-cell');

            $('.MapBlockDropList img[data-type="block_podpitky"]').attr('src', $('.MapBlockDropList img[data-type="block_podpitky"]').attr('src').replace('2.png', '1.png')).attr('data-id', '22').attr('data-left', '226');

            conclusionLineStart();


        } else {


            $('.box-modal .carousel-element[data-id="23"]').css('display', 'table-cell');
            $('.box-modal .carousel-element[data-id="22"]').css('display', 'none');


        }


    }

    if (typeBlock == 'block_otopleniya') {

        $('.box-modal .carousel-element[data-id="21"]').css('display', 'none');

    }


    if (inputData != '') {

        var inputDataJSON = $.parseJSON(inputData);


        if (typeBlock == 'block_vvoda') {

            ////////////////////
            /////////////////////


            $('.box-modal .modalCharacteristic[data-type=block_vvoda] select[name=qType] option:contains("' + inputDataJSON.block_vvoda.qType + '")').attr("selected", "selected").trigger('change');

            if (inputDataJSON.block_vvoda.uzel_ucheta == 'true') {
                $('.box-modal #uzel_ucheta').prop('checked', true).trigger('change');
            } else {
                $('.box-modal #uzel_ucheta').prop('checked', false).trigger('change');
            }

            if (inputDataJSON.block_vvoda.regul_dav_t1 == 'true') {
                $('.box-modal #regul_dav_t1').prop('checked', true).trigger('change');
            } else {
                $('.box-modal #regul_dav_t1').prop('checked', false).trigger('change');
            }

            if (inputDataJSON.block_vvoda.regul_dav_t2 == 'true') {
                $('.box-modal #regul_dav_t2').prop('checked', true).trigger('change');
            } else {
                $('.box-modal #regul_dav_t2').prop('checked', false).trigger('change');
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

            if (inputDataJSON.block_vvoda.regul_dav_t2 == 'true') {
                $('.box-modal #regul_dav_t2').prop('checked', true).trigger('change');
            } else {
                $('.box-modal #regul_dav_t2').prop('checked', false);
            }

            if (inputDataJSON.block_vvoda.shkafDopObor == 'true') {
                $('.box-modal #shkafDopObor').prop('checked', true).trigger('change');
            } else {
                $('.box-modal #shkafDopObor').prop('checked', false);
            }
            check($('.box-modal #regul_dav_t2'));

            $('.box-modal input[name=skafDi][value=' + inputDataJSON.block_vvoda.skafDi + ']').attr('checked', true).trigger('change');
            $('.box-modal input[name=skafDo][value=' + inputDataJSON.block_vvoda.skafDo + ']').attr('checked', true).trigger('change');

            $('.box-modal textarea[name=message]').val(inputDataJSON.block_vvoda.message);



        }


        if (typeBlock == 'block_otopleniya') {


            $('.box-modal .modalCharacteristic[data-type=block_otopleniya] input[name=q]').val(inputDataJSON.block_otopleniya.q);
            $('.box-modal .modalCharacteristic[data-type=block_otopleniya] input[name=dp]').val(inputDataJSON.block_otopleniya.dp);
            $('.box-modal .modalCharacteristic[data-type=block_otopleniya] input[name=h]').val(inputDataJSON.block_otopleniya.h);
            $('.box-modal .modalCharacteristic[data-type=block_otopleniya] input[name=typeBlock][value=' + inputDataJSON.block_otopleniya.typeBlock + ']').attr('checked', true).trigger('change');
            $('.box-modal .modalCharacteristic[data-type=block_otopleniya] input[name=pch][value=' + inputDataJSON.block_otopleniya.pch + ']').attr('checked', true).trigger('change');
            $('.box-modal .modalCharacteristic[data-type=block_otopleniya] select[name=qType] option:contains("' + inputDataJSON.block_otopleniya.qType + '")').attr("selected", "selected").trigger('change');
            $('.box-modal .modalCharacteristic[data-type=block_otopleniya] select[name=kontr] option:contains("' + inputDataJSON.block_otopleniya.kontr + '")').attr("selected", "selected").trigger('change');
            $('.box-modal .modalCharacteristic[data-type=block_otopleniya] select[name=regKlapan] option:contains("' + inputDataJSON.block_otopleniya.regKlapan + '")').attr("selected", "selected").trigger('change');
            $('.box-modal .modalCharacteristic[data-type=block_otopleniya] select[name=t11] option:contains("' + inputDataJSON.block_otopleniya.t11 + '")').attr("selected", "selected").trigger('change');
            $('.box-modal .modalCharacteristic[data-type=block_otopleniya] select[name=t21] option:contains("' + inputDataJSON.block_otopleniya.t21 + '")').attr("selected", "selected").trigger('change');
            $('.box-modal .modalCharacteristic[data-type=block_otopleniya] select[name=shkafAvtomatiky] option:contains("' + inputDataJSON.block_otopleniya.shkafAvtomatiky + '")').attr("selected", "selected").trigger('change');
            $('.box-modal .modalCharacteristic[data-type=block_otopleniya] select[name=dpType] option:contains("' + inputDataJSON.block_otopleniya.dpType + '")').attr("selected", "selected").trigger('change');
            $('.box-modal .modalCharacteristic[data-type=block_otopleniya] .bvNasos').val(inputDataJSON.block_otopleniya.bvNasos);
            $('.box-modal textarea[name=message]').val(inputDataJSON.block_otopleniya.message);

        }


        if (typeBlock == 'block_gvs') {


            $('.box-modal .modalCharacteristic[data-type=block_gvs] input[name=typeNag][value=' + inputDataJSON.block_gvs.typeNag + ']').attr('checked', true).trigger('change');
            $('.box-modal .modalCharacteristic[data-type=block_gvs] select[name=qSelGVS] option:contains("' + inputDataJSON.block_gvs.qSelGVS + '")').attr("selected", "selected").trigger('change');
            $('.box-modal .modalCharacteristic[data-type=block_gvs] input[name=qGVS]').val(inputDataJSON.block_gvs.qGVS);
            $('.box-modal .modalCharacteristic[data-type=block_gvs] input[name=GVSgt1]').val(inputDataJSON.block_gvs.GVSgt1);
            $('.box-modal .modalCharacteristic[data-type=block_gvs] input[name=GVSt1]').val(inputDataJSON.block_gvs.GVSt1);
            $('.box-modal .modalCharacteristic[data-type=block_gvs] input[name=GVSt2]').val(inputDataJSON.block_gvs.GVSt2);
            $('.box-modal .modalCharacteristic[data-type=block_gvs] input[name=GVSt3]').val(inputDataJSON.block_gvs.GVSt3);
            $('.box-modal .modalCharacteristic[data-type=block_gvs] input[name=GVStv1]').val(inputDataJSON.block_gvs.GVStv1);
            $('.box-modal .modalCharacteristic[data-type=block_gvs] input[name=pb1]').val(inputDataJSON.block_gvs.pb1);
            $('.box-modal .modalCharacteristic[data-type=block_gvs] select[name=pb1SelGVS] option:contains("' + inputDataJSON.block_gvs.pb1SelGVS + '")').attr("selected", "selected").trigger('change');
            $('.box-modal .modalCharacteristic[data-type=block_gvs] input[name=shemaPodkl][value=' + inputDataJSON.block_gvs.shemaPodkl + ']').attr('checked', true).trigger('change');
            $('.box-modal .modalCharacteristic[data-type=block_gvs] select[name=kontrGVS] option:contains("' + inputDataJSON.block_gvs.kontrGVS + '")').attr("selected", "selected").trigger('change');
            $('.box-modal .modalCharacteristic[data-type=block_gvs] select[name=regKlapanGVS] option:contains("' + inputDataJSON.block_gvs.regKlapanGVS + '")').attr("selected", "selected").trigger('change');
            $('.box-modal .modalCharacteristic[data-type=block_gvs] select[name=shkafAvtomatikyGVS] option:contains("' + inputDataJSON.block_gvs.shkafAvtomatikyGVS + '")').attr("selected", "selected").trigger('change');
            $('.box-modal textarea[name=message]').val(inputDataJSON.block_gvs.message);
            // alert(inputDataJSON.block_gvs.message);


            if (inputDataJSON.block_gvs.dhodDBlockBOV == 'true') {
                $('.box-modal #dhodDBlockBOV').prop('checked', true);
            } else {
                $('.box-modal #dhodDBlockBOV').prop('checked', false);
            }


        }


        if (typeBlock == 'block_podpitky') {


            $('.box-modal .modalCharacteristic[data-type=block_podpitky] input[name=nasosStan][value=' + inputDataJSON.block_podpitky.nasosStan + ']').attr('checked', true).trigger('change');
            $('.box-modal .modalCharacteristic[data-type=block_podpitky] select[name=povNasos] option:contains("' + inputDataJSON.block_podpitky.povNasos + '")').attr("selected", "selected").trigger('change');
            $('.box-modal .modalCharacteristic[data-type=block_podpitky] select[name=klapanSol] option:contains("' + inputDataJSON.block_podpitky.klapanSol + '")').attr("selected", "selected").trigger('change');
            $('.box-modal .modalCharacteristic[data-type=block_podpitky] select[name=rasshirBak] option:contains("' + inputDataJSON.block_podpitky.rasshirBak + '")').attr("selected", "selected").trigger('change');
            $('.box-modal textarea[name=message]').val(inputDataJSON.block_podpitky.message);


        }


    }

    if ($('#i-data').text() != '') {

        var inputIDataJSON = $.parseJSON($('#i-data').text());

    }


    if ($('#i-q').text() != '') {

        var jsonQ = $.parseJSON($('#i-q').text());

    }


    if (jsonQ) {

        $('.box-modal .modalCharacteristic[data-type=block_vvoda] input[name=q]').val(jsonQ.iQ.q);

    }




    if ((typeBlock == 'block_vvoda' && $('#i-data').text() != '') || (typeBlock == 'block_otopleniya' && $('#i-data').text() != '')) {


        $('.box-modal input[name=t1]').val(inputIDataJSON.iData.t1);
        $('.box-modal input[name=t2]').val(inputIDataJSON.iData.t2);
        console.log(inputIDataJSON.iData);

        //  $('.box-modal select[name=t1] option:contains("' + inputIDataJSON.iData.t1 + '")').attr("selected", "selected").trigger('change');


    }

    if (typeBlock == 'block_otopleniya' && $('#i-data').text() != '') {
        if($('span[data-type="block_otopleniya"]').length <= 0) {
            // $('.box-modal select[name=bov_type]').val(inputIDataJSON.iData.t1);
        var inputDataVvod = $('span[data-type="block_vvoda"]').text();

        var inputDataJSONVvod = JSON.parse(inputDataVvod);
         
        console.log('Изменить тип схемы блока отопления');
        console.log(inputDataJSONVvod.block_vvoda.hasPodpitka);
        if(inputDataJSONVvod.block_vvoda.hasPodpitka == 0) {
            $('.box-modal select[name="bov_type"]').val('2');
        } else {
            $('.box-modal select[name="bov_type"]').val('1');
        }
       
        $('.box-modal select[name="bov_type"]').prop('disabled', 'disabled');
        check($('.box-modal select[name="bov_type"]'));
        }
        
    }


    if ($('#i-data-general').text() != '') {

        var inputIDataGeneralJSON = $.parseJSON($('#i-data-general').text());

        $('.box-modal input[name=p1]').val(inputIDataGeneralJSON.iDataGeneral.p1); //.attr('disabled', 'disabled')
        $('.box-modal input[name=p2]').val(inputIDataGeneralJSON.iDataGeneral.p2); //.attr('disabled', 'disabled')

        $('.box-modal select[name="p1Type"] option:contains("' + inputIDataGeneralJSON.iDataGeneral.p1Type + '")').attr("selected", "selected").trigger('change');
        $('.box-modal select[name="p2Type"] option:contains("' + inputIDataGeneralJSON.iDataGeneral.p2Type + '")').attr("selected", "selected").trigger('change');


    }


    adapCarusel();

}


function adapCarusel() {


    var widthImg = 0;


    $('.box-modal .carousel-element img').each(function(indx) {

        var loadImg = new Image();

        loadImg.src = $(this).attr('src');

        loadImg.onload = function() {

            // Расчитываем размеры карусели
            widthImg += loadImg.width;

            if ($('.box-modal .carousel-element:visible img').length - 1 == indx) {


                if ($('.box-modal').width() < widthImg) {


                    $('.carouselPrev, .carouselNext').css('display', 'block');
                    $('.carousel').removeClass('carouselLine');


                } else {


                    $('.carouselPrev, .carouselNext').css('display', 'none');
                    $('.carousel').addClass('carouselLine');


                }

            }


        }


    });


}


function menuType() {

    $('.clickDp').css('display', 'none');
    $('.clickDpMenu').fadeIn(200);

}


function varTime() {

    statusDelete = true;

}


// Генерация системных сообщений
function systemMessage(typeWindow, messageText) {

    if (typeWindow == 'error') {


        var messageHtml = '<div id="MesIndex' + systemMessageIndex + '" class="systemMessageError">';
        messageHtml += '<i class="fa fa-times" onclick="messageClose(this)" aria-hidden="true"></i>';
        messageHtml += '<div class="systemMessageDiv">';
        messageHtml += '<div class="systemMessageIcon"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i></div>';
        messageHtml += '<h3>Ошибка</h3>';
        messageHtml += '<p>' + messageText + '</p>';
        messageHtml += '</div>';
        messageHtml += '</div>';

        $('#systemMessage').append(messageHtml);


    }

    if (typeWindow == 'info') {


        var messageHtml = '<div id="MesIndex' + systemMessageIndex + '" class="systemMessageInfo">';
        messageHtml += '<i class="fa fa-times" onclick="messageClose(this)" aria-hidden="true"></i>';
        messageHtml += '<div class="systemMessageDiv">';
        messageHtml += '<div class="systemMessageIcon"><i class="fa fa-info-circle" aria-hidden="true"></i></div>';
        messageHtml += '<h3>Предупреждение</h3>';
        messageHtml += '<p>' + messageText + '</p>';
        messageHtml += '</div>';
        messageHtml += '</div>';

        $('#systemMessage').append(messageHtml);


    }


    $("#MesIndex" + systemMessageIndex).css({ opacity: "1" }).delay(10000).animate({ opacity: "0" }, 1000);

    systemMessageIndex++;

}


// Конструктор линий
function conclusionLineStart() {
    var gvsLine = $('.MapBlockDropList .MapBlockDrop img[data-type=block_gvs]').attr('data-lile');
    $('#svg-line .line').remove();
    $('#svg-point').html('');
    //
    arrPoint = [];
    arrPoint2 = [];
    thisBlockCont = '';
    thisBlockContPodpitka = '';
    //проверяем валидность вставляемого блока
    if ($('.MapBlockDropInput img').attr('data-id')) {
        //получаем данные и координаты
        var vImage = $('.MapBlockDropInput img'),
            vImagePos = $('.MapBlockDropInput img').position();
        //рисуем лини от тепловой сети (красная)
        var lineX1 = $('.MapBlock1 .heat').position().left + 24,
            lineY1 = $('.MapBlock1 .heat').position().top + 24,
            lineX2 = vImagePos.left + parseInt(vImage.attr('data-left')),
            lineY2 = vImagePos.top + parseInt(vImage.attr('data-h1'));
        conclusionLine('red', lineX1, lineY1, lineX2, lineY2);
        //рисуем лини от тепловой сети (синия)
        var lineX1 = $('.MapBlock1 .cold').position().left + 24,
            lineY1 = $('.MapBlock1 .cold').position().top + 37,
            lineX2 = vImagePos.left + parseInt(vImage.attr('data-left')),
            lineY2 = vImagePos.top + parseInt(vImage.attr('data-c1'));
        conclusionLine('blue', lineX1, lineY1, lineX2, lineY2);
        //переменные для рисования линий подпитки
        var podpitkaTrigger = true;
        var podpitkaFlag = false;
        var podpitkaCount = 0;
        var podpitkaCountReal = 0;
        var podpitkaNumb = 0;
        //приходимся по всем блокам
        $('.MapBlockDropList .MapBlockDrop').each(function() {
            //проверяем валидность валидность блока
            if ($(this).find('img').attr('data-id')) {
                //рисуем линии на блок отопления
                if ($(this).find('img').attr('data-type') == 'block_otopleniya') {
                    var blockDab = $(this).find('img');
                    //рисуем линии на систему отопления/выход (красная)
                    var lineX1 = blockDab.position().left + blockDab.width(),
                        lineY1 = blockDab.position().top + parseInt(blockDab.attr('data-h1')),
                        lineX2 = blockDab.position().left + blockDab.width() + 80,
                        lineY2 = blockDab.position().top + parseInt(blockDab.attr('data-h1'));
                    conclusionLine('red', lineX1, lineY1, lineX2, lineY2);
                    //рисуем линии на систему отопления/выход (синяя)
                    var lineX1 = blockDab.position().left + blockDab.width(),
                        lineY1 = blockDab.position().top + parseInt(blockDab.attr('data-c1')),
                        lineX2 = blockDab.position().left + blockDab.width() + 80,
                        lineY2 = blockDab.position().top + parseInt(blockDab.attr('data-c1'));
                    conclusionLine('blue', lineX1, lineY1, lineX2, lineY2);
                }

                if ($(this).find('img').attr('data-type') != 'block_podpitky') {


                    // Определяем высоту линии
                    thisBlockCont = $(this).find('img');


                    var lineX1 = vImagePos.left + vImage.width() + 110,
                        lineY1 = thisBlockCont.position().top + parseInt(thisBlockCont.attr('data-h1')),
                        lineX2 = thisBlockCont.position().left + parseInt(thisBlockCont.attr('data-left')),
                        lineY2 = thisBlockCont.position().top + parseInt(thisBlockCont.attr('data-h1'));

                    conclusionLine('red', lineX1, lineY1, lineX2, lineY2);



                    if (gvsLine == 1) {


                        ///////////
                        // Блок ГВС 2 вариант
                        ///////////

                        if (thisBlockCont.attr('data-type') != 'block_gvs') {
                            var lineX1 = vImagePos.left + vImage.width() + 78,
                                lineY1 = thisBlockCont.position().top + parseInt(thisBlockCont.attr('data-c1')),
                                lineX2 = thisBlockCont.position().left,
                                lineY2 = thisBlockCont.position().top + parseInt(thisBlockCont.attr('data-c1'));

                            conclusionLine('blue', lineX1, lineY1, lineX2, lineY2);

                            arrPoint2.push(lineY2)

                        }


                    } else {


                        var lineX1 = vImagePos.left + vImage.width() + 45,
                            lineY1 = thisBlockCont.position().top + parseInt(thisBlockCont.attr('data-c1')),
                            lineX2 = thisBlockCont.position().left,
                            lineY2 = thisBlockCont.position().top + parseInt(thisBlockCont.attr('data-c1'));

                        conclusionLine('blue', lineX1, lineY1, lineX2, lineY2);

                        // Вставить принт
                        arrPoint2.push(lineY2);


                    }


                } else {


                    //отрисовка линий блока подпитки
                    if ($(this).find('img').attr('data-type') == 'block_podpitky') {
                        podpitkaCountReal++;
                        thisBlockContPodpitka = $(this).find('img');
                        //проверка есть ли другие блоки подпитки с учтённым насосом 
                        if (podpitkaTrigger) {
                            podpitkaTrigger = false;
                            $('.MapBlockDropList .MapBlockDrop').each(function() {
                                checkBlock = $(this).find('img');
                                if (checkBlock.attr('data-type') == 'block_podpitky') {
                                    podpitkaCount++;
                                    if (checkBlock.attr('data-podpitka-pov') == '3') {
                                        podpitkaFlag = true;
                                        podpitkaNumb = podpitkaCount;
                                    }
                                }
                            });
                        }
                        //линия синяя от блока подпитки до центра
                        var lineX1 = vImagePos.left + vImage.width() + 12,
                            lineY1 = thisBlockContPodpitka.position().top + parseInt(thisBlockContPodpitka.attr('data-h1')),
                            lineX2 = thisBlockContPodpitka.position().left + parseInt(thisBlockContPodpitka.attr('data-left')),
                            lineY2 = thisBlockContPodpitka.position().top + parseInt(thisBlockContPodpitka.attr('data-h1'));
                        conclusionLine('blue', lineX1, lineY1, lineX2, lineY2);
                        arrPoint.push(lineY2);
                        //линия синяя от блока ввода до центра
                        var lineX1 = vImagePos.left + vImage.width(),
                            lineY1 = vImagePos.top + parseInt(vImage.attr('data-t')),
                            lineX2 = vImagePos.left + vImage.width() + 12,
                            lineY2 = vImagePos.top + parseInt(vImage.attr('data-t'));
                        conclusionLine('blue', lineX1, lineY1, lineX2, lineY2);

                        if (podpitkaFlag) {
                            if (podpitkaCountReal == 1) {
                                //линия от первого блока подпитки 
                                var lineX1 = vImagePos.left + vImage.width() + 12,
                                    lineY1 = vImagePos.top + parseInt(vImage.attr('data-t')),
                                    lineX2 = vImagePos.left + vImage.width() + 12,
                                    lineY2 = thisBlockContPodpitka.position().top + parseInt(thisBlockContPodpitka.attr('data-h1'));
                                conclusionLine('blue', lineX1, lineY1, lineX2, lineY2);
                            } else {
                                //координаты положения линии от первого блока подпитки до второго	
                                var pLineX = vImagePos.left + 758;
                                var pLineY = vImagePos.top + 328.25;
                                //линия от середины блока подпитки вниз (синяя вертикальная)
                                var lineX1 = pLineX, //858
                                    lineY1 = vImagePos.top + 287.25, //339
                                    lineX2 = pLineX, //858
                                    lineY2 = pLineY; //380
                                conclusionLine('blue', lineX1, lineY1, lineX2, lineY2);
                                //линия от предыдущий до центра (синяя горизонтальная)
                                var lineX1 = vImagePos.left + vImage.width() + 12, //858
                                    lineY1 = pLineY, //339
                                    lineX2 = pLineX, //858
                                    lineY2 = pLineY; //380
                                conclusionLine('blue', lineX1, lineY1, lineX2, lineY2);
                                //обход труб
                                arrPoint.push(pLineY);
                                //линия от предыдущий до нижнего блока подпитки (синяя вертикальная)
                                var lineX1 = vImagePos.left + vImage.width() + 12, //858
                                    lineY1 = pLineY, //339
                                    lineX2 = vImagePos.left + vImage.width() + 12, //858
                                    lineY2 = thisBlockContPodpitka.position().top + parseInt(thisBlockContPodpitka.attr('data-h1')); //380
                                conclusionLine('blue', lineX1, lineY1, lineX2, lineY2);
                            }

                        } else {
                            var lineX1 = vImagePos.left + vImage.width() + 12,
                                lineY1 = thisBlockContPodpitka.position().top + parseInt(thisBlockContPodpitka.attr('data-h1')),
                                lineX2 = thisBlockContPodpitka.position().left + parseInt(thisBlockContPodpitka.attr('data-left')),
                                lineY2 = thisBlockContPodpitka.position().top + parseInt(thisBlockContPodpitka.attr('data-h1'));
                            conclusionLine('blue', lineX1, lineY1, lineX2, lineY2);
                            arrPoint.push(lineY2);



                            var lineX1 = vImagePos.left + vImage.width() + 12,
                                lineY1 = vImagePos.top + parseInt(vImage.attr('data-t')),
                                lineX2 = vImagePos.left + vImage.width() + 12,
                                lineY2 = thisBlockContPodpitka.position().top + parseInt(thisBlockContPodpitka.attr('data-h1'));

                            conclusionLine('blue', lineX1, lineY1, lineX2, lineY2);
                        }






                        var vImagePrev = thisBlockContPodpitka.parents('.MapBlockDrop ').prev().find('img');

                        if (vImagePrev.attr('data-id')) {


                            var lineX1 = vImagePrev.position().left + parseInt(vImagePrev.attr('data-podpitka')),
                                lineY1 = vImagePrev.position().top + parseInt(vImagePrev.height()) - 10,
                                lineX2 = vImagePrev.position().left + parseInt(vImagePrev.attr('data-podpitka')),
                                lineY2 = thisBlockContPodpitka.position().top + parseInt(thisBlockContPodpitka.attr('data-podpitka'));

                            conclusionLine('blue', lineX1, lineY1, lineX2, lineY2);


                        }


                        var vImagePrev = thisBlockContPodpitka.parents('.strongBlockTop ').prev().find('img');

                        if (vImagePrev.attr('data-id')) {


                            var lineX1 = vImagePrev.position().left + parseInt(vImagePrev.attr('data-podpitka')),
                                lineY1 = vImagePrev.position().top + parseInt(vImagePrev.height()) - vImagePrev.attr('data-podpitka-h'),
                                lineX2 = vImagePrev.position().left + parseInt(vImagePrev.attr('data-podpitka')),
                                lineY2 = thisBlockContPodpitka.position().top + parseInt(thisBlockContPodpitka.attr('data-podpitka'));

                            conclusionLine('blue', lineX1, lineY1, lineX2, lineY2);


                        }

                    }


                }





            }




        });




        if (thisBlockCont) {

            var lineX1 = vImagePos.left + vImage.width(),
                lineY1 = vImagePos.top + parseInt(vImage.attr('data-h1')),
                lineX2 = vImagePos.left + vImage.width() + 110,
                lineY2 = vImagePos.top + parseInt(vImage.attr('data-h1'));

            conclusionLine('red', lineX1, lineY1, lineX2, lineY2);



            var lineX1 = vImagePos.left + vImage.width() + 110,
                lineY1 = vImagePos.top + parseInt(vImage.attr('data-h1')),
                lineX2 = vImagePos.left + vImage.width() + 110,
                lineY2 = thisBlockCont.position().top + parseInt(thisBlockCont.attr('data-h1'));
            console.log('red' + lineX1 + ' ' + lineY1 + ' ' + lineX2 + ' ' + lineY2);
            conclusionLine('red', lineX1, lineY1, lineX2, lineY2);



            // Блок ГВС исключение
            if (gvsLine == 1) {

                var oDataC1 = vImage.attr('data-c1');

                if ($('.block_otopleniya img').attr('data-id')) {

                    var oImage = $('.block_otopleniya img'),
                        oImagePos = $('.block_otopleniya img').position();

                    oDataC1 = oImage.attr('data-c1');
                }
                var lineX1 = vImagePos.left + vImage.width() + 78,
                    lineY1 = vImagePos.top + parseInt(oDataC1),
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
            console.log('blue' + lineX1 + ' ' + lineY1 + ' ' + lineX2 + ' ' + lineY2);
            conclusionLine('blue', lineX1, lineY1, lineX2, lineY2);


        }





        if (arrPoint) {

            if (thisBlockCont) {


                var pointBlockCont = thisBlockCont.position().top + parseInt(thisBlockCont.attr('data-h1')),
                    pointBlockContLeft = vImagePos.left + vImage.width() + 110 - 7;

                for (var i = 0; arrPoint.length > i; i++) {


                    if (arrPoint[i] < pointBlockCont) {

                        var arrNumTop = arrPoint[i] - 6;

                        $('#svg-point').append('<div class="point" style="top: ' + arrNumTop + 'px; left: ' + pointBlockContLeft + 'px"></div>');


                        var pointBlockContLeftNum = pointBlockContLeft - 65;

                        $('#svg-point').append('<div class="point" style="top: ' + arrNumTop + 'px; left: ' + pointBlockContLeftNum + 'px"></div>');


                        // Блок ГВС исключение
                        if (gvsLine == 1) {

                            var pointBlockContLeftNum = pointBlockContLeft - 32;

                            $('#svg-point').append('<div class="point" style="top: ' + arrNumTop + 'px; left: ' + pointBlockContLeftNum + 'px"></div>');

                        }


                    }


                }


            }


        }


        if (arrPoint2) {

            if (thisBlockCont) {


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
function conclusionLine(typeLine, lineX1, lineY1, lineX2, lineY2) {


    var line = document.createElementNS("http://www.w3.org/2000/svg", "line");

    line.setAttribute("x1", lineX1);
    line.setAttribute("y1", lineY1);
    line.setAttribute("x2", lineX2);
    line.setAttribute("y2", lineY2);
    line.setAttribute("stroke-width", "2");

    if (typeLine == 'red') {

        line.setAttribute("class", "line line-red");

    } else {

        line.setAttribute("class", "line line-blue");

    }


    $('#svg-line line:last').after(line);


}


function caruselPrevNext(aThis, direction) {

    if (direction == 'prev') {

        var sign = -400;

    } else if (direction == 'next') {

        var sign = 400;

    }

    var vCarusel = $(aThis).parents('.blockPsCarusel').find('.carousel');

    vCarusel.animate({ scrollLeft: vCarusel.scrollLeft() + sign }, 200);

}

function messageClose(aThis) {

    $(aThis).parents('.systemMessageError').remove();

}


function caruselClickActive(aThis) {

    $(aThis).parent().find('.carousel-element').removeClass('active');
    $(aThis).addClass('active');


    var caruselDataImg = $(aThis).find('img').attr('data-id');

    if (caruselDataImg == 13 || caruselDataImg == 14 || caruselDataImg == 16 || caruselDataImg == 16) {

        $('.blockVvodaTeplovich').css('display', 'block');

    } else {

        $('.blockVvodaTeplovich').css('display', 'none');

    }


}

function inputCheck(aThis) {

    // $(aThis).val($(aThis).val().replace(',', '.').replace(/[^0-9.]/g, ''));

}

//сохранения данных блока и перенос его на координатную плоскость
function modalSave(aThis) {
    var typeblockSave = $('.box-modal .modalCharacteristic').attr('data-type'),
        errorInputStatus = true;

    //скрываем поле ошибки
    const eb = $('.box-modal .errorText');
    eb.css('display', 'none').html(first_error_text);
    $('.box-modal .errorInput').removeClass('errorInput');
    //ширина блока по умолчанию
    var blockWidth = '405px';
    //проверка полей у блока ввода
    let p1 = null,
    p1_link,
    p2 = null,
    p2_link;
    if (typeblockSave == 'block_vvoda') {
        $('.box-modal input[name="t1"], .box-modal input[name="t2"], .box-modal input[name="p1"], .box-modal input[name="p2"]').each(function() {
            const name = $(this).attr("name");
            const value = $(this).val();
            if (value == '') {
                $(this).addClass('errorInput');
                errorInputStatus = false;
            }
            switch(name){
                case "p1":
                    p1 = value;
                    p1_link = $(this);
                break;
                case "p2":
                    p2 = value;
                    p2_link = $(this);
                break;
            }
            if(p1 !== null && p2 !== null && p1 != "" && p2 != ""){
                if(p1 <= p2){
                    p1_link.addClass('errorInput');
                    p2_link.addClass('errorInput');
                    errorInputStatus = false;
                    eb.html(`P1 не должно быть меньше P2`);
                }
            }
        });
        //ширина блока ввода
        blockWidth = '405px';
    }
    //проверка полей у блока отопления
    if (typeblockSave == 'block_otopleniya') {
        $('.box-modal input[name="q"], .box-modal input[name="t1"], .box-modal input[name="t2"], .box-modal input[name="p1"], .box-modal input[name="p2"], .box-modal input[name="t11"], .box-modal input[name="t21"], .box-modal input[name="dp"], .box-modal input[name="h"]').each(function() {
            if ($(this).val() == '') {
                $(this).addClass('errorInput');
                errorInputStatus = false;
            }
        });
        //ширина блока отопления
        blockWidth = '550px';
    }
    //проверка полей у блока ГВС
    if (typeblockSave == 'block_gvs') {
        const typeNag = $('.box-modal input[name=typeNag]:checked').val();
        $('.box-modal input[name="qGVS"], .box-modal input[name="GVSt1"], .box-modal input[name="GVSt2"], .box-modal input[name="p1"], .box-modal input[name="p2"], .box-modal input[name="GVSt3"], .box-modal input[name="GVStv1"], .box-modal input[name="pb1"]').each(function() {
            const name = $(this).attr("name");
            if (name == "qGVS" && typeNag == 2) {
                return true;
            }
            if (name == "GVSgt1" && typeNag == 1) {
                return true;
            }
            if ($(this).val() == '') {
                $(this).addClass('errorInput');
                errorInputStatus = false;
            }
        });
        //ширина блока ГВС
        blockWidth = '535px';
    }
    //проверка полей у блока подпитки (отсутствует)
    if (typeblockSave == 'block_podpitky') {
        //ширина блока подпитки
        blockWidth = '525px';
    }
    //временное отключение проверки на ошибки
    //errorInputStatus = true;
    //если обнаружены ошибки то выводим их и прерываем функцию
    if (!errorInputStatus) {
        $('.box-modal .errorText').css('display', 'block');
        return false;
    }
    //закрываем модальное окно с выбором блока
    $.arcticmodal('close');
    //данные блока на координатной плоскости по умолчанию
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
        dropDataPodpitkaH = dropData.attr('data-podpitka-h'),
        //блок на координатной плоскости по умолчанию
        dropDataImg = '<img  class="blockImg" src="' + dropDataImage + '" data-t="' + dropDataT + '" data-podpitka="' + dropDataPodpitka + '" data-podpitka-h="' + dropDataPodpitkaH + '" data-left="' + dropDataLeft + '" data-lile="' + dropDataLine + '" data-type="' + dropDataType + '" data-id="' + dropDataId + '" style="width: ' + blockWidth + '" data-h1="' + dropDataH1 + '" data-h2="' + dropDataH2 + '" data-c1="' + dropDataC1 + '" data-c2="' + dropDataC2 + '" alt=""/>';
    //очищаем данные по блоку
    inputData = "";
    //заполняем данные по блоку ввода
    if (dropDataType == 'block_vvoda') {
        var inputDataJSON = '{"' + dropDataType + '": {"q": "' + $('.box-modal input[name=q]').val() + '",';
        inputDataJSON += '"id": "' + $('.box-modal .carousel-element.active').attr('data-id') + '",';
        inputDataJSON += '"hasPodpitka": "' + $('.box-modal .carousel-element.active').attr('data-line') + '",';
        inputDataJSON += '"qType": "' + $('.box-modal select[name=qType] option:selected').text() + '",';
        inputDataJSON += '"uzel_ucheta": "' + $('.box-modal #uzel_ucheta').prop("checked") + '",';
        inputDataJSON += '"regul_dav_t1": "' + $('.box-modal #regul_dav_t1').prop("checked") + '",';
        inputDataJSON += '"regul_dav_t2": "' + $('.box-modal #regul_dav_t2').prop("checked") + '",';
        inputDataJSON += '"bal_klapan": "' + $('.box-modal #bal_klapan').prop("checked") + '",';
        inputDataJSON += '"shkafPodpitka": "' + $('.box-modal #shkafPodpitka').prop("checked") + '",';
        inputDataJSON += '"shkafDopObor": "' + $('.box-modal #shkafDopObor').prop("checked") + '",';
        inputDataJSON += '"skafDi": "' + $('.box-modal input[name=skafDi]:checked').val() + '",';
        inputDataJSON += '"message": "' + $('.box-modal textarea[name=message]').val() + '",';
        inputDataJSON += '"skafDo": "' + $('.box-modal input[name=skafDo]:checked').val() + '"';
        inputDataJSON += '}}';

        inputData = '<span class="spanHidden" data-type="' + dropDataType + '">' + inputDataJSON + '</span>';
        //пока что-то неизвестное
        if ($('#i-q').text() != '') {
            var jsonQ = $.parseJSON($('#i-q').text());
        }
        var inputDataJSON = '{"iQ": {';
        if ($('#i-q').text() != '') {
            if (jsonQ.iQ) {
                if (jsonQ.iQ.q) {} else {
                    inputDataJSON += '"q": "' + $('.box-modal input[name=q]').val() + '"';
                }
            } else {
                inputDataJSON += '"q": "' + $('.box-modal input[name=q]').val() + '"';
            }
        } else {
            inputDataJSON += '"q": "' + $('.box-modal input[name=q]').val() + '"';
        }
        inputDataJSON += '}}';
        $('#i-q').text(inputDataJSON);
    }
    //заполняем данные по блоку отопления
    if (dropDataType == 'block_otopleniya') {
        var inputDataJSON = '{"' + dropDataType + '": {';
        inputDataJSON += '"q": "' + $('.box-modal input[name=q]').val() + '",';
        inputDataJSON += '"id": "' + $('.box-modal .carousel-element.active').attr('data-id') + '",';
        inputDataJSON += '"qType": "' + $('.box-modal select[name=qType] option:selected').text() + '",';
        inputDataJSON += '"kontr": "' + $('.box-modal select[name=kontr] option:selected').text() + '",';
        inputDataJSON += '"regKlapan": "' + $('.box-modal select[name=regKlapan] option:selected').text() + '",';
        // inputDataJSON += '"t11": "' + $('.box-modal select[name=t11] option:selected').text() + '",';
        // inputDataJSON += '"t21": "' + $('.box-modal select[name=t21] option:selected').text() + '",';
        inputDataJSON += '"t11": "' + $('.box-modal input[name=t11]').val() + '",';
        inputDataJSON += '"t21": "' + $('.box-modal input[name=t21]').val() + '",';
        inputDataJSON += '"dpType": "' + $('.box-modal select[name=dpType] option:selected').text() + '",';
        inputDataJSON += '"shkafAvtomatiky": "' + $('.box-modal select[name=shkafAvtomatiky] option:selected').text() + '",';
        inputDataJSON += '"typeBlock": "' + $('.box-modal input[name=typeBlock]:checked').val() + '",';
        inputDataJSON += '"pch": "' + $('.box-modal input[name=pch]:checked').val() + '",';
        inputDataJSON += '"dp": "' + $('.box-modal input[name=dp]').val() + '",';
        inputDataJSON += '"bvNasos": "' + $('.box-modal .bvNasos').val() + '",';

        inputDataJSON += '"bov_type": "' + $('.box-modal select[name=bov_type] option:selected').text() + '",';
        inputDataJSON += '"nasos_type": "' + $('.box-modal select[name=nasos_type] option:selected').text() + '",';
        inputDataJSON += '"ras_nasos": "' + $('.box-modal select[name=ras_nasos] option:selected').text() + '",';
        inputDataJSON += '"teplo": "' + $('.box-modal select[name=teplo] option:selected').text() + '",';
        inputDataJSON += '"teploPr": "' + $('.box-modal select[name=teploPr] option:selected').val() + '",';
        inputDataJSON += '"reg_t": "' + $('.box-modal select[name=reg_t] option:selected').text() + '",';


        inputDataJSON += '"message": "' + $('.box-modal textarea[name=message]').val() + '",';
        inputDataJSON += '"h": "' + $('.box-modal input[name=h]').val() + '"';
        inputDataJSON += '}}';
        inputData = '<span class="spanHidden" data-type="' + dropDataType + '">' + inputDataJSON + '</span><span class="spanHiddenNasos">' + $('.box-modal .bvNasos').val() + '</span>';
    }
    //заполняем данные по блоку подпитки
    if (dropDataType == 'block_podpitky') {
        var inputDataJSON = '{"' + dropDataType + '": {';
        inputDataJSON += '"povNasos": "' + $('.box-modal select[name=povNasos] option:selected').text() + '",';
        inputDataJSON += '"id": "' + $('.box-modal .carousel-element.active').attr('data-id') + '",';
        inputDataJSON += '"klapanSol": "' + $('.box-modal select[name=klapanSol] option:selected').text() + '",';
        inputDataJSON += '"rasshirBak": "' + $('.box-modal select[name=rasshirBak] option:selected').text() + '",';
        inputDataJSON += '"message": "' + $('.box-modal textarea[name=message]').val() + '",';
        inputDataJSON += '"nasosStan": "' + $('.box-modal input[name=nasosStan]:checked').val() + '"';
        inputDataJSON += '}}';
        inputData = '<span class="spanHidden" data-type="' + dropDataType + '">' + inputDataJSON + '</span>';
        //генерируем особый блок на координатной плоскости
        dropDataImg = '<img  class="blockImg" src="' + dropDataImage + '" data-podpitka-pov="' + $('.box-modal input[name=nasosStan]:checked').val() + '" data-t="' + dropDataT + '" data-podpitka="' + dropDataPodpitka + '" data-podpitka-h="' + dropDataPodpitkaH + '" data-left="' + dropDataLeft + '" data-lile="' + dropDataLine + '" data-type="' + dropDataType + '" data-id="' + dropDataId + '" style="width: ' + blockWidth + '" data-h1="' + dropDataH1 + '" data-h2="' + dropDataH2 + '" data-c1="' + dropDataC1 + '" data-c2="' + dropDataC2 + '" alt=""/>';
    }
    //заполняем данные по блоку ГВС
    if (dropDataType == 'block_gvs') {
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
        inputDataJSON += '"gvs_type": "' + $('.box-modal select[name=gvs_type] option:selected').text() + '",';
        inputDataJSON += '"nasos_type": "' + $('.box-modal select[name=nasos_type] option:selected').text() + '",';
        inputDataJSON += '"teplo": "' + $('.box-modal select[name=teplo] option:selected').text() + '",';
        inputDataJSON += '"teploPr": "' + $('.box-modal select[name=teploPr] option:selected').val() + '",';
        inputDataJSON += '"reg_t": "' + $('.box-modal select[name=reg_t] option:selected').text() + '",';
        inputDataJSON += '"message": "' + $('.box-modal textarea[name=message]').val() + '",';
        inputDataJSON += '"dhodDBlockBOV": "' + $('.box-modal #dhodDBlockBOV').prop("checked") + '"';
        inputDataJSON += '}}';
        inputData = '<span class="spanHidden" data-type="' + dropDataType + '">' + inputDataJSON + '</span>';
    }


    if ($('.box-modal input[name=t1]').val()) {

        var inputDataJSON = '{"iData": {';
        inputDataJSON += '"t1": "' + $('.box-modal input[name=t1]').val() + '",';
        inputDataJSON += '"t2": "' + $('.box-modal input[name=t2]').val() + '"';
        inputDataJSON += '}}';

        $('#i-data').text(inputDataJSON);

    }


    if ($('.box-modal input[name=p1]').val()) {

        var inputDataJSON = '{"iDataGeneral": {';
        inputDataJSON += '"p1": "' + $('.box-modal input[name=p1]').val() + '",';
        inputDataJSON += '"p2": "' + $('.box-modal input[name=p2]').val() + '",';
        inputDataJSON += '"p1Type": "' + $('.box-modal select[name=p1Type] option:selected').text() + '",';
        inputDataJSON += '"p2Type": "' + $('.box-modal select[name=p2Type] option:selected').text() + '"';
        inputDataJSON += '}}';

        $('#i-data-general').text(inputDataJSON);

    }





    var modalAttr = '';

    if (dropDataType == 'block_otopleniya' || dropDataType == 'block_gvs') {

        modalAttr = 'onclick="modalGenerator(\'' + dropDataType + '\',\'\',\'\',this)"';

    }


    if (dropDataType == 'block_podpitky' && $(dropThis).prev().find('img').attr('data-lile') == '0') {


        systemMessage('error', 'Необходимо выбрать блок отопления с теплообменником');

        return false;


    }


    $(dropThis).html($('<div ' + modalAttr + ' class="dropBlock draggable ' + dropDataType + '"><h3 data-scid="' + scetcikI + '"><div class="divBotPosition"><i class="fa fa-pencil"></i> <i class="fa fa-trash" onclick="deleteBlock(this)"></i></div>' + $(aThis).parents('.box-modal').find('h3').html() + '</h3>' + dropDataImg + inputData + '</div>'));


    scetcikI++;


    if (dropDataType == 'block_otopleniya') {

        var pr = $(".box-modal .modalCharacteristic input[name=typeBlock]:checked").attr("data-name");



        if (!$(dropThis).next().html() && $(dropThis).next().html() != '') {

            $('.heatNetworkCls').each(function() {


                $(this).find('.thermalNetwork').text('Система ' + $(this).parents('.blockSystem').prev().find('h3').text().substr(6));


            });



        } else {


            $(dropThis).next('.blockSystem').html('<div class="tpBlockStl"><div class="heatNetworkCls" style="top: calc(66px + ' + dropDataH2 + 'px);">' +
                '                                <div class="joiningFeederLine"><div class="heatLine"></div><i>Присоединение к подающему</i> <span>трубопроводу по мосту</span></div>' +
                '                                <span data-scid="' + scetcikI + '" class="thermalNetwork">Система ' + pr + '</span>' +
                '                                <div class="attachReverseLine"><div class="coldLine"></div><i>Присоединение к обратному</i> <span>трубопроводу по мосту</span></div>' +
                '                            </div></div>');


        }




    }


    if (dropDataType == 'block_vvoda') {


        console.log('ff');
        if (statusGVS) {

            if ($('.MapBlockDropList .blockImg[data-type="block_otopleniya"]').length >= 2) {

                var htmlMenu = '<div class="clickDpMenu"><span onclick="modalGenerator(\'block_gvs\',\'\',\'\',$(this).parents(\'.MapBlockDrop\'))">Вставить блок ГВС</span></div>';

            } else {

                var htmlMenu = '<div class="clickDpMenu"><span onclick="modalGenerator(\'block_otopleniya\',\'\',\'\',$(this).parents(\'.MapBlockDrop\'))">Вставить блок отопления</span><span onclick="modalGenerator(\'block_gvs\',\'\',\'\',$(this).parents(\'.MapBlockDrop\'))">Вставить блок ГВС</span></div>';

            }


        } else {

            var htmlMenu = '<div class="clickDpMenu"><span onclick="modalGenerator(\'block_otopleniya\',\'\',\'\',$(this).parents(\'.MapBlockDrop\'))">Вставить блок отопления</span></div>';


        }

        $(".mmc").html('<div class="insertBlockNew insertBlockNull" onclick="menuType()">' + htmlMenu + '<div class="clickDp"><div class="iconHover"></div><span>Вставить блок</span></div></div>');

        $(".mmc").addClass('blockActiveHover');

        console.log('<div class="insertBlockNew insertBlockNull" onclick="menuType()">' + htmlMenu + '<div class="clickDp"><div class="iconHover"></div><span>Вставить блок</span></div></div>');

        if (dropDataLine == '1') {

            $('.blockPodpitkyList, .blockOtopleniyaList').css('display', 'table');

        } else {

            $('.blockPodpitkyList, .blockOtopleniyaList').css('display', 'none');

            $('.blockPodpitkyList').addClass('blockActive').html('<div class="insertBlockPodpitky"><div class="iconHover"></div><span>Вставить блок подпитки</span></div>');

            $('.blockOtopleniyaList').css('display', 'table');

        }


        $(dropThis).removeClass('blockActiveHover');

        $('.blockLeftCont[data-type=block_vvoda]').css('display', 'none');

        $('#blockLeft').getNiceScroll().resize();


    }


    if (dropDataType == 'block_otopleniya') {


        $(dropThis).removeClass('blockActive');


        if ($(dropThis).parents('.strongBlock').next().hasClass('strongBlockTop')) {


            $(dropThis).parents('.strongBlock').next().remove();


        } else {


            if ($(dropThis).parents('.strongBlockTop').next().hasClass('blockPodpitkyList') && $(dropThis).find('img').attr('data-lile') == '0') {


                $(dropThis).parents('.strongBlockTop').next().css('display', 'none').addClass('blockActive').html('<div class="insertBlockPodpitky"><div class="iconHover"></div><span>Вставить блок подпитки</span></div>');


            } else {

                if ($(dropThis).find('img').attr('data-lile') == '1') {

                    $(dropThis).parents('.strongBlockTop').next().css('display', 'table');

                }

            }



        }




        if (dropDataLine == 1) {


            $(dropThis).parents('.strongBlock').after('<div class="strongBlock strongBlockTop"><div class="MapBlockDrop blockPodpitkyList blockActive"  onclick="modalGenerator(\'block_podpitky\',\'\',\'\',this)">' + blockPodpitkyList + '</div></div>');

            dropFn();


        }



        if ($(dropThis).parents('.strongBlock').prevAll('.strongBlock').find('img[data-type=block_gvs]').attr('data-type') == 'block_gvs') {


            if ($(this).find('.MapBlockDropList img["data-type=block_gvs"]')) {

                var sbb = $(dropThis).parents('.strongBlock').next('.strongBlockTop'),
                    sbbHtml = '',
                    trBlockSystem = '';


                if (sbb.html()) {

                    var sbbHtml = sbb.html();
                    sbb.remove();

                }

                if ($(dropThis).next().html()) {

                    trBlockSystem = '<div class="blockSystem">' + $(dropThis).next().html() + '</div>';

                }

                $('.MapBlockDropList .blockImg[data-type="block_gvs"]').parents('.strongBlock').before('<div class="strongBlock"><div class="MapBlockDrop">' + $(dropThis).html() + '</div>' + trBlockSystem + '</div><div class="strongBlock">' + sbbHtml + '</div>');
                $(dropThis).parents('.strongBlock').remove();

            }


        }



    }


    if (dropDataType == 'block_podpitky') {


        $(dropThis).removeClass('blockActive');


    }


    if (dropDataType == 'block_gvs') {


        $('.blockLeftCont[data-type=block_gvs]').css('display', 'none');

        $('#blockLeft').getNiceScroll().resize();


        if ($('.strongBlock:last').next().hasClass('MapBlockDrop')) {

            //$('.strongBlock:last').next().remove();

        }


        $(dropThis).html($('<div ' + modalAttr + ' class="dropBlock draggable"><h3 data-scid="' + scetcikI + '"><div class="divBotPosition"><i class="fa fa-pencil"></i> <i class="fa fa-trash" onclick="deleteBlock(this)"></i></div>' + $(aThis).parents('.box-modal').find('h3').html() + '</h3>' + dropDataImg + inputData + '</div>'));

        statusGVS = 0;


    }


    var loadImg = new Image();
    loadImg.src = dropDataImage;

    loadImg.onload = function() {

        conclusionLineStart();

    }



    blockVvodaType = 0;


    $('.strongBlock .blockImg, .blockOtopleniyaList img').each(function() {


        if ($(this).attr('data-lile') == 1 && $(this).attr('data-type') != 'block_gvs') {


            blockVvodaType = 1;


        } else {


            if (blockVvodaType != 1) {


                blockVvodaType = 2;


            }


        }


    });


    // if (blockVvodaType == 1 && $('.blockVvodaDS img').attr('data-lile') != 1 && dropDataType != 'block_gvs') {


    //     systemMessage('info', 'Вставьте блок отопления с дополнительной линией подпитки');

    //     $('.blockVvodaDS').html(cloneBlockVvoda);

    //     dropFn();

    //     $('.blockLeftCont[data-type=block_vvoda]').css('display', 'block');

    //     $('#blockLeft').getNiceScroll().resize();


    // } else if (blockVvodaType == 2 && $('.blockVvodaDS img').attr('data-lile') == 1) {


    //     systemMessage('info', 'Вставьте блок отопления БЕЗ дополнительной линией подпитки');

    //     $('.blockVvodaDS').html(cloneBlockVvoda);

    //     dropFn();

    //     $('.blockLeftCont[data-type=block_vvoda]').css('display', 'block');

    //     $('#blockLeft').getNiceScroll().resize();


    // }

    var jsonQSum = 0;

    $('.MapBlockDropList span[data-type="block_otopleniya"], .MapBlockDropList span[data-type="block_gvs"]').each(function() {

        var jsonq = $.parseJSON($(this).text());


        if (jsonq.block_otopleniya) {

            jsonQSum += parseInt(jsonq.block_otopleniya.q);

        } else if (jsonq.block_gvs) {

            jsonQSum += parseInt(jsonq.block_gvs.qGVS);

        }


    });


    if (jsonQSum) {

        $('.qSum').attr('disabled', 'disabled');

        var inputDataJSON = '{"iQ": {';
        inputDataJSON += '"q": "' + jsonQSum + '"';
        inputDataJSON += '}}';

        $('#i-q').text(inputDataJSON);

    }

    //if($(".mmc").find('<div>').find('<div>').h

    prNasos();


    dropThis = "";

    vetka2();


}
/* ---- Конец функции сохранения ---- */

function removeMap() {

    $('.MapBlockDrop').empty();
    $('#svg-line .line').remove();
    thisBlock = '';

    $('.blockLeftCont').css('display', 'block');
    $('#blockLeft').getNiceScroll().resize();

    //
    $('.blockOtopleniyaList, .blockPodpitkyList').addClass('blockActive').css('display', 'none');

    $('.blockOtopleniyaList').html('<div class="insertBlockOtopleniya"><div class="iconHover"></div><span>Вставить блок отопления</span></div>');
    $('.blockPodpitkyList').html('<div class="insertBlockPodpitky"><div class="iconHover"></div><span>Вставить блок подпитки</span></div>');
    $('.MapStr .blockVvodaDS').html(cloneBlockVvoda);


    if ($('.strongBlock:last').next().hasClass('MapBlockDrop')) {

        $('.strongBlock:last').next().remove();

    }

    if ($('.strongBlock:last').hasClass('strongBlockTop')) {

        $('.strongBlock:last').remove();

    }

    $('.blockSystem').html('');


    $('.blockLeftCont[data-type=block_vvoda], .blockLeftCont[data-type=block_gvs]').css('display', 'block');

    $('#blockLeft').getNiceScroll().resize();

    statusGVS = 1;

    conclusionLineStart();

    var oneHoverBlock = '<div class="divBotPosition"><i class="fa fa-pencil"></i> <i class="fa fa-trash" onclick="deleteBlockOne(this)"></i></div><div class="insertBlockNew insertBlockNull" onclick="menuType()"><div class="clickDpMenu insertBlockNewMax"><span onclick="modalGenerator(\'block_vvoda\',\'\',\'\',$(this).parents(\'.mapOneBlockDrop\')); ">Вставить блок ввода</span></div><div class="clickDpMenu insertBlockNewMax"><span onclick="modalGenerator(\'block_gvs\',\'\',\'\',$(this).parents(\'.mapOneBlockDrop\'))">Вставить блок ГВС</span></div><div class="clickDpMenu insertBlockNewMax"><span onclick="modalGenerator(\'block_otopleniya\',\'\',\'\',$(this).parents(\'.mapOneBlockDrop\'))">Вставить блок отопления</span></div><div class="clickDpMenu insertBlockNewMax"><span onclick="modalGenerator(\'block_podpitky\',\'\',\'\',$(this).parents(\'.mapOneBlockDrop\'))">Вставить блок подпитки</span></div><div class="clickDp"><div class="iconHover"></div><span>Вставить блок</span></div></div>';
    $('.mapOneBlockDrop').addClass('blockActiveHover').html(oneHoverBlock);

}


function deleteBlock(aThis) {

    statusDelete = false;

    if ($(aThis).parents('.draggable').find('img').attr('data-type') == 'block_vvoda') {

        $(aThis).parents('.blockVvodaDS').html(cloneBlockVvoda);

        dropFn();

        $('.blockLeftCont[data-type=block_vvoda]').css('display', 'block');

        $('#blockLeft').getNiceScroll().resize();


    } else if ($(aThis).parents('.draggable').find('img').attr('data-type') == 'block_otopleniya') {


        if ($(aThis).parents('.blockOtopleniyaList').hasClass("blockOtopleniyaList")) {

            $(aThis).parents('.blockOtopleniyaList').addClass('blockActive').html(cloneBlockOtopleniyaList);

            dropFn();

        } else {

            if ($(aThis).parents('.strongBlock').next().hasClass('strongBlockTop')) {


                $(aThis).parents('.strongBlock').next().remove();


            }

            $(aThis).parents('.MapBlockDrop').html('');



        }


        $('.blockSystem').each(function() {


            if ($(this).html() != '') {


                if (!$(this).prev().find('img').attr('src')) {

                    $(this).html('');

                }


            }


        });


    } else if ($(aThis).parents('.draggable').find('img').attr('data-type') == 'block_podpitky') {

        $(aThis).parents('.blockPodpitkyList').addClass('blockActive').html(blockPodpitkyList);

        dropFn();




    } else if ($(aThis).parents('.draggable').find('img').attr('data-type') == 'block_gvs') {

        $(aThis).parents('.MapBlockDrop').remove();

        $('.blockLeftCont[data-type=block_gvs]').css('display', 'block');

        $('#blockLeft').getNiceScroll().resize();

        statusGVS = 1;


    }

    $('#svg-line .line').remove();

    conclusionLineStart();


}


// Чекбоксы в блоках ввода

function check(aThis) {

    var arrFr = new Array(),
        //блок ввода
        du1 = '[data-uzel_ucheta="0"]',
        du2 = '[data-regul_dav_t2="0"]',
        du3 = '[data-bal_klapan="0"]',
        du4 = '[data-regul_dav_t1="0"]',
        //блок отопления и гвс
        duo1 = '[data-nasos_type="0"]',
        duo2 = '[data-ras_nasos="0"]',
        duo3 = '[data-teplo="0"]',
        duo4 = '[data-reg_t="0"]',
        duo5 = '[data-bov_type="0"]',
        dug1 = '[data-gvs_type="0"]',
        chekStatus = 0;


    if ($('.box-modal #uzel_ucheta').prop("checked")) {

        du1 = '[data-uzel_ucheta="1"]';

    } else {

        du1 = '[data-uzel_ucheta="0"]';
        chekStatus += 1;

    }

    if ($('.box-modal #regul_dav_t2').prop("checked")) {

        du2 = '[data-regul_dav_t2="1"]';

    } else {

        du2 = '[data-regul_dav_t2="0"]';
        chekStatus += 1;

    }

    if ($('.box-modal #regul_dav_t1').prop("checked")) {

        du4 = '[data-regul_dav_t1="1"]';

    } else {

        du4 = '[data-regul_dav_t1="0"]';
        chekStatus += 1;

    }

    if ($('.box-modal #bal_klapan').prop("checked")) {

        du3 = '[data-bal_klapan="1"]';

    } else {

        du3 = '[data-bal_klapan="0"]';
        chekStatus += 1;

    }

    const sel1 = $('.box-modal #nasos_type_sel');
    //console.log(sel1);
    //console.log(sel1.val());
    if (sel1.length > 0) {
        duo1 = `[data-nasos_type="${sel1.val()}"]`;

    } else {

        duo1 = '[data-nasos_type="0"]';
        chekStatus += 1;

    }

    const sel2 = $('.box-modal #ras_nasos_sel');
    if (sel2.length > 0) {
        duo2 = `[data-ras_nasos="${sel2.val()}"]`;

    } else {

        duo2 = '[data-ras_nasos="0"]';
        chekStatus += 1;

    }

    const sel3 = $('.box-modal #teplo_sel');
    if (sel3.length > 0) {
        duo3 = `[data-teplo="${sel3.val()}"]`;

    } else {

        duo3 = '[data-teplo="0"]';
        chekStatus += 1;

    }

    const sel4 = $('.box-modal #reg_t_sel');
    if (sel4.length > 0) {
        duo4 = `[data-reg_t="${sel4.val()}"]`;

    } else {

        duo4 = '[data-reg_t="0"]';
        chekStatus += 1;

    }

    const sel5 = $('.box-modal #bov_type_sel');
    if (sel5.length > 0) {
        duo5 = `[data-bov_type="${sel5.val()}"]`;
        if (sel5.val() == '2') {
            duo3 = '[data-teplo="0"]';
            duo4 = '[data-reg_t="1"]';
            sel3.prop('disabled', 'disabled');
            sel4.prop('disabled', 'disabled');
        } else {
            sel3.prop('disabled', false);
            sel4.prop('disabled', false);
        }

    } else {

        duo5 = '[data-bov_type="0"]';
        chekStatus += 1;

    }

    const selg1 = $('.box-modal #gvs_type_sel');
    if (selg1.length > 0) {
        dug1 = `[data-gvs_type="${selg1.val()}"]`;

    } else {

        dug1 = '[data-gvs_type="0"]';
        chekStatus += 1;

    }




    console.log('.box-modal .carousel-element' + du1 + du2 + du3 + du4 + duo1 + duo2 + duo3 + duo4 + duo5 + dug1);



    //if(chekStatus != 3){

    $('.box-modal .carousel-element').css('display', 'none');
    $('.box-modal .carousel-element' + du1 + du2 + du3 + du4 + duo1 + duo2 + duo3 + duo4 + duo5 + dug1).css('display', 'table-cell');
    caruselClickActive($('.box-modal .carousel-element' + du1 + du2 + du3 + du4 + duo1 + duo2 + duo3 + duo4 + duo5 + dug1).eq(0));


    //}


    adapCarusel();


}


function typeNag(aThis) {


    $('.box-modal  .vhodDanie input[name=typeNag]').each(function() {


        if ($(this).prop("checked")) {


            if ($(this).val() == '1') {

                $('.box-modal #rd1').css('display', 'inline-block');
                $('.box-modal #rd2').css('display', 'none');

            } else {

                $('.box-modal #rd1').css('display', 'none');
                $('.box-modal #rd2').css('display', 'inline-block');

            }


        }


    });


}


function shPodkl(aThis) {


    var qblockOtopleniyaJson,
        qgvs = 0,
        gvsThis = $('.box-modal input[name="qGVS"]');

    $('.spanHidden[data-type="block_otopleniya"]').each(function() {
        qblockOtopleniyaJson = $.parseJSON($(this).text());

        qgvs += Number(qblockOtopleniyaJson.block_otopleniya.q);

    });

    if ($(aThis).prop("checked")) {


        if ($(aThis).val() == '2') {


            $('.box-modal .carousel-element[data-id="17"]').css('display', 'none');
            $('.box-modal .carousel-element[data-id="18"]').css('display', 'table-cell');


        } else if ($(aThis).val() == '1') {


            $('.box-modal .carousel-element[data-id="17"], .box-modal .carousel-element[data-id="18"]').css('display', 'table-cell');

            if (0.2 < (Number(gvsThis.val()) / qgvs) && (Number(gvsThis.val()) / qgvs) < 1) {

                $('.box-modal .carousel-element[data-id="18"]').css('display', 'none');

            } else {

                $('.box-modal .carousel-element[data-id="17"]').css('display', 'none');

            }


        } else if ($(aThis).val() == '3') {


            $('.box-modal .carousel-element[data-id="17"]').css('display', 'table-cell');
            $('.box-modal .carousel-element[data-id="18"]').css('display', 'none');


        }


    }


}


//функция изменения описания полей  блока БОВ при выборе отопления или вентиляции
function typeBlock() {
    $('.box-modal  .vhodDanie input[name=typeBlock]').each(function() {
        if ($(this).prop("checked")) {
            var dataName = $(this).attr('data-name');
            $('.box-modal .modalCharacteristic[data-type=block_otopleniya]').prev('h3').text('Блок ' + dataName);
            $('.box-modal #t11').attr('tooltip', 'Температура в подающем трубопроводе ' + dataName);
            $('.box-modal #t21').attr('tooltip', 'Температура в обратном трубопроводе ' + dataName);
            $('.box-modal #dp').attr('tooltip', 'Потери давления в системе ' + dataName);
            if ($(this).val() == '1') {
                $('.box-modal #hVs').attr('tooltip', 'Высота здания');

            } else if ($(this).val() == '2') {
                $('.box-modal #hVs').attr('tooltip', 'Максимальная высота установки, калорифера');

            } else if ($(this).val() == '3') {
                $('.box-modal #hVs').attr('tooltip', 'Максимальная высота установки технологического оборудования');

            }
        }
    });
}


function sravOtop() {


    var t1 = $('.box-modal select[name=t1] option:selected').text(),
        t11 = $('.box-modal select[name=t11] option:selected').text();

    if (t1 && t11) {

        if (t1 == t11) {

            $('.box-modal .carousel-element[data-id=20]').css('display', 'none');
            $('.box-modal .carousel-element[data-id=21]').css('display', 'table-cell');


        } else {


            $('.box-modal .carousel-element[data-id=20]').css('display', 'table-cell');
            $('.box-modal .carousel-element[data-id=21]').css('display', 'none');


        }

    } else {


        $('.box-modal .carousel-element[data-id=20], .box-modal .carousel-element[data-id=21]').css('display', 'table-cell');


    }

    adapCarusel();

}

function timeout(ms) {
    return new Promise(resolve => setTimeout(resolve, ms));
}

async function loadDataResult(id) {
    let data;
    try {
        const json = await $.post("/ajax.php?do=loadDataResult", { id });
        data = JSON.parse(json);
        if (data.result) {
            return data;
        } else {
            if (window.stopLoadDataResult) {
                return false;
            }
            await timeout(5000);
            return await loadDataResult(id);
        }
    } catch (error) {
        return { error };
    }
}

// --- Расчеты ---

function calculateCostZp(a) {

    $('.raschetStoimostyLoading').css('display', 'block');
    $('.raschetStoimostyResult').css('display', 'none');

    var c = $('<div class="box-modal" />');
    c.html($('#raschetStoimostySource').html());

    window.stopLoadDataResult = false;
    const btn_close = $(`<div class="box-modal_close arcticmodal-close">закрыть</div>`);
    btn_close.on("click", function() {
        window.stopLoadDataResult = true;
        $(".box-modal").css({ "background-color": "#fff" });
    });

    c.prepend(btn_close);
    $.arcticmodal({
        content: c
    });

    let time = 180; //секунд

    const loaderTextTimer = $(".loaderTextTimer");
    loaderTextTimer.html(``);

    const timerId = setInterval(function(){
        if(time >= 0){
            const minute = Math.floor(time / 60);
            const minute_v = ('0' + minute).slice(-2);
            const sec = time - minute * 60;
            const sec_v = ('0' + sec).slice(-2);
            loaderTextTimer.html(`${minute_v}:${sec_v}`);
            time--;
        } else {
            clearTimeout(timerId);
            loaderTextTimer.html(`Прервано соединение с интернетом. Пожалуйста повторите попытку.`);
        }
    }, 1000);

    $(".box-modal").css({ "background-color": "#e7e7e7" });

    let iUrl = window.location.href.split("/");
    iUrl = iUrl[iUrl.length - 1];

    if (a == 1) {

        var postDataAjax = '';

        $('#constructMap .spanHidden').each(function(ind) {

            if ($(this).text()) {

                if (ind) { postDataAjax += '||' }
                postDataAjax += 'a' + ind + ':' + $(this).text();

            }


        });



        var jsonQSum = 0;

        $('.MapBlockDropList span[data-type="block_otopleniya"], .MapBlockDropList span[data-type="block_gvs"]').each(function() {

            var jsonq = $.parseJSON($(this).text());


            if (jsonq.block_otopleniya) {

                jsonQSum += parseInt(jsonq.block_otopleniya.q);

            } else if (jsonq.block_gvs) {

                jsonQSum += parseInt(jsonq.block_gvs.qGVS);

            }


        });

        //alert(jsonQSum);

        postDataAjax = postDataAjax.replace(/block_vvoda": [^]"q": "[0-9.,]"/, 'block_vvoda": {"q": "' + jsonQSum + '"');

        $.post(
            "https://atri-energo.ru/ajax.php?do=getBlocksCalculation", {

                iData: $('#i-data').text(),
                iDataGeneral: $('#i-data-general').text(),
                iUrl: iUrl,
                postData: postDataAjax,
                iq: $('#i-q').text()

            },
            async function onAjaxSuccess(data) {
                const resultData = await loadDataResult(iUrl);
                if (resultData) {
                    if (resultData?.error && resultData?.error.length > 0) {
                        // alert(resultData.error);
                    } else {
                        console.log(resultData);
                        $('.raschetStoimostyResult').html(resultData.template);
                    }
                }
                $("#raschetStoimostySource").find(".raschetStoimostyResult").children().remove();
                $('.raschetStoimostyLoading').css('display', 'none');
                $('.raschetStoimostyResult').css('display', 'block');

                // let res = '<div class="titleRas"><h1>Результаты расчетов</h1></div><div>';


                // let json = JSON.parse(data);

                // if (!('error' in json)) {

                //     res += '<div class="groupBlock"><div><h3>Блочный тепловой пункт (БТП: ' + json["group"]["code"] + ')</h3></div>';

                //     if (json["group"]["id"] == -1) {

                //         res += '<div class="info-msg1"><i class="fa fa-info-circle"></i> По данному БТП информация отсутствует. Наши сотрудники подготовят данные в течении 1 часа. Результат выполнения вы можете посмотреть в разделе мои расчеты.</div>';

                //     } else {

                //         res += '<div class="result-div-bot"><a class="btnClassic" href="/selection/print/pz/' + iUrl + '">Получить ПЗ</a><a class="btnClassic" href="/selection/specification_all/' + iUrl + '">Получить общую спецификацию</a><a class="btnClassic" href="/download/' + json["group"]["aid_scheme"] + '">Получить общую схему</a></div>';

                //     }
                //     res += '</div>';
                //     res += '<div class="groupBlock">';

                //     for (let i = 0; i < json["blocks"].length; i++) {

                //         res += '<div class="hrBlock"><div><h3>' + json["blocks"][i]["type"] + ' - ' + json["blocks"][i]["code"] + '</h3></div>';

                //         if (json["blocks"][i]["id"] == -1) {

                //             res += '<div class="info-msg1"><i class="fa fa-info-circle"></i> По данному блоку информация отсутствует. Наши сотрудники подготовят данные в течении 1 часа. Результат выполнения вы можете посмотреть в разделе мои расчеты.</div>';

                //         } else {

                //             res += '<div class="result-div-bot">';

                //             if (json["blocks"][i]["aid_scheme"] != -1) {

                //                 res += '<a href="/download/' + json["blocks"][i]["aid_scheme"] + '" class="btnClassic">Получить схему</a>';

                //             }

                //             if (json["blocks"][i]["aid_size"] != -1) {

                //                 res += '<a href="/download/' + json["blocks"][i]["aid_size"] + '" class="btnClassic">Получить размеры</a>';

                //             }

                //             res += '<a href="/catalog_v2/specification/selection/' + json["blocks"][i]["id"] + '" class="btnClassic">Получить спецификацию</a>';
                //             res += '</div>';

                //         }

                //         res += '</div>';

                //     }
                //     res += '</div>';
                // } else {
                //     res += json["error"];
                // }
                // res += '</div>';

                // $('.box-modal .resultModalBot').css('display', 'block');

                // $('.raschetStoimostyResult').html(res);
            }
        );

    } else if (a == 2) {

        $.post(
            "https://atri-energo.ru/ajax.php?do=getBlocksCalculation", {

                iData: $('.MapBlockOneImg .spanHidden').text(),
                iUrl: iUrl,


            },
            function onAjaxSuccess(data) {

                //$('.raschetStoimostyResult').html(data);
                $('.raschetStoimostyLoading').css('display', 'none');
                $('.raschetStoimostyResult').css('display', 'block');

                let res = '<div class="titleRas"><h1>Результаты расчетов</h1></div><div>';

                let json = JSON.parse(data);

                if (!('error' in json)) {

                    res += '<div class="groupBlock"><div><h3>Блочный тепловой пункт (БТП: ' + json["group"]["code"] + ')</h3></div>';

                    if (json["group"]["id"] == -1) {

                        res += '<div class="info-msg1"><i class="fa fa-info-circle"></i> По данному БТП информация отсутствует. Наши сотрудники подготовят данные в течении 1 часа. Результат выполнения вы можете посмотреть в разделе мои расчеты.</div>';

                    } else {

                        res += '<div class="result-div-bot"><a class="btnClassic" href="/selection/print/pz/' + iUrl + '">Получить ПЗ</a><a class="btnClassic" href="/selection/specification_all/' + iUrl + '">Получить общую спецификацию</a><a class="btnClassic" href="/download/' + json["group"]["aid_scheme"] + '">Получить общую схему</a></div>';

                    }
                    res += '</div>';
                    res += '<div class="groupBlock">';

                    for (let i = 0; i < json["blocks"].length; i++) {

                        res += '<div class="hrBlock"><div><h3>' + json["blocks"][i]["type"] + ' - ' + json["blocks"][i]["code"] + '</h3></div>';

                        if (json["blocks"][i]["id"] == -1) {

                            res += '<div class="info-msg1"><i class="fa fa-info-circle"></i> По данному блоку информация отсутствует. Наши сотрудники подготовят данные в течении 1 часа. Результат выполнения вы можете посмотреть в разделе мои расчеты.</div>';

                        } else {

                            res += '<div class="result-div-bot">';

                            if (json["blocks"][i]["aid_scheme"] != -1) {

                                res += '<a href="/download/' + json["blocks"][i]["aid_scheme"] + '" class="btnClassic">Получить схему</a>';

                            }

                            if (json["blocks"][i]["aid_size"] != -1) {

                                res += '<a href="/download/' + json["blocks"][i]["aid_size"] + '" class="btnClassic">Получить размеры</a>';

                            }

                            res += '<a href="/catalog_v2/specification/selection/' + json["blocks"][i]["id"] + '" class="btnClassic">Получить спецификацию</a>';
                            res += '</div>';

                        }

                        res += '</div>';

                    }
                    res += '</div>';
                } else {
                    res += json["error"];
                }
                res += '</div>';

                $('.box-modal .resultModalBot').css('display', 'block');

                $('.raschetStoimostyResult').html(res);
            }
        );

    }

}


function calculateCost() {

    if (!$('.blockVvodaDS img').attr('src')) {


        systemMessage('error', 'Вставьте блок ввода');

        return false;


    }

    var mStatus = 0;

    $('.MapBlockDropList .dropBlock img').each(function() {

        if ($(this).attr('src')) {

            mStatus = 1;

        }

    });

    if (mStatus == 0) {


        systemMessage('error', 'Вставьте блок отопления или блок ГВС');

        return false;


    }

    mStatus = 0;


    if (!$('.dropBlock img').attr('src')) {


        systemMessage('error', 'Вставьте блок ввода');

        return false;


    }


    calculateCostZp(1);


}

function selT11(aThis) {

    var selectedOption = aThis.options[aThis.selectedIndex];

    $('.box-modal .carousel-element[data-id="19"], .box-modal .carousel-element[data-id="20"]').css('display', 'table-cell');
    $('.box-modal .carousel-element[data-id="21"]').css('display', 'none');

    if (selectedOption.value == '95') {

        if ($('.box-modal select[name=t11] option:selected').text() == '95') {


            $('.box-modal .carousel-element[data-id="19"], .box-modal .carousel-element[data-id="20"]').css('display', 'none');
            $('.box-modal .carousel-element[data-id="21"]').css('display', 'table-cell');


        }



        $('.box-modal .modalCharacteristic[data-type=block_otopleniya] select[name=t21] option:contains("70")').prop('selected', true);

    } else if (selectedOption.value == '90') {

        $('.box-modal .modalCharacteristic[data-type=block_otopleniya] select[name=t21] option:contains("65")').prop('selected', true);

    } else if (selectedOption.value == '85') {

        $('.box-modal .modalCharacteristic[data-type=block_otopleniya] select[name=t21] option:contains("60")').prop('selected', true);

    } else if (selectedOption.value == '80') {

        $('.box-modal .modalCharacteristic[data-type=block_otopleniya] select[name=t21] option:contains("60")').prop('selected', true);

    }

    return false;

}

function selT21(aThis) {

    var t11Select = $('.box-modal select[name=t11] option:selected').text();

    var selectedOption = aThis.options[aThis.selectedIndex];

    if (selectedOption.value == '70') {


        $('.box-modal .modalCharacteristic[data-type=block_otopleniya] select[name=t11] option:contains("95")').prop('selected', true);


    } else if (selectedOption.value == '65') {


        $('.box-modal .modalCharacteristic[data-type=block_otopleniya] select[name=t11] option:contains("90")').prop('selected', true);


    } else if (selectedOption.value == '60') {

        if (t11Select != '85' && t11Select != '80') {


            $('.box-modal .modalCharacteristic[data-type=block_otopleniya] select[name=t11] option:contains("85")').prop('selected', true);


        }

    }

    return false;
}

// Показать скрыть блок с теплообменником при температуре 95
function selT1(aThis) {

    var t11Select = $('.box-modal select[name=t11] option:selected').text();

    var selectedOption = aThis.options[aThis.selectedIndex];

    if (selectedOption.value == '95') {

        $('.box-modal .carousel-element[data-id="19"], .box-modal .carousel-element[data-id="20"]').css('display', 'none');
        $('.box-modal .carousel-element[data-id="21"]').css('display', 'table-cell');

    } else {

        $('.box-modal .carousel-element[data-id="21"]').css('display', 'none');
        $('.box-modal .carousel-element[data-id="19"], .box-modal .carousel-element[data-id="20"]').css('display', 'table-cell');

    }

}



// --- Работа со шкафами ---

function shkafDpInter(aThis) {

    if ($(aThis).prop("checked")) {


        $('.box-modal #skafDi').css('display', 'inline-block');


    } else {


        $('.box-modal #skafDi').css('display', 'none');


    }

}


function shkafDpOborudovanie(aThis) {


    if ($(aThis).prop("checked")) {


        $('.box-modal #shkafDpo').css('display', 'inline-block');


    } else {


        $('.box-modal #shkafDpo').css('display', 'none');


    }


}


function qgvs(aThis) {

    // $('.box-modal .carousel-element').css('display', 'table-cell');

    // var qblockOtopleniyaJson,
    //     qgvs = 0;

    // $('.spanHidden[data-type="block_otopleniya"]').each(function() {
    //     qblockOtopleniyaJson = $.parseJSON($(this).text());

    //     qgvs += Number(qblockOtopleniyaJson.block_otopleniya.q);

    // });


    // if ($(aThis).val() != '') {


    //     if (0.2 < (Number($(aThis).val()) / qgvs) && (Number($(aThis).val()) / qgvs) < 1) {

    //         $('.box-modal .carousel-element[data-id="18"]').css('display', 'none');

    //     } else {

    //         $('.box-modal .carousel-element[data-id="17"]').css('display', 'none');

    //     }


    // } else {

    //     $('.box-modal .carousel-element[data-id="17"], .box-modal .carousel-element[data-id="18"]').css('display', 'table-cell');

    // }


}


function blPodpitSogSp(aThis, v) {


    if (v == 0) {


        $('.box-modal .carousel-element[data-id="22"]').css('display', 'table-cell');
        $('.box-modal .carousel-element[data-id="23"]').css('display', 'none');
        caruselClickActive($('.box-modal .carousel-element[data-id="22"]').eq(0));


    } else if (v == 1) {


        $('.box-modal .carousel-element[data-id="22"]').css('display', 'none');
        $('.box-modal .carousel-element[data-id="23"]').css('display', 'table-cell');
        caruselClickActive($('.box-modal .carousel-element[data-id="23"]').eq(0));

    } else if (v == 2) {


        // Согласно СП, вычисления блока подпитки
        var proverkaNasosov = 0;


        $('.MapBlockDropList .spanHiddenNasos').each(function() {

            proverkaNasosov += parseInt($(this).text());

        });


        if (proverkaNasosov >= 2) {

            $('.box-modal .carousel-element[data-id="22"]').css('display', 'table-cell');
            $('.box-modal .carousel-element[data-id="23"]').css('display', 'none');
            caruselClickActive($('.box-modal .carousel-element[data-id="22"]').eq(0));


        } else {

            $('.box-modal .carousel-element[data-id="23"]').css('display', 'table-cell');
            $('.box-modal .carousel-element[data-id="22"]').css('display', 'none');
            caruselClickActive($('.box-modal .carousel-element[data-id="23"]').eq(0));

        }



    }


}



// ---------------------------- //
// Показывает насосы по формуле //
/* ---------------------------- */
function prNasos(aThis) {


    var prH = '',
        prP2 = '';

    prH = $('.box-modal .modalCharacteristic[data-type="block_otopleniya"] input[name="h"]');
    prP2 = $('.box-modal .modalCharacteristic[data-type="block_otopleniya"] input[name="p2"]');
    prP2Tp = 0;


    if (prH.val() && prP2.val()) {

        if ($('.box-modal select[name=p2Type] option:selected').text() == 'кгс/см²') {

            prP2Tp = Number(prP2.val()) * 10;

        } else if ($('.box-modal select[name=p1Type] option:selected').text() == 'МПа') {

            prP2Tp = Number(prP2.val()) * 100;

        } else {

            prP2Tp = Number(prP2.val());

        }


        var proverkaNasosov = 0;


        $('.MapBlockDropList .spanHiddenNasos').each(function() {

            proverkaNasosov += parseInt($(this).text());

        });




        // Показывает насосы по формуле
        if (Number(prH.val()) - prP2Tp > 5) {


            $('#st-i-nasos').val('1');


            /*if($('.blockVvodaDS .blockImg').attr('data-lile') == 1) {*/


            $('.box-modal .bvNasos').val('1');


            /*}*/


            // if (proverkaNasosov >= 2) {


            //     if ($('.blockVvodaDS .insertBlockNew span').text() != 'Вставить блок ввода') {


            //         $('.blockVvodaDS .blockImg').attr('src', $('.blockVvodaDS .blockImg').attr('src').replace('/block_vvoda/', '/block_vvoda_n/'));


            //     }


            // }


        } else {



            if (proverkaNasosov == 0 || proverkaNasosov == 1) {


                $('#st-i-nasos').val('0');


                if ($('.blockVvodaDS .blockImg').attr('data-lile') == 1) {

                    $('.blockVvodaDS .blockImg').attr('src', $('.blockVvodaDS .blockImg').attr('src').replace('/block_vvoda_n/', '/block_vvoda/'));


                }


            }

            $('.box-modal .bvNasos').val('0');

        }



    }


}


/* --- Свободная форма --- */

var modalBlockOtopleniya = $('.modal-block-otopleniyac').clone(),
    modalBlockOtopleniyaGvs = $('.modal-characteristic-gvs').clone();

function svobodnayaForma() {

    var c = $('<div class="box-modal svobodnaya-forma" />');
    c.html($('#svobodnayaForma').html());
    c.prepend('<div class="box-modal_close arcticmodal-close">закрыть</div>');
    $.arcticmodal({
        content: c,
        closeOnOverlayClick: false
    });



}


function sbPrev() {


    var sbNext = $('.box-modal .sf-active').prevAll('.shag-sf-true').eq(0);

    if (sbNext.length) {

        $('.box-modal .sf-active').removeClass('sf-active');
        sbNext.addClass('sf-active');
        $('.box-modal .shag-content').fadeOut(500);
        sbNext.delay(550).fadeIn(500);


        if ($('.box-modal .sf-active').hasClass('sf-first')) {


            $('.box-modal .modal-form-bot-left').css('display', 'none');



        } else {


            $('.modal-form-bot-right').css('display', 'inline-block');
            $('.box-modal .modal-form-bot-save').css('display', 'none');



        }


    }


}


function sbNext() {


    var sbNext = $('.box-modal .sf-active').nextAll('.shag-sf-true').eq(0);

    if (sbNext.length) {

        $('.box-modal .sf-active').removeClass('sf-active');
        sbNext.addClass('sf-active');
        $('.box-modal .shag-content').fadeOut(500);
        sbNext.delay(550).fadeIn(500);


        $('.modal-form-bot-left').css('display', 'inline-block');

        if ($('.box-modal .sf-active').hasClass('sf-last')) {

            $('.box-modal .modal-form-bot-right').css('display', 'none');
            $('.box-modal .modal-form-bot-save').css('display', 'inline-block');



        } else {


            $('.modal-form-bot-right').css('display', 'inline-block');
            $('.box-modal .modal-form-bot-save').css('display', 'none');



        }


    }

}



function sfBlockVvoda(aThis) {

    var selectedOption = aThis.options[aThis.selectedIndex];


    if (parseInt(selectedOption.value) > 0) {

        $('.box-modal .shag-content-block-vvoda').addClass('shag-sf-true');

    } else {

        $('.box-modal .shag-content-block-vvoda').removeClass('shag-sf-true');

    }

}


function kolvoSistem() {

    if (parseInt($('.box-modal select[name=bovSel] option:selected').text()) == 0 && parseInt($('.box-modal select[name=gvsSel] option:selected').text()) == 0) {

        $('.kolBlockVvDiv').slideDown(600);

    } else {

        $('.kolBlockVvDiv').slideUp(600);

    }

}


function kolBlockVv(aThis) {


    $('.box-modal .qNagruzkaFor, .box-modal .qNagruzkaGvsFor').remove();

    var inputBody = '';

    for (var i = 1; $(aThis).val() >= i; i++) {


        inputBody += '<div class="vhodDanie qNagruzkaFor" tooltip="Нагрузка">Q<span class="qovSpanB">ов</span><span class="qovSpan">' + i + '</span> <input class="SFqBOVBv" oninput="" type="text" name="SFqBOVBv"><select name="SFqBOVType"><option>Гкал/ч</option><option>Ккал/ч</option><option>МВт</option><option>Вт</option></select></div>';


    }

    for (var i = 1; $(aThis).val() >= i; i++) {


        inputBody += '<div class="vhodDanie qNagruzkaGvsFor" tooltip="Нагрузка">Q<span class="qovSpanB">гв</span><span class="qovSpan">' + i + '</span> <input class="SFqGVSBv" oninput="" type="text" name="SFqGVSBv"><select name="SFqGVSType"><option>Гкал/ч</option><option>Ккал/ч</option><option>МВт</option><option>Вт</option></select></div>';


    }


    $('.box-modal .qNagruzkaBv').after(inputBody);




}


function sfBOV(aThis) {

    var selectedOption = aThis.options[aThis.selectedIndex];


    if (parseInt(selectedOption.value) > 0) {

        $('.box-modal .shag-content-bov').addClass('shag-sf-true');

        $('.box-modal .modalCharacteristic').html('');


        for (var i = 0; i < parseInt(selectedOption.value); i++) {

            $('.box-modal .modalCharacteristic').append(modalBlockOtopleniya.html());

        }


    } else {

        $('.box-modal .shag-content-bov').removeClass('shag-sf-true');

    }

    kolvoSistem();

}

function sfGVS(aThis) {

    var selectedOption = aThis.options[aThis.selectedIndex];


    if (parseInt(selectedOption.value) > 0) {

        $('.box-modal .shag-content-gvs').addClass('shag-sf-true');

        $('.box-modal .modal-characteristic-gvs').html('');


        for (var i = 0; i < parseInt(selectedOption.value); i++) {

            $('.box-modal .modal-characteristic-gvs').append(modalBlockOtopleniyaGvs.html());

        }


    } else {

        $('.box-modal .shag-content-gvs').removeClass('shag-sf-true');

    }

    kolvoSistem();

}


function chekInputRg(aThis) {


    if ($(aThis).prop("checked")) {

        $(aThis).parent('label').next().removeAttr('disabled');

    } else {

        $(aThis).parent('label').next().attr('disabled', 'disabled');

    }


}

function sdBP(aThis) {


    if ($(aThis).prop("checked")) {

        $(aThis).parents('.labPt').next().slideDown(200);

    } else {

        $(aThis).parents('.labPt').next().slideUp(200);

    }


}


var m = 2;

function br_n(aThis) {

    if ($(aThis).val() == '') {

        $(aThis).parents('.sfBR').append('<div class="sfBrOtv"><div>Ответвление №' + m + '</div><div class="vhodDanie">Ду <input onkeydown="br_n(this)" type="text" name="SFdu"> мм</div><div class="vhodDanie labPt"><label><input class="br_klapan" name="br_klapan" type="checkbox"> Балансировочный клапан </label></div></div>');

        m++;

    }

}


function fourTruba(aThis) {

    if ($(aThis).prop("checked")) {


        if (parseInt($('.box-modal .vibor-form-div select[name=bovSel] option:selected').text()) == 0 && parseInt($('.box-modal .vibor-form-div select[name=gvsSel] option:selected').text()) == 0 && $('.box-modal .kolBlockVv').val() != '') {


            $('.box-modal .bSFt1, .box-modal .bSFt2, .box-modal .bSFp1, .box-modal .bSFp2').css('display', 'none');
            $('.box-modal .bSFt3, .box-modal .bSFt4, .box-modal .bSFp3, .box-modal .bSFp4').css('display', 'inline-block');

        } else {

            $('.box-modal .bSFt3, .box-modal .bSFt4, .box-modal .bSFp3, .box-modal .bSFp4').css('display', 'inline-block');

        }

    } else {

        $('.box-modal .bSFt3, .box-modal .bSFt4, .box-modal .bSFp3, .box-modal .bSFp4').css('display', 'none');
        $('.box-modal .bSFt1, .box-modal .bSFt2, .box-modal .bSFp1, .box-modal .bSFp2').css('display', 'inline-block');

    }


}


function sbSave() {


    var dataJSON = '{"data": {"sfWidth": "' + $('.box-modal input[name=sfWidth]').val() + '",';
    dataJSON += '"sfLenght": "' + $('.box-modal input[name=sfLenght]').val() + '",';
    dataJSON += '"sfHeight": "' + $('.box-modal input[name=sfHeight]').val() + '",';
    dataJSON += '"sfHeightBuilding": "' + $('.box-modal input[name=sfHeightBuilding]').val() + '",';
    dataJSON += '"sfHeightBuildingType": "' + $('.box-modal .shag-content-block-vvoda select[name=sfHeightBuilding] option:selected').text() + '",';
    dataJSON += '"message": "' + $('.box-modal textarea[name=message]').val() + '",';
    dataJSON += '"files": "' + filesId + '",';
    dataJSON += '"nKolon": "' + $('.box-modal input[name=nKolon]:checked').val() + '"';
    dataJSON += '}}';

    //


    var sfDataJSON = '{"blockVvoda": {"q": "' + $('.box-modal .shag-content-block-vvoda input[name=SFq]').val() + '",';
    sfDataJSON += '"qType": "' + $('.box-modal .shag-content-block-vvoda select[name=SFqType] option:selected').text() + '",';
    sfDataJSON += '"t1": "' + $('.box-modal .shag-content-block-vvoda input[name=SFt1]').val() + '",';
    sfDataJSON += '"t2": "' + $('.box-modal .shag-content-block-vvoda input[name=SFt2]').val() + '",';
    sfDataJSON += '"p1": "' + $('.box-modal .shag-content-block-vvoda input[name=SFp1]').val() + '",';
    sfDataJSON += '"p2": "' + $('.box-modal .shag-content-block-vvoda input[name=SFp2]').val() + '",';

    if ($('.box-modal .fourTruba').prop("checked")) {

        sfDataJSON += '"t3": "' + $('.box-modal .shag-content-block-vvoda input[name=SFt3]').val() + '",';
        sfDataJSON += '"t4": "' + $('.box-modal .shag-content-block-vvoda input[name=SFt4]').val() + '",';
        sfDataJSON += '"p3": "' + $('.box-modal .shag-content-block-vvoda input[name=SFp3]').val() + '",';
        sfDataJSON += '"p4": "' + $('.box-modal .shag-content-block-vvoda input[name=SFp4]').val() + '",';
        sfDataJSON += '"p3Type": "' + $('.box-modal .shag-content-block-vvoda select[name=SFp3Type] option:selected').text() + '",';
        sfDataJSON += '"p4Type": "' + $('.box-modal .shag-content-block-vvoda select[name=SFp4Type] option:selected').text() + '",';

    }

    sfDataJSON += '"p1Type": "' + $('.box-modal .shag-content-block-vvoda select[name=SFp1Type] option:selected').text() + '",';
    sfDataJSON += '"p2Type": "' + $('.box-modal .shag-content-block-vvoda select[name=SFp2Type] option:selected').text() + '",';
    sfDataJSON += '"uzel_ucheta": "' + $('.box-modal .shag-content-block-vvoda #uzel_ucheta').prop("checked") + '",';
    sfDataJSON += '"podpitka": "' + $('.box-modal .shag-content-block-vvoda #podpitka').prop("checked") + '",';
    sfDataJSON += '"regulator_davleniya": "' + $('.box-modal .shag-content-block-vvoda #regulator_davleniya').prop("checked") + '",';
    sfDataJSON += '"SFregDavVal": "' + $('.box-modal .shag-content-block-vvoda input[name=SFregDavVal]').val() + '",';
    sfDataJSON += '"ogranichenie_rashoda": "' + $('.box-modal .shag-content-block-vvoda #ogranichenie_rashoda').prop("checked") + '",';

    if (parseInt($('.box-modal .kolBlockVv').val()) != '') {


        $('.box-modal .qNagruzkaFor').each(function(idx) {

            sfDataJSON += '"bov_q_' + idx + '": {';
            sfDataJSON += '"q": "' + $(this).find('input[name=SFqBOVBv]').val() + '",';
            sfDataJSON += '"qType": "' + $(this).find('select[name=SFqBOVType] option:selected').text() + '",';
            sfDataJSON += '},';

        });


        $('.box-modal .qNagruzkaGvsFor').each(function(idx) {

            sfDataJSON += '"gvs_q_' + idx + '": {';
            sfDataJSON += '"q": "' + $(this).find('input[name=SFqGVSBv]').val() + '",';
            sfDataJSON += '"qType": "' + $(this).find('select[name=SFqGVSType] option:selected').text() + '",';
            sfDataJSON += '},';

        });


    }

    sfDataJSON += '"SForgRasVal": "' + $('.box-modal .shag-content-block-vvoda input[name=SForgRasVal]').val() + '"';
    sfDataJSON += '}}';


    /* --- БОВ --- */
    var inx = 0;
    var sfDataJSONbov = '{';

    $('.box-modal .bov fieldset').each(function(indx) {


        if (inx > 0) { sfDataJSONbov += ','; }

        sfDataJSONbov += '"bov_' + indx + '": {"q": "' + $(this).find('input[name=SFq]').val() + '",';
        sfDataJSONbov += '"qType": "' + $(this).find('select[name=SFqType] option:selected').text() + '",';
        sfDataJSONbov += '"t1": "' + $(this).find('input[name=SFt1]').val() + '",';
        sfDataJSONbov += '"t2": "' + $(this).find('input[name=SFt2]').val() + '",';
        sfDataJSONbov += '"p1": "' + $(this).find('input[name=SFp1]').val() + '",';
        sfDataJSONbov += '"p1Type": "' + $(this).find('select[name=SFp1Type] option:selected').text() + '",';
        sfDataJSONbov += '"p2": "' + $(this).find('input[name=SFp2]').val() + '",';
        sfDataJSONbov += '"p2Type": "' + $(this).find('select[name=SFp2Type] option:selected').text() + '",';
        sfDataJSONbov += '"t11": "' + $(this).find('input[name=SFt11]').val() + '",';
        sfDataJSONbov += '"t21": "' + $(this).find('input[name=SFt21]').val() + '",';
        sfDataJSONbov += '"dp": "' + $(this).find('input[name=SFdp]').val() + '",';
        sfDataJSONbov += '"h": "' + $(this).find('input[name=SFh]').val() + '",';
        sfDataJSONbov += '"SFkontr": "' + $(this).find('input[name=SFkontr]').val() + '",';
        sfDataJSONbov += '"SFregKlapan": "' + $(this).find('input[name=SFregKlapan]').val() + '",';
        sfDataJSONbov += '"SFshkafAvtomatiky": "' + $(this).find('input[name=SFshkafAvtomatiky]').val() + '",';
        sfDataJSONbov += '"SFtypeBlock": "' + $(this).find('input[name=SFtypeBlock]:checked').attr('data-name') + '",';

        if ($(this).find('.sfBP') != 'none') {

            sfDataJSONbov += '"bp": {"SFnasosStan": "' + $(this).find('input[name=SFnasosStan]').val() + '",';
            sfDataJSONbov += '"SFnasosPov": "' + $(this).find('input[name=SFnasosPov]').val() + '",';
            sfDataJSONbov += '"SFklapanSol": "' + $(this).find('input[name=SFklapanSol]').val() + '",';
            sfDataJSONbov += '"SFrasshirBak": "' + $(this).find('input[name=SFrasshirBak]').val() + '"';
            sfDataJSONbov += '},';

        }


        if ($(this).find('.sfBR') != 'none') {

            $('.sfBrOtv').each(function(idx) {

                if ($(this).find('input[name=SFdu]').val() != '') {

                    sfDataJSONbov += '"br_' + idx + '": {"SFdu": "' + $(this).find('input[name=SFdu]').val() + '",';
                    sfDataJSONbov += '"br_klapan": "' + $(this).find('input[name=br_klapan]').prop("checked") + '"';
                    sfDataJSONbov += '},';

                }

            });

        }


        sfDataJSONbov += '"SFpch": "' + $(this).find('input[name=SFpch]:checked').val() + '"';
        sfDataJSONbov += '}';

        inx++;

    });

    sfDataJSONbov += '}';

    /* --- END БОВ --- */


    /* --- ГВС --- */

    var inx = 0;
    var sfDataJSONgvs = '{';

    $('.box-modal .gvs fieldset').each(function(indx) {

        if (inx > 0) { sfDataJSONgvs += ','; }

        sfDataJSONgvs += 'gvs_' + indx + '": {"qGVS": "' + $(this).find('input[name=SFqGVS]').val() + '",';
        sfDataJSONgvs += '"qSelGVS": "' + $(this).find('select[name=SFqSelGVS] option:selected').text() + '",';
        sfDataJSONgvs += '"GVSt1": "' + $(this).find('input[name=SFGVSt1]').val() + '",';
        sfDataJSONgvs += '"GVSt2": "' + $(this).find('input[name=SFGVSt2]').val() + '",';
        sfDataJSONgvs += '"p1": "' + $(this).find('input[name=SFp1]').val() + '",';
        sfDataJSONgvs += '"p1Type": "' + $(this).find('select[name=SFp1Type] option:selected').text() + '",';
        sfDataJSONgvs += '"p2": "' + $(this).find('input[name=SFp2]').val() + '",';
        sfDataJSONgvs += '"p2Type": "' + $(this).find('select[name=SFp2Type] option:selected').text() + '",';
        sfDataJSONgvs += '"GVSt3": "' + $(this).find('input[name=SFGVSt3]').val() + '",';
        sfDataJSONgvs += '"GVStv1": "' + $(this).find('input[name=SFGVStv1]').val() + '",';
        sfDataJSONgvs += '"pb1": "' + $(this).find('input[name=SFpb1]').val() + '",';
        sfDataJSONgvs += '"pb1SelGVS": "' + $(this).find('select[name=SFpb1SelGVS] option:selected').text() + '",';
        sfDataJSONgvs += '"kontrGVS": "' + $(this).find('input[name=SFkontrGVS]').val() + '",';
        sfDataJSONgvs += '"regKlapanGVS": "' + $(this).find('input[name=SFregKlapanGVS]').val() + '",';
        sfDataJSONgvs += '"shkafAvtomatikyGVS": "' + $(this).find('input[name=SFshkafAvtomatikyGVS]').val() + '",';
        sfDataJSONgvs += '"SFshemaPodkl": "' + $(this).find('input[name=SFshemaPodkl]:checked').attr('data-name') + '",';
        sfDataJSONgvs += '"SFpch": "' + $(this).find('input[name=SFpch]:checked').val() + '"';

        sfDataJSONgvs += '}';

        inx++;

    });

    sfDataJSONgvs += '}';

    /* --- END ГВС --- */


    $.post(
        "/ajax.php?do=selectCodeFree", {
            data: dataJSON,
            blockVvod: sfDataJSON,
            bov: sfDataJSONbov,
            gvs: sfDataJSONgvs
        },

        function onAjaxSuccess(data) {

            setTimeout(function() { postTimeout(data) }, 3000);

        }

    );


}

function postTimeout(a) {


    $('.box-modal .submiltLoader').css('display', 'none');

    if (a == 1) {

        $('.box-modal .sf-ok').css('display', 'block');

    } else {

        $('.box-modal .sf-error').css('display', 'block');

    }


}


// Функция вывода объектов на экран
function alertObj(obj) {
    var str = "";
    for (k in obj) {
        str += k + ": " + obj[k] + "\r\n";
    }
    // alert(str);
}

function change_gvs_type_sel_gvs(t) {
    const item = $(t);
    const value = item.val();
    const reg_t_sel = item.parent().parent().find("#reg_t_sel");
    switch (value) {
        default:
            case "1":
            reg_t_sel.val("1").change().prop("disabled", true);
        break;
        case "2":
                reg_t_sel.prop("disabled", false);
            break;
    }
}