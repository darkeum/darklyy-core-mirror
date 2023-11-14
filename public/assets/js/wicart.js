var local = {
    "basket_is_empty": "Спецификация пуста",
    "name": "Название",
    "price": "Цена",
    "all": "Всего",
    "order": "Оформить заказ",
    "basket": "Спецификация",
    "num": "кол-во",
    "send": "Спасибо за покупку!\nМы свяжемся с Вами в ближайшее время",
    "goods": "Товаров",
    "amount": "на сумму"
};

function WICard(obj, plugins) {
    this.widjetX = 0;
    this.widjetY = 0;
    this.widjetObj;
    this.widjetPos;
    this.cardID = "";
    this.DATA = {};
    this.IDS = [];
    this.objNAME = obj;
    this.CONFIG = {};
    this.IMG = "iVBORw0KGgoAAAANSUhEUgAAACAAAAAgCAMAAABEpIrGAAABpFBMVEUAAABEREBEREBEREBEREASEhEJCQgGBgYBAQEAAAAGBgUHBwYAAAAAAAADAwNEREBEREAJCQkICAcGBgYFBQUJCQgnJyVEREAICAgBAQEAAAAICAcAAAAAAAAAAAAJCQgAAAAGBgYBAQEQEA8NDQwHBwcBAQEMDAsSEhEJCQkBAQEBAQEBAQFAQDxEREBEREADAwIAAAABAQESEhEkJCIAAAAICAgAAAAQEA9EREAAAAATExIAAAAKCgkNDQwAAAAAAAABAQETExIHBwcDAwMDAwMTExIAAAAAAAABAQEAAAAAAAAAAAAAAAAAAAAAAAADAwIAAAAGBgUBAQEWFhUAAAAAAAAHBwYBAQEVFRMDAwMHBwcUFBMWFhUBAQETExIAAAAAAAADAwMMDAsAAAAAAAASEhEAAAAUFBMAAAAJCQkrKygDAwMAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAADAwMAAAAJCQgAAAAiIiABAQEAAAAAAAAAAAAAAAAAAAAAAAAAAAABAQFEREAFBQUBAQEAAAAGBgYICAgHBwcBAQEJW8x2AAAAhXRSTlMAAQcIAjJ0kZqZnp+LaygNDxKw/v6wIwPY1A+upf4nenGWvXCg/Pubc8eSmLkcHxpW+vlhBr6hl3EDUl9pop5Q+fhe+1FPXVT8mlxfXWD9+1dbnPRRRved9ldI+Fhd+mBaVlSYWJN1V2dqwwVTA8ORvJXAknRzKii2rCjc3BUTqagrepgUbRZwswAAAAlwSFlzAAAASAAAAEgARslrPgAAAb1JREFUOMuNk2dTwkAQhpdiCZagotiwd5RYQeyKJdii2FDsvffeG0TJnza3lzg4kBnv0zt5NpvdZ3IA/zs6vUGv00wAxrj4hESGnERTkomk5JRU1swaFZ6WnhEMieSEgl9qsmRmWVWe/R2W8KkUjkw5uUr/vNhcEvPpLHEZGly0FYA8pb4wqMFFscioBzAU06lKSsvKKa+orKqmqYY1yB3smGvrHFx9A0mNTQ6uuQU7Oc1yB50Lc6uDc0ObXNHYTlIHfqmTJaZcmLvkpwDdPb19hAODkzjRlAuzpx93GhhEPsTjpF6gBTg1PyxnN4d8hHKJoQUhuhU/qvKxcWVTWmAPKlsLE5RP8qoJLND5vhQ/U9PIYWZWNcWgyTnFpH+ecoCFgNKTQZM+atK/qHI3txSg73gjTC6vIF9dw0nXhSiTG8g3eaEeJ92KMrlN+I4gSp5dkvaiTO4fAKwJ6PQQ4Og4wuQJ3fp070zZXzhJOafpAgsuQ2Gtf+4KC64tWtx2gybZW61/8g7QpPn+ITZ/fAI0yVqfX2wx+r8+gxVNkl3f3j+cnU4v3j4vSa73NwDrJ5qkt+f3Jv5N6u3Vvt0/UGcpYbC85ecAAAAldEVYdGRhdGU6Y3JlYXRlADIwMTMtMDUtMThUMDY6MDM6MzEtMDU6MDALk1CfAAAAJXRFWHRkYXRlOm1vZGlmeQAyMDEzLTA1LTE4VDA2OjAzOjMxLTA1OjAwes7oIwAAAABJRU5ErkJggg==";


    this.init = function (widjetID, config) {
        this.CONFIG = config || {};
        try {
            this.DATA = JSON.parse(localStorage.getItem(widjetID));
            if ($.isEmptyObject(this.DATA)) {
                this.DATA = {};
            }
        } catch (e) {
            this.DATA = {};
        }
        try {
            this.IDS = JSON.parse(localStorage.getItem(widjetID + "_ids"));
            if ($.isEmptyObject(this.IDS)) {
                this.IDS = [];
            }
        } catch (e) {
            this.IDS = [];
        }

        this.cardID = widjetID;

        this.widjetObj = $("#" + widjetID);
        this.widjetObj2 = $("#basketwidjet2, .fa-receipt span");
        this.widjetObj3 = $("#bsum_numb");
        this.widjetObjSum = $("#basketwidjetSum");
        this.widjetObjSum2 = $("#basketwidjetSum2");

        if ($.isEmptyObject(this.DATA)) {
            this.widjetObj.html('0');
            this.widjetObj2.html('0');
            this.widjetObj3.html('0');
            this.widjetObjSum.html("0.00 ₽");
            this.widjetObjSum2.html("0.00 ₽");
        } else {
            this.reCalc();
            this.renderBasketTable();
        }

    }

    /***********************************************************************************************
     * example: onclick="cart.addToCart(this, '2', 'Name of comic 2', '25500')
     **********************************************************************************************/
    this.addToCart = function (curObj, id, params) {

        // view-cart-an
        var that = $(curObj).parents('.view-cart-an').find('.wp-post-image');

        // fa
        var bascket;
        if ($(document).scrollTop() > 200) {
            bascket = $('#header-menu .fa-receipt');
        } else {
            bascket = $('.top-minicart-icon');
        }

        var w = that.width();

        that.clone()
            .css({
                'width': w,
                'position': 'absolute',
                'z-index': '9999999',
                top: that.offset().top,
                left: that.offset().left
            })
            .appendTo("body")
            .animate({
                opacity: 0.05,
                left: bascket.offset()['left'],
                top: bascket.offset()['top'] + 30,
                width: 20
            }, 1000, function () {
                $(this).remove();
            });


        var kol = parseInt($('#kolTv').val());

        if ($("input").is("#" + wiNumInputPrefID + id)) {
            kol = parseInt($("#" + wiNumInputPrefID + id).val());
        }

        id = ($.isNumeric(id)) ? "ID" + id.toString() : id;

        var id_ = ($.isEmptyObject(params.subid)) ? id : id + "_" + params.subid;

        let purl = params.url;
        if (purl == undefined) {
            purl = document.location.href;
        }
        let punit = "";
        if (params.unit == undefined) {
            punit = 'шт';
        } else {
            punit = params.unit.replace(/\./g, "");
        }
        punit += '.';

        var goodieLine = {
            "id": id_,
            "name": params.name,
            "price": params.price,
            "num": kol,
            "url": purl,
            "photo": params.photo,
            "unit": punit
        };

        if ($.isEmptyObject(this.DATA)) {
            this.DATA[id_] = goodieLine;
            this.IDS.push(id_);
        } else
            for (var idkey in this.DATA) {


                if ($.inArray(id_, this.IDS) === -1) {
                    this.DATA[id_] = goodieLine;
                    this.IDS.push(id_)

                } else if (idkey == id_) {

                    this.DATA[idkey].num += kol;

                }
            }

        localStorage.setItem(this.cardID, JSON.stringify(this.DATA));
        localStorage.setItem(this.cardID + "_ids", JSON.stringify(this.IDS));
        this.reCalc();

        this.renderBasketTable();

        if (this.CONFIG.showAfterAdd) {
            cart.showWinow('bcontainer', 1);
        }
    }
    this.reCalc = function () {
        var num, sum;
        num = sum = 0;
        for (var idkey in this.DATA) {
            num += parseInt(this.DATA[idkey].num);
            sum += parseFloat(parseInt(this.DATA[idkey].num) * parseFloat(this.DATA[idkey].price));
        }

        // *** currency plugin *** //

        if (typeof WICartConvert == 'function') {
            sum = WICartConvert(sum);
        }

        // *** //
        sum = sum.toFixed(2).replace(/(\d)(?=(\d{3})+([^\d]|$))/g, "$1 ");
        this.widjetObj.html(num);
        this.widjetObj2.html(num);
        $('#header-menu .fa-receipt').addClass('active-cart');
        this.widjetObj3.html(num);

        this.widjetObjSum.html(sum + " ₽");
        this.widjetObjSum2.html(sum + " ₽");
        localStorage.setItem(this.cardID, JSON.stringify(this.DATA));
    }
    this.clearBasket = function () {
        this.DATA = {};
        this.IDS = [];
        this.widjetObj.html("0");
        this.widjetObj2.html("0");
        this.widjetObjSum.html("0.00 ₽");
        this.widjetObjSum2.html("0.00 ₽");
        localStorage.setItem(this.cardID, "{}");
        localStorage.setItem(this.cardID + "_ids", "[]");
        $("#btable").html('');
        $("#bcontainer").remove();
        $("#blindLayer").remove();
    }
    this.renderBasketTable = function () {

        if ($('#bcontainer').length == 0) {
            $("body").append(
                "<div id='blindLayer' class='blindLayer'></div>" +
                "<div id='bcontainer' class='bcontainer'>" +
                    "<div id='bsubject'>"
                        + local.basket +
                        "<a id='bclose' href='#' onclick='" + this.objNAME + ".closeWindow(\"bcontainer\", 1);'>"
                            + "<img src='data:image/jpeg;base64," + this.IMG + "' alt='' />" +
                        "</a>" +
                    "</div>" +
                    "<table id='bcaption'>" +
                        "<tr>" +
                            "<td>ID</td>" +
                            "<td>" + local.name + "</td>" +
                            "<td>" + local.price + "</td>" +
                            "<td>" + local.num + "</td>" +
                            "<td>" + local.all + "</td>" +
                            "<td></td>" +
                        "</tr>" +
                    "</table>" +
                    "<div id='overflw'>" +
                        "<table class='btable' id='btable'></table>" +
                    "</div>" +
                    "<div id='bfooter'>" +
                        "<button class='bbutton' onclick=\"cart.showWinow('order', 1)\">"
                            + local.order +
                        "</button>" +
                        "<span id='bsum'>...</span>" +
                    "</div>" +
                "</div>");

        } else {
            $("#btable").html("");
        }
        this.center($("#bcontainer"))

        for (var idkey in this.DATA) {
            // console.log(this.DATA[idkey]);
            with (this.DATA[idkey]) {
                var productLine = '' +
                    '<div class="btable-div">' +
                        // '<div>'+ id +'</div>' +
                        '<div class="image">' +
                            '<img src="' + photo + '" alt="" />' +
                        '</div>' +
                        '<div class="title">' +
                            '<a href="' + url + '">' + name + '</a>' +
                        '</div>' +
                        '<div class="count text-center">' +
                            '<div class="cart-blk noselect">' +
                                '<div class="cart-minus" id="minus_' + id + '">-</div>' +
                                    '<input oninput="inputCheck(this)" type="text" size="1" value="' + num + '" class="basket_num" id="basket_num_' + id + '">' +
                                    '<div class="cart-plus" id="plus_' + id + '">+</div>' +
                                '</div>' +
                            '</div>' +
                        '<div class="weight">' +
                            '<span class="prod-weight">' + unit + '</span>' +
                        '</div>' +
                        '<div id="lineprice_' + id + '"class="wigoodprice">' +
                            '<span class="prace-ver">' + price.replace(/(\d)(?=(\d{3})+([^\d]|$))/g, "$1 ") + ' ₽</span>' +
                        '</div>' +
                        '<div class="prod-total-cost" id="linesum_' + id + '">' +
                            '<span class="prace-sum-ver">' + parseFloat(Math.round(price * num * 100.0) / 100.0).toFixed(2).replace(/(\d)(?=(\d{3})+([^\d]|$))/g, "$1 ") + ' ₽</span>' +
                        '</div>' +
                        '<div class="cart-ico">' +
                            '<span><i class="fa fa-heart-o" aria-hidden="true"></i></span>' +
                            '<span onclick="' + this.objNAME + '.delItem(\'' + id + '\')"><i class="fas fa-trash-alt"></i></span>' + '</div></div>';
            }

            $("#btable").append(productLine);
            // <span class="basket_num" id="basket_num_' + id + '">' + num + '</span>
            // <input type="text" size="1" value="'+num+'" class="basket_num" id="basket_num_' + id + '">
            $(".basket_num_buttons").data("min-value");
        }


        //* кнопки +/-
        var self = this;

        $("[id^=basket_num_]").change(function () {
            var cartItemID = $(this).attr("id").substr(11);
            var cartNum = parseInt($("#basket_num_" + cartItemID).val());
            var cartNum = (cartNum > 1) ? cartNum : 1;
            cartNum = (cartNum < 1000000) ? cartNum : 1000000;
            $("#basket_num_" + cartItemID).val(cartNum);
            // console.log(cartItemID);
            self.DATA[cartItemID].num = cartNum;
            var price = parseFloat($("#lineprice_" + cartItemID).text().replace(/\s/ig, ''));
            $("#linesum_" + cartItemID).html('<span class="prace-sum-ver">' + (price * cartNum).toFixed(2).replace(/(\d)(?=(\d{3})+([^\d]|$))/g, "$1 ") + ' ₽</span>');

            self.sumAll();

            self.reCalc();
        });
        for (var ids in this.IDS) {
            $('#minus_' + this.IDS[ids]).bind("click", function () {
                var cartItemID = $(this).attr("id").substr(6);
                //var cartNum = parseInt($("#basket_num_" + cartItemID).text());
                var cartNum = parseInt($("#basket_num_" + cartItemID).val());
                var cartNum = (cartNum > 1) ? cartNum - 1 : 1;
                //console.log(cartItemID);
                self.DATA[cartItemID].num = cartNum;

                $("#basket_num_" + cartItemID).val(cartNum);
                //$("#basket_num_" + cartItemID).html(cartNum);
                var price = parseFloat($("#lineprice_" + cartItemID).text().replace(/\s/ig, ''));
                //$("#linesum_" + cartItemID).html( parseFloat(price * cartNum) + ' ₽' );
                $("#linesum_" + cartItemID).html('<span class="prace-sum-ver">' + (price * cartNum).toFixed(2).replace(/(\d)(?=(\d{3})+([^\d]|$))/g, "$1 ") + ' ₽</span>');

                self.sumAll();

                self.reCalc();

            });

            $('#plus_' + this.IDS[ids]).bind("click", function () {
                var cartItemID = $(this).attr("id").substr(5);
                var cartNum = parseInt($("#basket_num_" + cartItemID).val());
                //var cartNum = parseInt($("#basket_num_" + cartItemID).text());
                var cartNum = (cartNum < 1000000) ? cartNum + 1 : 1000000;
                self.DATA[cartItemID].num = cartNum;
                $("#basket_num_" + cartItemID).val(cartNum);
                //$("#basket_num_" + cartItemID).html(cartNum);
                var price = parseFloat($("#lineprice_" + cartItemID).text().replace(/\s/ig, ''));
                $("#linesum_" + cartItemID).html(parseFloat(price * cartNum).toFixed(2).replace(/(\d)(?=(\d{3})+([^\d]|$))/g, "$1 ") + ' ₽');
                $("#linesum_" + cartItemID).html('<span class="prace-sum-ver">' + (price * cartNum).toFixed(2).replace(/(\d)(?=(\d{3})+([^\d]|$))/g, "$1 ") + ' ₽</span>');

                self.sumAll();
                self.reCalc();

            });

        }
        this.sumAll();

    }
    this.sumAll = function () {
        var sum = 0;
        for (var idkey in this.DATA) {
            sum += parseFloat(this.DATA[idkey].price * this.DATA[idkey].num);
        }
        $("#bsum").html(sum.toFixed(2).replace(/(\d)(?=(\d{3})+([^\d]|$))/g, "$1 ") + " ₽");
    }
    this.center = function (obj) {

        obj.css({"top": "200px"});
        obj.css({"left": Math.max(0, (($(window).width() - $(obj).outerWidth()) / 2) + $(window).scrollLeft()) + "px"});
        return obj;
    }
    this.showWinow = function (win, blind) {
        $("#" + win).show();
        if (blind)
            $("#blindLayer").show();
    }
    this.closeWindow = function (win, blind) {
        $("#" + win).hide();
        if (blind)
            $("#blindLayer").hide();
    }
    this.delItem = function (id) {
        if (confirm("Удалить #" + id + "?")) {
            $("#btable").html("");
            delete this.DATA[id];
            this.IDS.splice($.inArray(id, this.IDS), 1);
            this.reCalc();
            this.renderBasketTable();
            localStorage.setItem(this.cardID, JSON.stringify(this.DATA));
            localStorage.setItem(this.cardID + "_ids", JSON.stringify(this.IDS));
            if (this.IDS.length == 0)
                this.widjetObj.html('0');
            this.widjetObj2.html("0");
            this.widjetObjSum.html("0.00 ₽");
            this.widjetObjSum2.html("0.00 ₽");
        }
    }
    this.sendOrder = function (domElm) {
        var bodyHTML = "";
        var arr = domElm.split(",");

        for (var f = 0; f < arr.length; f++) {

            bodyHTML += this.getForm(arr[f]) + "<br><br>";

        }
        ;
        $('.basket_num_buttons').remove();
        $.post("sendmail.php?subj=Order WICart", {"order": bodyHTML}).done(function (data) {
            cart.closeWindow("bcontainer", 1)
            cart.closeWindow("order", 0);
            if (cart.CONFIG.clearAfterSend) {
                cart.clearBasket();
            }
            alert(local.send);
        });

    }
    this.getForm = function (formId) {
        var formObj = document.getElementById(formId);
        var copyForm = formObj.cloneNode(true);

        INPUTS = [].slice.call(copyForm.querySelectorAll("input,select,textarea"));

        INPUTS.forEach(function (elm) {
            if ((elm.tagName == 'INPUT') && (elm.type == 'checkbox')) {
                var spanReplace = document.createElement("span");
                spanReplace.innerHTML = (elm.checked) ? elm.value : "";
                elm.parentNode.replaceChild(spanReplace, elm);
            } else if ((elm.tagName == 'INPUT') && (elm.type == 'radio')) {
                var spanReplace = document.createElement("span");
                spanReplace.innerHTML = (elm.checked) ? elm.value : "";
                elm.parentNode.replaceChild(spanReplace, elm);
            } else if ((elm.tagName == 'INPUT') && ((elm.type == 'text') || (elm.type == 'hidden'))) {
                var subjP = document.createElement('b');
                subjP.innerHTML = elm.placeholder;
                elm.parentNode.insertBefore(subjP, elm);
                var spanReplace = document.createElement("div");
                spanReplace.innerHTML = elm.value;
                elm.parentNode.replaceChild(spanReplace, elm);
            } else if (elm.tagName == 'TEXTAREA') {
                var subjP = document.createElement('b');
                subjP.innerHTML = elm.placeholder;
                elm.parentNode.insertBefore(subjP, elm);
                var spanReplace = document.createElement("div");
                spanReplace.innerHTML = $("#" + elm.id).val();
                elm.parentNode.replaceChild(spanReplace, elm);
            }
            if (elm.tagName == 'SELECT') {
                var selVal = $("#" + elm.id + " option:selected").val();
                $(elm).replaceWith(selVal);
            }

        });

        return copyForm.innerHTML;
    }
}


