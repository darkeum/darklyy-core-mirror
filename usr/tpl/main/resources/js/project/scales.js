function recalc_standard(event) {
    var parent = $(event.target).parent().parent();
    parent.find('.diametr').html('');
    parent.find('.dlina').html('');
    parent.find('.material').html('');
    parent.find('.pokritie').html('');
    parent.find('.klassp').html('');
    parent.find('.idtov').html('');
    parent.find('.massa').html('');
    parent.find('.colvo').val('1');
    var stand = parent.find('.standard').val();
    var types = parent.find('.mtype').val();
    var s = '';
    $.getJSON('/project/tool/convert/scales?stand=' + stand + '&types=' + types, {}, function(jsona) {
        $.each(jsona, function(i, val) {
            s = s + '<option value="' + val + '">' + val + '</option>';
        });
        parent.find('.diametr').html(s);
        parent.find('.diametr').val(jsona[0]).change();
    });
};

function recalc_types(event) {
    var parent = $(event.target).parent().parent();
    parent.find('.diametr').html('');
    parent.find('.standard').html('');
    parent.find('.dlina').html('');
    parent.find('.material').html('');
    parent.find('.pokritie').html('');
    parent.find('.klassp').html('');
    parent.find('.idtov').html('');
    parent.find('.massa').html('');
    parent.find('.colvo').val('1');
    var stand = parent.find('.mtype').val()
    var s = '';
    $.getJSON('/project/tool/convert/scales?types=' + stand, {}, function(jsona) {
        $.each(jsona, function(i, val) {
            s = s + '<option value="' + val + '">' + val + '</option>';
        });
        parent.find('.standard').html(s);
        parent.find('.standard').val(jsona[0]).change();
    });
};

function recalc_dia(event) {
    var parent = $(event.target).parent().parent();
    parent.find('.dlina').html('');
    parent.find('.material').html('');
    parent.find('.pokritie').html('');
    parent.find('.klassp').html('');
    parent.find('.idtov').html('');
    parent.find('.massa').html('');
    parent.find('.colvo').val('1');

    var stand = parent.find('.standard').val();
    var dia = parent.find('.diametr').val();
    var types = parent.find('.mtype').val();

    var s = '';
    $.getJSON('/project/tool/convert/scales?stand=' + stand + '&dia=' + dia + '&types=' + types, {}, function(jsona) {
        $.each(jsona, function(i, val) {
            s = s + '<option value="' + val + '">' + val + '</option>';
        });
        parent.find('.dlina').html(s);
        parent.find('.dlina').val(jsona[0]).change();
    });
};

function recalc_dlina(event) {
    var parent = $(event.target).parent().parent();
    parent.find('.material').html('');
    parent.find('.pokritie').html('');
    parent.find('.klassp').html('');
    parent.find('.idtov').html('');
    parent.find('.massa').html('');
    parent.find('.colvo').val('1');

    var stand = parent.find('.standard').val()
    var dia = parent.find('.diametr').val()
    var dlin = parent.find('.dlina').val()
    var types = parent.find('.mtype').val();

    var s = '';
    $.getJSON('/project/tool/convert/scales?stand=' + stand + '&dia=' + dia + '&dlina=' + dlin + '&types=' + types, {}, function(jsona) { // загрузку JSON данных из файла example.json   
        $.each(jsona, function(i, val) {
            s = s + '<option value="' + val + '">' + val + '</option>';
        });
        parent.find('.material').html(s);
        parent.find('.material').val(jsona[0]).change();
    });
};

function get_tov_info(event) {
    var parent = $(event.target).parent().parent();

    var stand = parent.find('.standard').val();
    var dia = parent.find('.diametr').val();
    var dlin = parent.find('.dlina').val();
    var mat = parent.find('.material').val();
    var pokr = parent.find('.pokritie').val();
    var pokrv = $('.pokritie option[value="' + pokr + '"]').html();
    var klassp = parent.find('.klassp').val();
    var klasspv = $('.klassp option[value="' + klassp + '"]').html();

    var types = parent.find('.mtype').val();

    var s = types + ': ' + stand + ' ' + dia + 'x' + dlin + ' ' + mat + ' ' + pokrv + ' ' + klasspv;

    var idt = parent.find('.idtov').val();
    var infb = parent.find('.tovinfo');
    $.getJSON('/project/tool/convert/scales?tovid=' + idt, {}, function(jsona) {
        infb.html('<span>' + s + '</span><img src="' + jsona + '">');
    });
}




function recalc_material(event) {

    var parent = $(event.target).parent().parent();

    parent.find('.pokritie').html('');
    parent.find('.klassp').html('');
    parent.find('.idtov').html('');
    parent.find('.massa').html('');
    parent.find('.colvo').val('1');

    var stand = parent.find('.standard').val()
    var dia = parent.find('.diametr').val()
    var dlin = parent.find('.dlina').val()
    var mat = parent.find('.material').val()
    var types = parent.find('.mtype').val();

    var sp = '';
    var kp = ''
    $.getJSON('/project/tool/convert/scales?stand=' + stand + '&dia=' + dia + '&dlina=' + dlin + '&material=' + mat + '&types=' + types, {}, function(jsona) { // загрузку JSON данных из файла example.json   
        $.each(jsona, function(i, val) {
            if (i == 'klp') {
                $.each(val, function(ik, valk) {
                    kp = kp + '<option value="' + ik + '">' + valk + '</option>';
                });
            }
            if (i == 'pokr') {
                $.each(val, function(ip, valp) {
                    sp = sp + '<option value="' + ip + '">' + valp + '</option>';
                });
            }
            if (i == 'massa') {
                parent.find('.massa').val(val);
            }
            if (i == 'id') {
                parent.find('.idtov').val(val);
            }
        });
        parent.find('.pokritie').html(sp);
        parent.find('.klassp').html(kp);

        var m1 = parent.find('.massa').val();
        var colvo = parent.find('.colvo').val();
        var allm = (m1 * colvo);

        parent.find('.allmass').val(allm.toFixed(4));
        get_tov_info(event);
        calc_all_inf();
    });
};

function calc_all_inf() {
    var fullcnt = 0;
    $('.colvo').each(function(index, data) {
        fullcnt = fullcnt + Number($(data).val());
    });
    $('.allcntt').html(fullcnt);

    var fullmass = 0;
    $('.allmass').each(function(index, data) {
        fullmass = fullmass + Number($(data).val());
    });
    $('.allmasst').html(fullmass.toFixed(4));
}

function recalc_massa(event) {
    var parent = $(event.target).parent().parent() /*.parent().parent().parent()*/ ;
    var m1 = parent.find('.massa').val();
    var colvo = parent.find('.colvo').val();
    var allm = (m1 /*/1000*/ * colvo);
    parent.find('.allmass').val(allm.toFixed(4));
    calc_all_inf();
}

function recalc_count(event) {
    var parent = $(event.target).parent().parent() /*.parent().parent().parent()*/ ;

    var m1 = parent.find('.massa').val();
    var allm = parent.find('.allmass').val();

    var colvo = (allm / (m1 /*/1000/*/ ));
    parent.find('.colvo').val(colvo.toFixed(0));
    calc_all_inf();
}



function remove_tovar(event) {
    var tovcnt = 0;
    $('.tovar').each(function(index, element) {
        tovcnt++;
    });
    var i = 0;
    let arr = [];
    $('.tovar').each(function(index, element) {
        arr[i] = element;
        i++;
    });

    var curelem = $(event.target).parent().parent();

    for (k = 0; k < i; k++) {
        if (arr[k] == curelem[0]) {
            if (k > 0) {
                $(arr[k - 1]).find('.butt').show();
            }
        }
    }
    if (tovcnt > 1) {
        var parent = $(event.target).parent().parent();
        parent.remove();
        calc_all_inf();
    };
}

function copytext(el) {
    var $tmp = $("<textarea>");
    $("body").append($tmp);
    $tmp.val($(el).val()).select();
    document.execCommand("copy");
    $tmp.remove();
}

function copy_link(event) {
    var ind = 0;
    var mas = [];
    var s = '';
    $('.tovar').each(function(index, element) {
        mas[ind] = {};
        mas[ind]['id'] = $(element).find('.idtov').val();
        mas[ind]['count'] = $(element).find('.colvo').val();
        // Индексы покрытие/прочность
        mas[ind]['pokr'] = $(element).find('.pokritie').val();
        //    var pokrv=$('.pokritie option[value="' + pokr + '"]').html();
        mas[ind]['klassp'] = $(element).find('.klassp').val();
        //    var klasspv=$('.klassp option[value="' + klassp + '"]').html();


        ind++;
        s = s + '&id' + ind + '=' + $(element).find('.idtov').val() + '-' + $(element).find('.pokritie').val() + '-' + $(element).find('.klassp').val() + '&cnt' + ind + '=' + $(element).find('.colvo').val();
    });
    s = '/Calculators-and-services/calcv2/?cnt=' + ind + s;
    document.getElementById("myInput").value = s;
    copytext("#myInput");
    alert("Ссылка скопирована в буфер обмена");
}

function get_tov_types() {
    var rs = '';
    $.getJSON('/project/tool/convert/scales?gettypes=1', {}, function(jsona) {
        rs = jsona;
    });
    return rs;
}

function add_tovar(event) {
    var parent = $(event.target).parent().parent();
    var s = '<div class="tovar">';
    s = s + '<label>Тип</br><select class="mtype" name="typem"></select></label>';
    s = s + '<label>Стандарт</br><select class="standard" ></select></label>';
    s = s + '<label>Диаметр</br><select class="diametr" name="diametr"></select></label>';
    s = s + '<label>Длина</br><select class="dlina" name="dlina"></select></label>';
    s = s + '<label>Материал</br><select class="material" name="material"></select></label>';
    s = s + '<label>Покрытие</br><select class="pokritie" name="pokritie"></select></label>';
    s = s + '<label>Прочность</br><select class="klassp" name="klassp"></select></label>';
    s = s + '<label><input type="text"  class="idtov"></td><td><input type="text" class="massa"></label>';
    s = s + '<label>Кол-во, шт.</br><input type="text" class="colvo" value="1"></label>';
    s = s + '<label>Вес, кг.</br><input type="text" class="allmass"></label>';
    s = s + '<p class="tovinfo"></p>';
    s = s + '<div class="bta"><img class="butrem" src="/usr/tpl/main/resources/images/project/scales/icon-close.svg" alt="Удалить"><img class="butt" src="/usr/tpl/main/resources/images/project/scales/icon-plus.svg" alt="Добавить"></div></div>';
    parent.after(s);
    var next = parent.next(".tovar");
    var nextsel = next.find(".mtype");
    next.find('.butt').on("click", function(event) {
        add_tovar(event);
    });
    next.find('.butrem').on("click", function(event) {
        remove_tovar(event);
    });
    next.find('.material').on("change", function(event) {
        recalc_material(event);
    });
    next.find('.standard').on("change input", function(event) {
        recalc_standard(event);
    });
    next.find('.mtype').on("change", function(event) {
        recalc_types(event);
    });
    next.find('.diametr').on("change", function(event) {
        recalc_dia(event);
    });
    next.find('.dlina').on("change", function(event) {
        recalc_dlina(event);
    });
    next.find('.colvo').on("change paste keyup keydown", function(event) {
        recalc_massa(event);
    });
    next.find('.allmass').on("change paste keyup keydown", function(event) {
        recalc_count(event);
    });
    var list = next.find(".mtype");
    list.html('');
    var st = '';
    $.getJSON('/project/tool/convert/scales?gettypes=1', {}, function(jsonadd) { 
        $.each(jsonadd, function(i, val) {
            st = st + '<option>' + val + '</option>';
        });
        list.html(st);
        $(list).trigger('change');
    });
}


function all_reset(event) {
    $('.tovar').each(function(index, element) {
        if (index > 0) {
            element.remove();
        };
    });
}

function all_download(event) {
    var ind = 0;
    var mas = [];
    var s = '';
    $('.tovar').each(function(index, element) {
        mas[ind] = {};
        mas[ind]['id'] = $(element).find('.idtov').val();
        mas[ind]['count'] = $(element).find('.colvo').val();
        mas[ind]['pokr'] = $(element).find('.pokritie').val();
        mas[ind]['klassp'] = $(element).find('.klassp').val();


        ind++;
        s = s + '&id' + ind + '=' + $(element).find('.idtov').val() + '-' + $(element).find('.pokritie').val() + '-' + $(element).find('.klassp').val() + '&cnt' + ind + '=' + $(element).find('.colvo').val();
    });
    var link = document.createElement('a');
    link.setAttribute('href', '/project/tool/convert/scales_download?cnt=' + ind + s);
    link.setAttribute('download', 'download');
    link.click();
}


function printHTML(html) {
    var frame = document.createElement('iframe');
    frame.style.cssText = 'border:none;position:fixed;left:100%;';
    frame.onload = function() {
        var cssText = 'body{display:none} @media print{@page {size: portrait} body{display:block}}';
        cssText += '.headbox{width: 100%;margin: 0 auto;display: flex; justify-content: space-between;}.headbox .text{width: 50%;font-weight: bold;}';
        cssText += '.headbox .text span{color: #ff0101;}.headbox .logo{width: 50%;}ul{list-style: none;}table{width: 80%;margin: 15px auto;border-collapse: collapse;}';
        cssText += 'thead{font-weight: bold;text-align: center;background: lightgray;}tr{height: 40px;}td,th{border: 1px solid black;padding: 5px 10px 5px 10px;width: 10%;}';
        cssText += 'td:first-child, td:nth-child(4),td:nth-child(5),td:nth-child(8),td:nth-child(9),td:last-child{width: 3%;text-align: center;}';
        var style = this.contentDocument.createElement('style');
        if (style.readyState == 'loading') {
            style.onreadystatechange = function() {
                alert(this.readyState);
                if (this.readyState = 'complete') {
                    this.sheet.cssText = cssText;
                }
            }
        } else {
            style.textContent = cssText;
        }
        this.contentDocument.getElementsByTagName('head')[0].appendChild(style);
        this.contentDocument.body.innerHTML = html;
        setTimeout(function() {
        }, 0);

        setTimeout(function() {
            frame.contentWindow.print();;
            frame.parentNode.removeChild(frame);
        }, 1000);
    }
    document.body.appendChild(frame);
}

function toDataURL(url, callback) {
    var xhr = new XMLHttpRequest();
    xhr.onload = function() {
        var reader = new FileReader();
        reader.onloadend = function() {
            callback(reader.result);
        }
        reader.readAsDataURL(xhr.response);
    };
    xhr.open('GET', url);
    xhr.responseType = 'blob';
    xhr.send();
}

function print_table(event) {
    var fullw = 0;
    var fullcnt = 0;

    var imgs = document.getElementById("printimg").innerHTML;

    var html = '<div class="headbox"><div class="logo"><img src="' + imgs + '" width="90%"/></div>';
    html = html + "<table><thead><tr><td>№ п/п</td><td>Тип</td><td>Стандарт</td><td>Диаметр</td><td>Длина</td><td>Материал</td>";
    html = html + "<td>Покрытие</td><td>Прочность</td><td>Количество</td><th>Масса</td></tr></thead><tbody>";
    var num = 1;
    $('.tovar').each(function(index, element) {

        html = html + '<tr><td>' + num + '</td>';
        num++;

        html = html + '<td>' + $(element).find('.mtype').val() + '</td>';
        html = html + '<td>' + $(element).find('.standard').val() + '</td>';
        html = html + '<td>' + $(element).find('.diametr').val() + '</td>';
        html = html + '<td>' + $(element).find('.dlina').val() + '</td>';
        html = html + '<td>' + $(element).find('.material').val() + '</td>';


        var pokrv = $('.pokritie option[value="' + $(element).find('.pokritie').val() + '"]').html();
        var klasspv = $('.klassp option[value="' + $(element).find('.klassp').val() + '"]').html();



        html = html + '<td>' + $(element).find('.pokritie option[value="' + $(element).find('.pokritie').val() + '"]').html() + '</td>';
        html = html + '<td>' + $(element).find('.klassp option[value="' + $(element).find('.klassp').val() + '"]').html() + '</td>';

        html = html + '<td>' + $(element).find('.colvo').val() + '</td>';
        html = html + '<td>' + $(element).find('.allmass').val() + '</td></tr>';
        fullw += Number($(element).find('.allmass').val());
        fullcnt += Number($(element).find('.colvo').val());
    });

    html = html + '<tr><td colspan="7" class="total"></td><td><b>ИТОГО:</b></td><td>' + fullcnt + '<br/>шт.</td><td>' + fullw + '<br/>кг.</td></tr>';

    html = html + "</tbody></table>";
    printHTML(html);
}

function send_table(event) {
    //    alert('отправка почты');
    // Добавляем див попапа
    var s = '<div id="myModalc" class="modalc">';
    s = s + '  <div class="modal-contentc">';
    s = s + '    <span class="closec">&times;</span>';
    // Добавляем контент
    s = s + '<div class="popaptable"><table><tr><th>Тип</th><th>Стандарт</th><th>Диаметр</th><th>Длина</th><th>Материал</th>';
    s = s + '<th>Покрытие</th><th>Прочность</th><th>Количество</th><th>Масса</th></tr>';
    $('.tovar').each(function(index, element) {
        s = s + '<tr><td><span>Тип: </span>' + $(element).find('.mtype').val() + '</td><td><span>Стандарт: </span>' + $(element).find('.standard').val() + '</td>';
        s = s + '<td><span>Диаметр: </span>' + $(element).find('.diametr').val() + '</td>';
        s = s + '<td><span>Длина: </span>' + $(element).find('.dlina').val() + '</td>';
        s = s + '<td><span>Материал: </span>' + $(element).find('.material').val() + '</td>';


        var pokr = $(element).find('.pokritie').val();
        var pokrv = $(element).find('.pokritie option[value="' + pokr + '"]').html();
        var klassp = $(element).find('.klassp').val();
        var klasspv = $(element).find('.klassp option[value="' + klassp + '"]').html();


        s = s + '<td><span>Покрытие: </span>' + pokrv + '</td>';
        s = s + '<td><span>Прочность: </span>' + klasspv + '</td>';

        s = s + '<td><span>Количество: </span>' + $(element).find('.colvo').val() + '</td>';
        s = s + '<td><span>Масса: </span>' + $(element).find('.allmass').val() + '</td></tr>';
    });
    s = s + "</table></div>";
    s = s + '<div class="calcformdata">';
    s = s + '<p>Телефон: <input class="telc" type="text" size="40"></p>';
    s = s + '<p>E-mail: <input class="mailc" type="text" size="40"></p>';
    s = s + '<p><a class="btn btn-lg text-uppercase rounded-0 g-brd-4 u-btn-outline-darkred" href="" onclick="mainsend();return true;">Отправить</a></p>';
    s = s + '</div>';
    s = s + '  </div>';
    s = s + '</div>';
    var parent = $(event.target).parent();
    parent.after(s);
    // Get the modal
    var modal = document.getElementById("myModalc");
    // Get the <span> element that closes the modal
    var span = document.getElementsByClassName("closec")[0];
    // When the user clicks on <span> (x), close the modal
    span.onclick = function() {
            modal.style.display = "none";
        }
        // When the user clicks anywhere outside of the modal, close it
    window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }
        // When the user clicks on the button, open the modal
    modal.style.display = "block";
}


/* =============================================================================== */
function mainsend() {
    var tablec = $(".popaptable").html();
    var telc = $(".telc").val();
    var mailc = $(".mailc").val();

    var tablecenc = encodeURI(tablec);
    var telcenc = encodeURI(telc);
    var mailcenc = encodeURI(mailc);

    /* Генерируем урлы для xls */
    var ind = 0;
    var mas = [];
    var s = '';
    $('.tovar').each(function(index, element) {
        mas[ind] = {};
        mas[ind]['id'] = $(element).find('.idtov').val();
        mas[ind]['count'] = $(element).find('.colvo').val();
        mas[ind]['pokr'] = $(element).find('.pokritie').val();
        mas[ind]['klassp'] = $(element).find('.klassp').val();
        ind++;
        s = s + '&id' + ind + '=' + $(element).find('.idtov').val() + '-' + $(element).find('.pokritie').val() + '-' + $(element).find('.klassp').val() + '&cnt' + ind + '=' + $(element).find('.colvo').val();
    });
    //    var mjs = JSON.stringify(mas);
    s = '?cnt=' + ind + s;
    var mjsenc = encodeURI(s);

    /* ====================================== */
    var rs = '';
    var mtr = "/calcv2/calcsendmail.php";
    $.post(mtr, { a: tablecenc, b: telcenc, c: mailcenc, d: mjsenc }, function(data) {
        alert(data);
    });


}

//$(document).ready(function(){
$(window).on('load', function() {

    toDataURL('https://atri-energo.ru/usr/tpl/dev2/assest/images/logo3.png', function(dataUrl) {
        var d = document.createElement('div');
        d.className = "printimg";
        d.id = "printimg";
        d.style.display = 'none';
        d.innerHTML = dataUrl;
        document.body.append(d);
        //  console.log('RESULT:', dataUrl);
    });



    $('.printl').on("click", function(event) {
        print_table(event);
    });
    $('.sendl').on("click", function(event) {
        send_table(event);
    });
    $('.copyl').on("click", function(event) {
        copy_link(event);
    });
    $('.resall').on("click", function(event) {
        all_reset(event);
    });
    $('.downl').on("click", function(event) {
        all_download(event);
    });
    $('.colvo').on("change paste keyup keydown", function(event) {
        recalc_massa(event);
    });
    $('.allmass').on("change paste keyup keydown", function(event) {
        recalc_count(event);
    });
    $('.butt').on("click", function(event) {
        add_tovar(event);
    });
    $('.butrem').on("click", function(event) {
        remove_tovar(event);
    });
    /*
        $('.klassp').on("change",function(event){
    	recalc_klassp(event);
        });
        $('.pokritie').on("change",function(event){
    	recalc_pokritie(event);
        });
    */
    $('.material').on("change", function(event) {
        recalc_material(event);
    });
    $('.standard').on("change input", function(event) {
        recalc_standard(event);
    });
    $('.mtype').on("change", function(event) {
        recalc_types(event);
    });
    $('.diametr').on("change", function(event) {
        recalc_dia(event);
    });
    $('.dlina').on("change", function(event) {
        recalc_dlina(event);
    });
    //    $('.standard').html('');
    //    $('#id1').html('');

    var s = '';
    $.getJSON('/project/tool/convert/scales', {}, function(json) { // загрузку JSON данных из файла example.json   
        $.each(json, function(i, val) {
            s = s + '<option value="' + val + '">';
        });

        var atur = window.location.href;
        if (atur.indexOf('?cnt=') < 0) {
            $('.standard').html('');
            $('.mtype').trigger('change');
        }
        //    $('#id1').html(s);
        //    $('.standard').html(s);
        //    $('.standard').val(json[0]).change();
    });
});