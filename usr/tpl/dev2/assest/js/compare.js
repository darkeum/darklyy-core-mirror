$(document).ready(function(){

    if($(window).width() > 1200) {

        $(window).scroll(function () {

            if ($(this).scrollTop() > 380 && $('.wrap-content').height() > 790) {
                $('.compare-con-wrap-div').addClass('compare-header');
            } else {
                $('.compare-con-wrap-div').removeClass('compare-header');
            }

        });

    }

    compareSlider();

    $('.compare-con').append('<div class="compare-intem-top compare-intem-b active" data-id-cat="467"><div class="compare-intem-div-b"><span>Добавить еще товары из <br/> данной категории</span><a href="/">Перейти в каталог</a></div></div>');

    $('.compare-atr').append('<div class="compare-intem-top-atr active"></div>');

    $('.compare-intem-div-b a').attr('href', $('.compare-con .active').attr('data-url-cat'));


});


function compareNext(){

    var compareWdBlock = $('.compare-intem-top').width() + 30,
        compareWdBlockSum = $(".compare-con-wrap, .compare-atr-wrap").scrollLeft();

    if(1){

        compareWdBlockSum += compareWdBlock;

        $(".compare-con-wrap, .compare-atr-wrap").animate({ scrollLeft: compareWdBlockSum }, 500);

    }


}


function comparePrev(){

    var compareWdBlock = $('.compare-intem-top').width() + 30,
        compareWdBlockSum = $(".compare-con-wrap, .compare-atr-wrap").scrollLeft();

    if(1){

        compareWdBlockSum -= compareWdBlock;

        $(".compare-con-wrap, .compare-atr-wrap").animate({ scrollLeft: compareWdBlockSum }, 500);

    }


}

function compareTab(aThis) {

    $(".compare-con-wrap, .compare-atr-wrap").scrollLeft(0);

    if(!$(aThis).hasClass("active")){

        $('.compare-cats .active, .compare-con .active, .compare-con-by').removeClass("active");
        $(aThis).addClass("active");

        $('.compare-intem-top[data-id-cat='+$(aThis).attr('data-id')+']').addClass("active");
        $('.compare-con-by[data-id-cat='+$(aThis).attr('data-id')+']').addClass("active");

    }


    $('.compare-intem-b').addClass("active");

    $('.compare-intem-div-b a').attr('href', $('.compare-con .active').attr('data-url-cat'));

    compareSlider();

}


function compareSlider(){

    var i = 0;

    $('.compare-con .active').each(function(){

        i += $(this).width();


        if(i > $('.compare-con-wrap').width()){

            $('.compare-next, .compare-prev').css('display','block');

        }else{

            $('.compare-next, .compare-prev').css('display','none');

        }

    });

}


function compareClear() {

    $.cookie('compare', null, { expires: 1, path: '/' });

    compareNumber();

}


function compareDelete(aThis, bId){

    var strCompare = '',
        strArrCompare = $.cookie('compare').split(',');


    var k = 0;

    for(var i = 0; i < strArrCompare.length; i++){

        if(strArrCompare[i] != bId) {

            if(strArrCompare[i] != '') {

                if (k != 0) {
                    strCompare += ',';
                }

                strCompare += strArrCompare[i];

                k++;

            }

        }

    }


    if(strCompare == ''){strCompare = null;}

    $.cookie('compare', strCompare, { expires: 100, path: '/' });

    var dir = $('.compare-cats span[data-id='+$(aThis).parents('.compare-intem-top').attr('data-id-cat')+'] span');

    dir.text(parseInt(dir.text() - 1));

    $(aThis).parents('.compare-intem-top').remove();

    compareNumber();

}
