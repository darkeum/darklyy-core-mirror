var cart;
var config;var wiNumInputPrefID;

$(document).ready(function(){
	
	

    localStorage.removeItem("page");
    $.cookie('reghim', null, {path: '/' });

    /* --- Инициализация диапазона цен --- */
    jQuery("#slider").slider({
        min: 0,
        max: 50000,
        values: [5000,45000],
        range: true,
        slide: function( event, ui ) {
            $( "#amount" ).html("от <b>" + ui.values[0].toLocaleString('ru') + "</b>₽ до <b>" + ui.values[1].toLocaleString('ru') + "</b> ₽" );
        }
    });


    cart=new WICard("cart");
    config={'clearAfterSend':true,'showAfterAdd':false};cart.init("basketwidjet",config);
    WICartNum('cart','wicartnum','winum_');

    $('.slider-a-bot').hover(function(){

        $(this).addClass('activeSlider');

    },function(){

        $(this).removeClass('activeSlider');

    });

    document.addEventListener('visibilitychange',function(e){
        cart.init("basketwidjet",config);
    },false);

    /* --- Ajax подгрузка страниц --- */
    // let loading = true;
    // if($('.block-filter').length) {
    //     $(window).on("scroll", function () {
    //         if (($(window).scrollTop() + $(window).height() + $(window).height() * 0.2) >= $(document).height()) {
    //             if (loading) {
    //                 loading = false;
    //                 let page = localStorage.getItem("page");
    //                 page++;
    //                 localStorage.setItem("page", page);
    //                 let data = $("#ajax_filter_form").serialize();
    //                 let sort = get_sort();
                    
    //                 $('.cssload-jumping').css('display','block');

    //                 $.post(
    //                     "/catalog_v2/ajax/filter_list", "sort="+sort.sort+"&sort_type="+sort.sort_type+"&ajax=1&page=" + page + "&" + data,
    //                     function (data) {


    //                         $('.cssload-jumping').css('display','none');

    //                         ajaxFilterList(data);


    //                     if (data != '') {
    //                         loading = true;
    //                     }
    //                 });
    //             }
    //         }
    //     });
    // }

    $(document).on('click', '.pagination-widget__show-more-btn', function(){
        const elem = $(this);
        const page = paginator('next');
        paginator(page);
        elem.prop('disabled', true);
        const data = $("#ajax_filter_form").serialize();
        // const sort = get_sort(); &sort="+sort.sort+"&sort_type="+sort.sort_type+
        toggle_load_filter_list("on");
        
        // $('.cssload-jumping').css('display','block');

        change_uri();
        $.get(
            "/catalog_v2/ajax/filter_list?ajax=1&" + data,
            function (data) {
                // $('.cssload-jumping').css('display','none');

                const result = JSON.parse(data);
                
                ajaxFilterList(result.main);

                $("#products-list-pagination").html(result.pagination);

                elem.prop('disabled', false);

                toggle_load_filter_list("off");
        });
    });

    $('.verticle-menu-block').hover(

        function(){

            $('#main').addClass('bg-menu');

        },function(){

            $('#main').removeClass('bg-menu');

    });

    // back to top

    $(window).scroll(function(){
        if ($(this).scrollTop() > 200) {
            $('#flytheme-totop').fadeIn();
        } else {
            $('#flytheme-totop').fadeOut();
        }
    });

    $(window).scroll(function(){
        if ($(this).scrollTop() > 280) {
            $('body').addClass('m-body');
        } else {
            $('body').removeClass('m-body');
        }
    });

    $('#flytheme-totop').click(function(){
        $("html, body").animate({ scrollTop: 0 }, 600);
        return false;
    });
    /* end back to top */

    $(".mScroll").click(function (event) {
        //отменяем стандартную обработку нажатия по ссылке
        event.preventDefault();

        //забираем идентификатор бока с атрибута href
        var id  = $(this).attr('href'),

            //узнаем высоту от начала страницы до блока на который ссылается якорь
            top = $(id).offset().top;

        //анимируем переход на расстояние - top за 1500 мс
        $('body,html').animate({scrollTop: top - 94}, 1500);
    });

    if($('.current-li-content').text()){

        $('#breadcrumbs-two').attr('style','width: calc(100% - 165px)');

    }


    jQuery(function($){
        $(document).mouseup(function (e){
            var div = $("#footerLine, .mycart, .header-menu-right .fa-shopping-basket"); // тут указываем ID элемента
            if (!div.is(e.target) // если клик был не по нашему блоку
                && div.has(e.target).length === 0) {
                $("#footerLine").hide();
            }
        });
    });

    compareNumber();

});

function a_page(page){
    paginator(page);
    onSubmitFilterList().then(() => {
        $("html, body").animate({ scrollTop: 0 }, 600);
    });
}

function paginator(act = 'get'){
    const page = $("#inp_page");
    let page_val = page.val();
    switch(act){
        default:
            page.val(act);
            // const className = 'pagination-widget__page_active';
            // $(".pagination-widget__page").removeClass(className);
            // $("#pagination-widget__page_"+act).addClass(className);
        break;
        case "get":
            return page_val;
        break;
        case "prev":
            return --page_val;
        break;
        case "next":
            return ++page_val;
        break;
    }
}

function change_uri(){
    const data = $("#ajax_filter_form").serialize();
    const new_uri = $("#main_uri").val() + '?' + data;
    history.pushState({}, "change_prop", new_uri);
}

$(function(){
    //загрузка основного списка
    /*$.ajax({
        url: "profile/specification_list",
        type: "POST",
        success:function(data){
            $("#result").html(data);
            setTimeout(function(){
                $("#result").fadeIn();
            }, 300);
        }
    });*/

    //восстановление спецификации
    $(document).on("click", ".restoreSpec", function(){
        localStorage.removeItem("basketwidjet");
        localStorage.removeItem("basketwidjet_ids");
        let id = $(this).attr("id").slice(12);
        //console.log(id);
        $.ajax({
            url: "profile/specification_list_detail",
            type: "POST",
            data: {id: id},
            success:function(data){
                localStorage.setItem("basketwidjet", data);
                let mas = [];
                let i = 0;
                let myData = JSON.parse(data);
                for(key in myData){
                    mas[i] = key;
                    i++;
                }
                localStorage.setItem("basketwidjet_ids", JSON.stringify(mas));
            }
        });
    });

    //удаление спецификации
    $(document).on("click", ".removeSpec", function(){
        let id = $(this).attr("id").slice(11);
        console.log(id);
        $.ajax({
            url: "profile/removeSpec",
            type: "POST",
            data: {id: id},
            success:function(data){
                $("#main_"+id).hide();
            }
        });
    });

    $(document).on("click", ".editSpec", function(){
        let id = $(this).data("id");
        swal("Наименование спецификации:", {
            content: "input",
            attributes: {
                placeholder: "Введите наименование спецификации",
            }
        }).then((value) => {
            if(value){
                Swal.fire({
                    title: "Успешно!",
                    icon: "success",
                });
                $.post("profile/update_name_spec", {id:id, name:value}, function(){
                    $.post("profile/specification_list", function(data){
                        $("#result").html(data);
                    })
                });
            }
        });
        $(".swal-content__input").val($(this).data("name"));
    });

    $(document).on("click", ".getSpec", function(){
        let id = $(this).data("id");
        $.post("profile/getJsonSpec", {id:id}, function(data){
            Cookies.set('cart_products', data);
            document.location.href = "/catalog_v2/specification";
            setTimeout(function(){
                Cookies.remove('cart_products');
            }, 2000)
        });
    });

});

$(document).on("click", "#checkoutBtn", function(){
    let type = $(this).data("type");
    switch(type){
        case "user":
            Swal.fire("Успешно!", "Заказ успешно оформлен", "success");
            $.post("/ajax.php?do=setNewSales", {cid: localStorage.getItem("basketwidjet")}, function(){

            });
            break;
        case "guest":
            Swal.fire("Ошибка!", "Вы являетесь не зарегистрированным пользователем", "error");
            break;
    }
});


//table table-striped cart-table
var check = true;
/*$("#footerLine, .top-minicart-icon").click(function () {
    if(check) {
        $("#footerLine").animate({height: "80%"}, {complete: function () {

                var cart_containr = $('#footerLine').height();
                var cart_table = $('#tCart').height();
                var cart_table_good = 0;
                if(cart_table<=(cart_containr-200))
                {
                    cart_table_good = cart_table;
                }
                else
                {
                    cart_table_good = cart_containr-200;
                }
                $(".myTable").height(cart_table_good);
            }
        }, "slow");
        setTimeout(function(){
            check = false;
        }, 100);
    }

});
$("#body_wrapper").click(function () {
    if(!check){
        $("#footerLine").animate({height: "55px"}, "slow");
        check = true;
    }
});*/


$(document).on("click", "#getSpecification", function () {
    console.log("~");
    $.cookie("cart_products", null);
    var basketwidjet = JSON.parse(localStorage.getItem("basketwidjet"));
    var res = {};

    for(key in basketwidjet){
        var itid = basketwidjet[key].id.substr(2);
        res[itid] = {};
        res[itid] = basketwidjet[key].num;
    }

    //$.cookie('name', 'value', { expires: 7 });
    $.cookie("cart_products", JSON.stringify(res), { expires: 7, path: '/' });
    $(location).attr('href', 'https://atri-energo.ru/catalog_v2/specification');

});


$("#saveSpecification").click(function(){
    let locData = localStorage.getItem("basketwidjet");
    $.ajax({
        type: "POST",
        url: "/administration/module/catalog_v2/saveSpecification",
        data: {data: locData},
        success: function(data){

            swal("Спецификация сохранена!", "Вы можете увидеть ваши спецификации посмотреть в личном кабинете.", "success", {
                buttons: {
                    spec: "Открыть спецификации",
                    close: "Закрыть",
                },
            }).then((value) => {
                switch (value) {
                    case "close":

                        break;
                    case "spec":
                        //console.log("~");
                        document.location.href = "https://atri-energo.ru/profile/specification";
                        break;
                }
            });
        }
    });
});



/*
$(document).on('click', '#checkbox_filter', function(e) {
    if ($(e.target).prop("checked")) {
        document.getElementById($(e.target).data('label')).className = 'smart-filter-checkbox smart-filter-checkbox-active';
        onSubmitFilter();
        onSubmitFilterList();
    } else {
        document.getElementById($(e.target).data('label')).className = 'smart-filter-checkbox';
        onSubmitFilter();
        onSubmitFilterList();
    }

    let divLoading = $("#divLoading");
    let width = $(divLoading).width();
    let height = $(divLoading).height();
    let position = $(divLoading).position();
    $("#myField").prop("disabled", true);

    let loading = $("#loading2");
    $(loading).css("width", width);
    $(loading).css("height", height);
    $(loading).css("left", position.left);
    $(loading).css("top", position.top);
    $(loading).css("position", "absolute");
    $(loading).css("display", "block");
    $(loading).css("background-color", "rgba(94, 94, 94, 0.3)");
});
*/

//загрузка detail

function userDetail(aThis, bId){

    let mas = bId.split("_");
    let iid = mas[1];
    let id = mas[2];
    //console.log(iid);
    //console.log(id);
    if($("#sr_"+iid).css("display") == "block"){
        $("#sr_"+iid).slideToggle();
    } else {
        $.ajax({
            url: "profile/specification_list_detail",
            type: "POST",
            data: {id: id},
            success:function(data){
                //console.log(data);
                let myData = JSON.parse(data);
                let str = '<ol>';
                for(key in myData){
                    str += "<li>"+myData[key]["name"]+"</li>";
                }

                str += "</ol>";
                $("#"+bId).html(str);
                $("#"+bId).slideToggle();
            }
        });
    }

}

function change_sort(){
    let elem = $(".order-elems.current").eq(0);
    let sort_text = elem.children().html();
    let sort = elem.data("sort");
    let sort_type = elem.data("sort_type");
    $("#sortCurrent").html(sort_text).data("sort", sort).data("sort_type", sort_type);
    $("#sort").val(sort);
    $("#sort_type").val(sort_type);
}

function change_current_sort(This){
    if(!$(This).hasClass("current")){
        $(".order-elems").removeClass("current");
        $(This).addClass("current");
        change_sort();
        paginator(1);
        onSubmitFilterList();
    }
}

function get_sort(){
    let sort = {};
    let elem = $("#sortCurrent");
    sort.sort = elem.data("sort");
    sort.sort_type = elem.data("sort_type");
    return sort;
}

function toggle_load_filter_list(act){
    switch(act){
        case "on":
            $("#ajax_filter_list_load").show();
        break;
        case "off":
            $("#ajax_filter_list_load").hide();
        break;
    }
}

function onSubmitFilterList() {
    return new Promise((resolve, reject) => {
        // const sort = get_sort(); sort="+sort.sort+"&sort_type="+sort.sort_type+"&"
        const form = $("#ajax_filter_form").serialize();
        toggle_load_filter_list("on");
        change_uri();
        $.get("/catalog_v2/ajax/filter_list?"+form, function(data){
            $("#main_ajax_filter_list").html(data);
            toggle_load_filter_list("off");
            resolve();
        });
    });
}

/* --- Фильтра каталога --- */

function ajaxFilterList(a){

    let yt = 0;
    $("#ajax_filter_list").append(a);

    //{%AJAX_LOAD%} catalog.cat.tpl <li class="{%AJAX_LOAD%}order-item post-{%ID%} view-cart-an">

    // $('.ajax-load').each(function(){

    //     $(this).delay(yt).fadeIn(200, function(){
    //         $(this).removeClass("ajax-load");
    //     });

    //     yt += 300;
    // });
}


(function ($) {
    "use strict";
    $(window).on(function () {
        $('.grid-view').on('click', function () {
            $('.list-view').removeClass('active');
            $('.grid-view').addClass('active');
            jQuery("ul.products-loop").fadeOut(300, function () {
                $(this).removeClass("list").fadeIn(300).addClass('grid');
            });
        });

        $('.list-view').on('click', function () {
            $('.grid-view').removeClass('active');
            $('.list-view').addClass('active');
            $("ul.products-loop").fadeOut(300, function () {
                jQuery(this).addClass("list").fadeIn(300).removeClass('grid');
            });
        });

    });
})(jQuery);

$(document).on('click', '.checkbox_filter', function(e) {
    paginator(1);
    if ($(e.target).prop("checked")) {
        document.getElementById($(e.target).data('label')).className = 'smart-filter-checkbox smart-filter-checkbox-active';
        onSubmitFilter();
        onSubmitFilterList();
    } else {
        document.getElementById($(e.target).data('label')).className = 'smart-filter-checkbox';
        onSubmitFilter();
        onSubmitFilterList();
    }

    let divLoading = $("#divLoading");
    let width = $(divLoading).width();
    let height = $(divLoading).height();
    let position = $(divLoading).position();
    $("#myField").prop("disabled", true);

    let loading = $("#loading2");
    $(loading).css("width", width);
    $(loading).css("height", height);
    $(loading).css("left", position.left-13);
    $(loading).css("top", position.top);
    $(loading).css("position", "absolute");
    $(loading).css("display", "block");
    $(loading).css("background-color", "rgba(255, 255, 255, 0.9)");
});

function onSubmitFilter(token)
{
    const data = $("#ajax_filter_form").serialize();
    change_uri();
    $.ajax({
        type: "GET",
        url: "/catalog_v2/ajax/filter?"+data,
        data,
        success: function(data) {
            $("#ajax_filter_attribute").html(data);
            // window.location.hash = $("#ajax_filter_form").serialize();
            setTimeout(function () {
                $("#myField").removeProp("disabled");
                $("#loading2").css("display", "none");
            }, 850);
        },
        error: function() {
            $("#ajax_filter_attribute").html(data);
            window.location.hash = $("#ajax_filter_form").serialize();
            setTimeout(function () {
                $("#myField").removeProp("disabled");
                $("#loading2").css("display", "none");
            }, 850);
        }
    });
}

/*
function onSubmitFilterList(token) {
    $.ajax({
        type: "POST",
        url: "/catalog_v2/ajax/filter_list",
        data: $("#ajax_filter_form").serialize(),
        success: function(data) {
            $("#ajax_filter_list").html(data);
        },
        error: function() {
            $("#ajax_filter_list").html(data);
        }
    });
}
*/

/* --- Конец фильтров каталога --- */

$(document).on('ready', function() {
    $(".carusel-center").slick({
        infinite: true,
        centerMode: true,
        slidesToShow: 5,
        slidesToScroll: 1,
        autoplay: true
    });
});

$(function(){


//Живой поиск
    let flag = true;
    $(".who").keyup(function() {
        // console.log('Search');
        if(this.value.length >= 3){
            if(flag){
                flag = false;
                // console.log('Ajax search');
                $.ajax({
                    type: 'post',
                    url: "/ajax.php?do=search", //Путь к обработчику
                    data: {'referal':this.value},
                    response: 'text',
                    success: function(data){
                        $(".overlay_search").show();
                        $(".search_result").html('<div class="dv-result">' + data + '</div>').slideDown(); //Выводим полученые данные в списке
                        flag = true;

                        if(!data){

                            $(".search_result").html('');

                        }

                    }
                });
            }
        }
    });
    $(".overlay_search").on("click", function(){
        $(".overlay_search").hide();
        $(".search_result").fadeOut();
    })

    //При выборе результата поиска, прячем список и заносим выбранный результат в input
    $(".search_result").on("click", "li", function(){
        //s_user = $(this).text();
        //$(".who").val(s_user).attr('disabled', 'disabled'); //деактивируем input, если нужно
        $(".search_result").fadeOut();
    })
});


/* --- Слайдер --- */

"use strict";

var $slides = undefined,
    interval = undefined,
    $selectors = undefined,
    $btns = undefined,
    currentIndex = undefined,
    nextIndex = undefined;

var cycle = function cycle(index) {
    var $currentSlide = undefined,
        $nextSlide = undefined,
        $currentSelector = undefined,
        $nextSelector = undefined;

    nextIndex = index !== undefined ? index : nextIndex;

    $currentSlide = $($slides.get(currentIndex));
    $currentSelector = $($selectors.get(currentIndex));

    $nextSlide = $($slides.get(nextIndex));
    $nextSelector = $($selectors.get(nextIndex));

    $currentSlide.removeClass("active").css("z-index", "0");

    $nextSlide.addClass("active").css("z-index", "1");

    $currentSelector.removeClass("current");
    $nextSelector.addClass("current");

    currentIndex = index !== undefined ? nextIndex : currentIndex < $slides.length - 1 ? currentIndex + 1 : 0;

    nextIndex = currentIndex + 1 < $slides.length ? currentIndex + 1 : 0;
};

$(function () {
    currentIndex = 0;
    nextIndex = 1;

    $slides = $(".slide");
    $selectors = $(".selector");
    $btns = $(".btn");

    $slides.first().addClass("active");
    $selectors.first().addClass("current");

    interval = window.setInterval(cycle, 10000);

    $selectors.on("click", function (e) {
        var target = $selectors.index(e.target);
        if (target !== currentIndex) {
            window.clearInterval(interval);
            cycle(target);
            interval = window.setInterval(cycle, 10000);
        }
    });

    $btns.on("click", function (e) {
        window.clearInterval(interval);
        if ($(e.target).hasClass("prev")) {
            var target = currentIndex > 0 ? currentIndex - 1 : $slides.length - 1;
            cycle(target);
        } else if ($(e.target).hasClass("next")) {
            cycle();
        }
        interval = window.setInterval(cycle, 10000);
    });
});


var sldBlockStatus = 1;

function func() {

    sldBlockStatus = 1;

}

function sldBlock(aThis, bId){

    if(sldBlockStatus) {

        if (!$(aThis).hasClass('active')) {

            sldBlockStatus = 0;

            $('.energiya-sld').removeClass('active');
            $(aThis).addClass('active');

            $('.con-data').fadeOut(300);
            $('.con-data[data-block-id=' + bId + ']').delay(300).fadeIn(300);

            setTimeout(func, 550);

            $('.energiya-sl-content').attr('data-block-radius', bId);


        }

    }

}


// Режимы вида каталога
function productViewType(aThis, viewType){

    $('.view-mode i').removeClass('active');
    $(aThis).addClass('active');

    if(viewType == 'grid'){

        $('.type-conclusion').removeClass('type-conclusion-list type-conclusion-table');

        $.cookie('type-conclusion-table', '', {path: '/' });
        $.cookie('type-conclusion-list', '', {path: '/' });

    }else if(viewType == 'list'){

        $('.type-conclusion').removeClass('type-conclusion-table', {path: '/' });
        $('.type-conclusion').addClass('type-conclusion-list', {path: '/' });

        $.cookie('type-conclusion-list', 'type-conclusion-list', { expires: 30, path: '/' });
        $.cookie('type-conclusion-table', '', {path: '/' });

    }else if(viewType == 'table'){

        $('.type-conclusion').removeClass('type-conclusion-list');
        $('.type-conclusion').addClass('type-conclusion-table');

        $.cookie('type-conclusion-table', 'type-conclusion-table', { expires: 30, path: '/' });
        $.cookie('type-conclusion-list', '', {path: '/' });

    }

}

function inputCheck(aThis){

    $(aThis).val($(aThis).val().replace(/[^0-9]/g, ''));

}

function orderMinus(){

    if(parseInt($('#kolTv').val()) > 1){

        $('#kolTv').val(parseInt($('#kolTv').val()) - 1);

    }

}

function orderPlus(){

    $('#kolTv').val(parseInt($('#kolTv').val()) + 1);

    return false;

}


function formVhod(){

    $('#exampleModal').arcticmodal();

}


function mapModal(){

    $('#mapModal').arcticmodal();

}


function cartBasket(){

    if($('#footerLine').css('display') == 'none'){

        $('#footerLine').slideDown(300);

    }else{

        $('#footerLine').slideUp(300);

    }

}

function loadMenu(){

    if($('.menu-list-nav').text() == ''){
		const menu_1 = $('#menu-primary-menu-1').clone().html();	
		$('.menu-list-nav').html('<nav id="main-nav"><ul>'+menu_1+'<li><a href="/feedback">Контакты</a></li>');
		let menu_2 = $('#menu-menu-vertical-category2-1').clone().html();
        menu_2 = menu_2.replace(/<[\/]*span(.*?)>/g, '');
		
		$('.menu-list-nav .menu-top-cat a').after('<ul>'+menu_2+'</ul>');

        $('.menu-list-nav ul, .menu-list-nav li, .menu-list-nav span, .menu-list-nav a').attr('class','');

        $('.menu-list-nav ul i').remove();
		$('.menu-list-nav').after('</ul></nav>');
		
		
		
    }
	 
}


function showFilter(){

    $('body').addClass('overflow-n');
    $('#divLoading').fadeIn();

}

function showClose(){

    $('body').removeClass('overflow-n');
    $('#divLoading').fadeOut();

}

function navMob(){


    if($('.menu-list').css('display') == 'none'){

        $('#nav-icon').addClass('open');

        loadMenu();

        if ($(window).scrollTop() < 280)  {

            $("html, body").animate({ scrollTop: 300 }, 500);

        }

        $('.menu-list').fadeIn(300);

        $('body').addClass('overflow-n');

    }else{

        $('#nav-icon').removeClass('open');

        $('.menu-list').fadeOut(300);

        $('body').removeClass('overflow-n');

    }

}

function filterTitle(aThis){

    if($(aThis).next('.filterConDiv').css('display') == 'none'){

        $(aThis).next().show(300);
        $(aThis).parent().addClass('block-filter-title-active');

    }else{

        $(aThis).next().hide(300);
        $(aThis).parent().removeClass('block-filter-title-active');

    }

}

function pOneBlock(aThis){


    $('.atri-proect-grid .active').removeClass('active');
    $(aThis).addClass('active');

    if($(aThis).attr('data-id') == '1'){

        $.cookie('reghim', null, {path: '/' });

    }else{

        $.cookie('reghim', 1, { expires: 7, path: '/' });

    }


}

function aProektRaschet(aThis){

    $(aThis).remove();
    $('.atri-proect-form').slideDown(400);

}

function aProektRaschetLeft(aThis){

    $('.atri-proect-grid img[data-id="1"]').click();
    $('#aProektRaschet').click();

}

function aProektRaschetRight(aThis){

    $('.atri-proect-grid img[data-id="2"]').click();
    $('#aProektRaschet').click();

}

function gbBotForma(aThis){

    $(aThis).remove();
    $('.gb-forma').slideDown(500);

}

function searchFormClick(aThis){
    $('#sidebar-top-header').slideToggle(200);
}

function serRazmestitZacaz() {
    $('.user-razmestit-zacaz-hover').slideToggle(200);
}

function pageUzelUcheta(){
    $('.page-uzel-ucheta-right-add-hover').slideToggle(200);
}

function addSistema(a){

    var h4 = '',
        pType = '',
        gvsSelect = '';

    if(a == '1'){

        h4 = 'Отопление';
        pType = 'otop';

    }else if(a == '2'){

        h4 = 'Вентиляция';
        pType = 'vent';

    }else if(a == '3'){

        h4 = 'ГВС';
        pType = 'gvs';
        gvsSelect = '<div><select>\n' +
                    '<option>Параллельная</option>\n' +
                    '<option>Смешанная</option>\n' +
                    '</select></div>';

    }


    var bText = '<div class="addSistemBlock ' + pType + '">';
        bText += '<i class="fa fa-times" onclick="deleteAddSistemBlock(this)" aria-hidden="true"></i>';
        bText += '<strong>' + h4 + '</strong>' + gvsSelect;
        bText += '<div class="addSistemBlockInput">Q = <input type="text"  name="' + pType + '" value=""> Гкалл/ч</div>';
        bText += '<div class="lineBlue"></div>';
        bText += '<div class="lineBlueBottom"></div>';
        bText += '<div class="lineRed"></div>';
        bText += '<div class="lineRedBottom"></div>';
        bText += '</div>';

    $('.page-uzel-ucheta-right-add-hover').css('display','none');

    $('.page-uzel-ucheta-right .addCont').prepend(bText);

    lineAddSistemBlock();

}

function lineAddSistemBlock(){

    var lenLine = $('.addSistemBlock').length;
	$('.placeholder_uzel').css('display','none');

    if(lenLine == 0){

        $('.bt-line-n, .addCont').addClass('nonAddCont');

    } else {

        $('.nonAddCont').removeClass('nonAddCont');

    }

    if(lenLine == 0){

        $('.bt-line-n2').addClass('nonAddCont');

    }else if(lenLine == 1){

        $('.bt-line-n2').removeClass('nonAddCont');

        if($('.addSistemBlock').hasClass('gvs')){

            $('.bt-line-n2').css('height','10px');

        }else{

            $('.bt-line-n2').css('height','51px');

        }

    }else{

        $('.bt-line-n2').addClass('nonAddCont');

    }

    //bt-line-n2


}


// Узел учета, отправка формы

function addSistemBlockPost(aThis) {

    var inputStatus = 0;

    $('.grid-page-uzel-ucheta .inputRed').removeClass('inputRed');

    var inputDataJSON = '{"data": [';


    $('.grid-page-uzel-ucheta input[type="text"]').each(function () {

        if ($(this).val() == '') {

            if(!$(this).hasClass("notGvs")){

                $(this).addClass('inputRed');
                inputStatus = 1;

            }

        }

    });

    if (inputStatus != 1) {

        var i = 0;

        $('.addSistemBlock, .addSistemBlock2').each(function (indx) {


            if($(this).find('input').val() != ''){

                if (i) {

                    inputDataJSON += ',';

                }


                inputDataJSON += '{"name": "' + $(this).find('input').attr('name') + '",';
                inputDataJSON += '"q": "' + $(this).find('input').val() + '"';

                if ($(this).find('input').attr('name') == 'gvs') {

                    inputDataJSON += ',"sel": "' + $(this).find('select option:selected').text() + '"';

                }

                inputDataJSON += '}';

                i++;


            }

            });



    }


    inputDataJSON += ']}';

    if (inputStatus != 1) {

        $(aThis).after('<img id="uzel-ucheta-load" class="gifLoader" src="usr/tpl/dev2/assest/images/1/827.gif" alt="">');
        $(aThis).remove();

        $.post(
            "/ajax.php?do=getMetering",
            {
                t1: $('.grid-page-uzel-ucheta input[name="t1"]').val(),
                t2: $('.grid-page-uzel-ucheta input[name="t2"]').val(),
                t3: $('.grid-page-uzel-ucheta input[name="t3"]').val(),
                t4: $('.grid-page-uzel-ucheta input[name="t4"]').val(),
                p1: $('.grid-page-uzel-ucheta input[name="p1"]').val(),
                p2: $('.grid-page-uzel-ucheta input[name="p2"]').val(),
                p3: $('.grid-page-uzel-ucheta input[name="p3"]').val(),
                p4: $('.grid-page-uzel-ucheta input[name="p4"]').val(),
                tuNumber: $('input[name="tuNumber"]').val(),
                tuData: $('input[name="tuData"]').val(), 
				dogNumber: $('input[name="dogNumber"]').val(),
                dogData: $('input[name="dogData"]').val(),
                number: $('input[name="number"]').val(),
                raz: $('input[name="raz"]').val(),
                prov: $('input[name="prov"]').val(),
                control: $('input[name="control"]').val(),
                gip: $('input[name="gip"]').val(),
                preobTeplo: $('select[name="preobTeplo"] option:selected').val(),
                preobDav: $('select[name="preobDav"] option:selected').val(),
                shema: $('.grid-vkladka-uzel-ucheta .active').attr('data-vkladka-id'),
                inputDataJSON: inputDataJSON,
                org: $('input[name="org"]').val(),
                inn: $('input[name="inn"]').val(),
                name: $('input[name="name"]').val(),
                email: $('input[name="email"]').val(),
                phone: $('input[name="phone"]').val(),
                object: $('input[name="object"]').val(),
				oid: $('select[name="oid"] option:selected').val(),
				object_id: $('select[name="object_id"] option:selected').val(),
				region: $('select[name="region"] option:selected').val(),
				city: $('select[name="city"] option:selected').val(),
				tso: $('select[name="tso"] option:selected').val(),
            },
            function onAjaxSuccess(data) {


                if(data != ''){

                    $('#uzel-ucheta-load').after('<div class="but_send_schet pb1" onclick="location.href = \'/profile/request/view/'+ data +'\';\n">Получить пояснительную записку</div>');
                    $('#uzel-ucheta-load').remove();

                }else{

                    $('#uzel-ucheta-load').after('<div class="messageError">Произошла ошибка!</div>');
                    $('#uzel-ucheta-load').remove();

                }


            }
        );
    }

}

function deleteAddSistemBlock(aThis){

    $(aThis).parent().remove();
    lineAddSistemBlock()

}

function vladkaUzelUcheta(aThis, a){

    $('.grid-vkladka-uzel-ucheta .active').removeClass('active');
    $(aThis).addClass('active');
    $('.ulListGvs').css('display','block');

    if(a == 1){

        $('.image-uzel-ucheta').attr('src','/usr/tpl/dev2/assest/images/1/8255.png');
        $('.addSistemBlock2').css('display','none');
        $('.addSistemBlock2BotLine, .addSistemBlock2BotLineH').css('display','none');
        $('.image-uzel-ucheta-hover').removeClass('image-uzel-ucheta-hover');
        $('.addSistemBlock2 input').val('');
        $('.page-uzel-ucheta-left-tt-dev5, .page-uzel-ucheta-left-tt-dev6, .page-uzel-ucheta-left-tt-dev7, .page-uzel-ucheta-left-tt-dev8').remove();

    }else if(a == 2){

        $('.image-uzel-ucheta').attr('src','/usr/tpl/dev2/assest/images/1/8255-2.png');
        $('.addSistemBlock2').css('display','none');
        $('.addSistemBlock2BotLine, .addSistemBlock2BotLineH').css('display','none');
        $('.image-uzel-ucheta-hover').removeClass('image-uzel-ucheta-hover');
        $('.addSistemBlock2 input').val('');
        $('.page-uzel-ucheta-left-tt-dev5, .page-uzel-ucheta-left-tt-dev6, .page-uzel-ucheta-left-tt-dev7, .page-uzel-ucheta-left-tt-dev8').remove();

    }else if(a == 3){

        $('.image-uzel-ucheta').attr('src','/usr/tpl/dev2/assest/images/1/8255-3.png');
        $('.addSistemBlock2').css('display','block');
        $('.addSistemBlock2BotLine, .addSistemBlock2BotLineH, .ulListGvs').css('display','none');
        $('.image-uzel-ucheta').addClass('image-uzel-ucheta-hover');
        $('.addCont .gvs').remove();
        lineAddSistemBlock();
        $('.page-uzel-ucheta-left-tt-dev5, .page-uzel-ucheta-left-tt-dev6, .page-uzel-ucheta-left-tt-dev7, .page-uzel-ucheta-left-tt-dev8').remove();
        $('.page-uzel-ucheta-left-tt').append('<div class="page-uzel-ucheta-left-tt-dev5">T3 = <input type="text" name="t3" value=""/> °С</div>\n' +
            '                                    <div class="page-uzel-ucheta-left-tt-dev7">P3 = <input type="text" name="p3" value=""/> МПа</div>');

    }
    else if(a == 4){

        $('.image-uzel-ucheta').attr('src','/usr/tpl/dev2/assest/images/1/8255-4.png');
        $('.addSistemBlock2, .addSistemBlock2BotLine, .addSistemBlock2BotLineH').css('display','block');
        $('.ulListGvs').css('display','none');
        $('.image-uzel-ucheta-hover').removeClass('image-uzel-ucheta-hover');
        $('.addCont .gvs').remove();
        lineAddSistemBlock();
        $('.page-uzel-ucheta-left-tt-dev5, .page-uzel-ucheta-left-tt-dev6, .page-uzel-ucheta-left-tt-dev7, .page-uzel-ucheta-left-tt-dev8').remove();
        $('.page-uzel-ucheta-left-tt').append('<div class="page-uzel-ucheta-left-tt-dev5">T3 = <input type="text"  name="t3" value=""/> °С</div>\n' +
            '                                    <div class="page-uzel-ucheta-left-tt-dev6">T4 = <input type="text"  name="t4" value=""/> °С</div>\n' +
            '                                    <div class="page-uzel-ucheta-left-tt-dev7">P3 = <input type="text" name="p3" value=""/> МПа</div>\n' +
            '                                    <div class="page-uzel-ucheta-left-tt-dev8">P4 = <input type="text"  name="p4" value=""/> МПа</div>');

    }




}

function sliderLinkClick(){

    location.href = $('#slide-select .current').attr('data-url');

}


function kolVoNasosStanRander(aThis) {

    $('.nasos-div-img2, .nasos-div-img3').remove();


    var sel1 = parseInt($('.nasos-div-img-grid-left select[name=sum] option:selected').text()),
        sel2 = parseInt($('.nasos-div-img-grid-left select[name=sumr] option:selected').text());


        for (var i = 0; i < sel1 - sel2 - 2; i++) {

            $('.nasos-div-img-dv').append('<div class="nasos-div-img2"></div>');

        }

        if(sel1 > 2){

            for (var i = 0; i < sel2; i++) {

                $('.nasos-div-img-dv').append('<div class="nasos-div-img3"></div>');

                if(sel1 == 3){

                    break;

                }


            }

        }



    //<div class="nasos-div-img2"></div>


}


function kolVoNasosStan(aThis) {

    //$(aThis).val($(aThis).val().replace(/[^0-9]/g, ''));

    if($(aThis).val() != ''){

        kolVoNasosStanRander(aThis);

    }

}


function compare(aThis, bId){

    if($(aThis).hasClass('active')){

        $(aThis).removeClass('active');

    }else{

        $(aThis).addClass('active');

    }


    if($.cookie('compare') != null){

        var strCompare = '',
            strArrCompare = $.cookie('compare').split(',');


        //alert(bId);
        if(!$(aThis).hasClass('active')){


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

        }else{


            var k = 0;

            for(var i = 0; i < strArrCompare.length; i++){

                if(strArrCompare[i] != '') {

                    if (k != 0) {
                        strCompare += ',';
                    }


                    strCompare += strArrCompare[i];

                    k++;

                }

            }


            strCompare += ',';

            strCompare += bId;



            if(strCompare == ''){strArrCompare = null;}

            $.cookie('compare', strCompare, { expires: 100, path: '/' });

        }


    }else{

        $.cookie('compare', bId, { expires: 100, path: '/' });

    }

    compareNumber();

}


function compareNumber() {

    if ($.cookie('compare')) {

        var compareNumber = $.cookie('compare').split(',').length;

    }else{

        var compareNumber = 0;

    }

    $('.compare-number').text(compareNumber);

    if(compareNumber){

        $('.compare-act').addClass('active-cart');

    }else{

        $('.compare-act').removeClass('active-cart');

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

$(function () {
  $('[data-toggle="tooltip"]').tooltip()
})

if (typeof window.stackBarBottom === 'undefined') {
    window.stackBarBottom = {
      'dir1': 'up',
      'firstpos1': 0,
      'spacing1': 0
    };
  }
  PNotify.defaults.styling = 'bootstrap4';
