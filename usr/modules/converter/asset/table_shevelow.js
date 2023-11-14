$(document).ready(function () {
    $("#CategoryTrees").change(function () {
        let c = this.value;
        for (let i = 1; i < 12; i++) {
            $("#blok" + i).css("display", c == i ? "block" : "none");
        }
    });
    function LResult(n) {
        $("#LBut" + n).click(function () {
            $("#hText" + n).html((($("#iText" + n).html() / 1000 * $("#L" + n).val())).toFixed(3));
        });
    }

    function ClearText(n) {
        $("#clear" + n).click(function () {
            $("#dp" + n).val("");
            $("#Q" + n).val("");
            $("#dText" + n).html("");
            $("#vText" + n).html("");
            $("#iText" + n).html("");
            $("#iText" + n).removeAttr("title");
        });
    }

    function SetEtc(n) {
        $("#etc" + n).click(function () {
            if ($("#etc" + n).prop("checked")) {
                $("#dp" + n).removeAttr("disabled");
                $(".disEtc" + n).attr("disabled", true);
            } else {
                $("#dp" + n).attr("disabled", true);
                $(".disEtc" + n).removeAttr("disabled");
            }
        });
    }

    function Losses(n) {
        $("#losses" + n).click(function () {
            if ($("#mainLos" + n).prop("hidden")) {
                $("#mainLos" + n).removeAttr("hidden");
            } else {
                $("#mainLos" + n).attr("hidden", true);
            }
        });
    }

    //<----------------steel_gas------------------->
    let depth1 = [1.8, 2, 2.2, 2.35, 2.5, 2.8, 3, 3.2, 3.5, 3.8, 4, 4.5, 5, 5.5];
    $("#dh1").change(function () {
        let CP = $("#depth1");
        CP.empty();
        switch (+$("#dh1").val()) {
            case 10.2:
                CP.append($('<option value="' + depth1[0] + '">' + depth1[0] + '</option>'));
                CP.append($('<option value="' + depth1[1] + '">' + depth1[1] + '</option>'));
                CP.append($('<option value="' + depth1[4] + '">' + depth1[4] + '</option>'));
                break;
            case 13.5:
            case 17.0:
                CP.append($('<option value="' + depth1[1] + '">' + depth1[1] + '</option>'));
                CP.append($('<option value="' + depth1[2] + '">' + depth1[2] + '</option>'));
                CP.append($('<option value="' + depth1[5] + '">' + depth1[5] + '</option>'));
                break;
            case 21.3:
            case 26.8:
                for (let i = 3; i < 6; i++) {
                    CP.append($('<option value="' + depth1[i] + '">' + depth1[i] + '</option>'));
                }
                CP.append($('<option value="' + depth1[7] + '">' + depth1[7] + '</option>'));
                break;
            case 33.5:
            case 42.3:
                CP.append($('<option value="' + depth1[5] + '">' + depth1[5] + '</option>'));
                CP.append($('<option value="' + depth1[7] + '">' + depth1[7] + '</option>'));
                CP.append($('<option value="' + depth1[10] + '">' + depth1[10] + '</option>'));
                break;
            case 48:
                CP.append($('<option value="' + depth1[6] + '">' + depth1[6] + '</option>'));
                CP.append($('<option value="' + depth1[8] + '">' + depth1[8] + '</option>'));
                CP.append($('<option value="' + depth1[10] + '">' + depth1[10] + '</option>'));
                break;
            case 60:
                CP.append($('<option value="' + depth1[6] + '">' + depth1[6] + '</option>'));
                CP.append($('<option value="' + depth1[8] + '">' + depth1[8] + '</option>'));
                CP.append($('<option value="' + depth1[11] + '">' + depth1[11] + '</option>'));
                break;
            case 75.5:
                CP.append($('<option value="' + depth1[7] + '">' + depth1[7] + '</option>'));
                CP.append($('<option value="' + depth1[10] + '">' + depth1[10] + '</option>'));
                CP.append($('<option value="' + depth1[11] + '">' + depth1[11] + '</option>'));
                break;
            case 88.5:
            case 101.3:
                CP.append($('<option value="' + depth1[8] + '">' + depth1[8] + '</option>'));
                CP.append($('<option value="' + depth1[10] + '">' + depth1[10] + '</option>'));
                CP.append($('<option value="' + depth1[11] + '">' + depth1[11] + '</option>'));
                break;
            case 114:
                for (let i = 10; i < 13; i++) {
                    CP.append($('<option value="' + depth1[i] + '">' + depth1[i] + '</option>'));
                }
                break;
            case 140:
            case 165:
                CP.append($('<option value="' + depth1[10] + '">' + depth1[10] + '</option>'));
                CP.append($('<option value="' + depth1[11] + '">' + depth1[11] + '</option>'));
                CP.append($('<option value="' + depth1[13] + '">' + depth1[13] + '</option>'));
                break;
        }
    });
    $(".temp1").click(function () {
        if ($("#temp11").prop("checked")) {
            $("#temp1s").attr("disabled", "true");
        } else {
            $("#temp1s").removeAttr("disabled");
        }
    });
    LResult(1);
    ClearText(1);
    SetEtc(1);
    Losses(1);
    //<----------------steel_gas------------------->
    //<------------------steel--------------------->
    let depth2 = [1, 1.2, 1.4, 1.6, 1.8, 2, 2.2, 2.5, 2.8, 3, 3.2, 3.5, 3.8, 4, 4.5, 5, 5.5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30];
    $("#dh2").change(function () {
        let CP = $("#depth2");
        CP.empty();
        switch (+$("#dh2").val()) {
            case 10:
            case 10.2:
                for (let i = 0; i < 2; i++) {
                    CP.append($('<option value="' + depth2[i] + '">' + depth2[i] + '</option>'));
                }
                break;
            case 12:
            case 13:
            case 14:
            case 16:
                for (let i = 0; i < 4; i++) {
                    CP.append($('<option value="' + depth2[i] + '">' + depth2[i] + '</option>'));
                }
                break;
            case 18:
                for (let i = 0; i < 5; i++) {
                    CP.append($('<option value="' + depth2[i] + '">' + depth2[i] + '</option>'));
                }
                break;
            case 19:
            case 20:
            case 21.3:
            case 22:
                for (let i = 0; i < 6; i++) {
                    CP.append($('<option value="' + depth2[i] + '">' + depth2[i] + '</option>'));
                }
                break;
            case 24:
            case 25:
            case 26:
            case 27:
            case 28:
            case 30:
                for (let i = 0; i < 8; i++) {
                    CP.append($('<option value="' + depth2[i] + '">' + depth2[i] + '</option>'));
                }
                break;
            case 32:
                for (let i = 0; i < 10; i++) {
                    CP.append($('<option value="' + depth2[i] + '">' + depth2[i] + '</option>'));
                }
                break;
            case 33.7:
            case 35:
            case 36:
            case 38:
            case 40:
            case 42:
            case 44.5:
            case 45:
                for (let i = 1; i < 10; i++) {
                    CP.append($('<option value="' + depth2[i] + '">' + depth2[i] + '</option>'));
                }
                break;
            case 48:
            case 48.3:
            case 51:
            case 53:
            case 54:
            case 57:
                for (let i = 2; i < 12; i++) {
                    CP.append($('<option value="' + depth2[i] + '">' + depth2[i] + '</option>'));
                }
                break;
            case 60:
            case 63.5:
                for (let i = 2; i < 13; i++) {
                    CP.append($('<option value="' + depth2[i] + '">' + depth2[i] + '</option>'));
                }
                break;
            case 70:
            case 73:
                for (let i = 2; i < 14; i++) {
                    CP.append($('<option value="' + depth2[i] + '">' + depth2[i] + '</option>'));
                }
                break;
            case 76:
                for (let i = 2; i < 17; i++) {
                    CP.append($('<option value="' + depth2[i] + '">' + depth2[i] + '</option>'));
                }
                break;
            case 88:
                for (let i = 3; i < 9; i++) {
                    CP.append($('<option value="' + depth2[i] + '">' + depth2[i] + '</option>'));
                }
                break;
            case 89:
                for (let i = 3; i < 17; i++) {
                    CP.append($('<option value="' + depth2[i] + '">' + depth2[i] + '</option>'));
                }
                break;
            case 95:
                for (let i = 5; i < 7; i++) {
                    CP.append($('<option value="' + depth2[i] + '">' + depth2[i] + '</option>'));
                }
                CP.append($('<option value="' + depth2[10] + '">' + depth2[10] + '</option>'));
                CP.append($('<option value="' + depth2[15] + '">' + depth2[15] + '</option>'));
                break;
            case 102:
            case 108:
            case 114:
            case 127:
            case 133:
            case 140:
            case 152:
                for (let i = 4; i < 17; i++) {
                    CP.append($('<option value="' + depth2[i] + '">' + depth2[i] + '</option>'));
                }
                break;
            case 159:
            case 168:
            case 177.8:
                for (let i = 4; i < 20; i++) {
                    CP.append($('<option value="' + depth2[i] + '">' + depth2[i] + '</option>'));
                }
                break;
            case 180:
                CP.append($('<option value="' + depth2[13] + '">' + depth2[13] + '</option>'));
                CP.append($('<option value="' + depth2[15] + '">' + depth2[15] + '</option>'));
                break;
            case 193.7:
                for (let i = 5; i < 20; i++) {
                    CP.append($('<option value="' + depth2[i] + '">' + depth2[i] + '</option>'));
                }
                break;
            case 219:
                for (let i = 7; i < 21; i++) {
                    CP.append($('<option value="' + depth2[i] + '">' + depth2[i] + '</option>'));
                }
                break;
            case 244.5:
                for (let i = 9; i < 21; i++) {
                    CP.append($('<option value="' + depth2[i] + '">' + depth2[i] + '</option>'));
                }
                break;
            case 273:
                for (let i = 11; i < 21; i++) {
                    CP.append($('<option value="' + depth2[i] + '">' + depth2[i] + '</option>'));
                }
                break;
            case 325:
                for (let i = 13; i < 21; i++) {
                    CP.append($('<option value="' + depth2[i] + '">' + depth2[i] + '</option>'));
                }
                break;
            case 355.6:
            case 377:
                for (let i = 13; i < 22; i++) {
                    CP.append($('<option value="' + depth2[i] + '">' + depth2[i] + '</option>'));
                }
                break;
            case 406.4:
            case 426:
                for (let i = 13; i < 24; i++) {
                    CP.append($('<option value="' + depth2[i] + '">' + depth2[i] + '</option>'));
                }
                break;
            case 530:
                for (let i = 15; i < 36; i++) {
                    CP.append($('<option value="' + depth2[i] + '">' + depth2[i] + '</option>'));
                }
                break;
            case 630:
                for (let i = 18; i < 32; i++) {
                    CP.append($('<option value="' + depth2[i] + '">' + depth2[i] + '</option>'));
                }
                break;
            case 720:
            case 820:
                for (let i = 18; i < 42; i++) {
                    CP.append($('<option value="' + depth2[i] + '">' + depth2[i] + '</option>'));
                }
                break;
            case 920:
                for (let i = 18; i < 32; i++) {
                    CP.append($('<option value="' + depth2[i] + '">' + depth2[i] + '</option>'));
                }
                break;
            case 1020:
                for (let i = 19; i < 42; i++) {
                    CP.append($('<option value="' + depth2[i] + '">' + depth2[i] + '</option>'));
                }
                break;
            case 1120:
                for (let i = 19; i < 32; i++) {
                    CP.append($('<option value="' + depth2[i] + '">' + depth2[i] + '</option>'));
                }
                break;
            case 1220:
                for (let i = 20; i < 32; i++) {
                    CP.append($('<option value="' + depth2[i] + '">' + depth2[i] + '</option>'));
                }
                break;
            case 1420:
                for (let i = 21; i < 32; i++) {
                    CP.append($('<option value="' + depth2[i] + '">' + depth2[i] + '</option>'));
                }
                break;
        }
    });
    $(".temp2").click(function () {
        if ($("#temp21").prop("checked")) {
            $("#temp2s").attr("disabled", "true");
        } else {
            $("#temp2s").removeAttr("disabled");
        }
    });
    LResult(2);
    ClearText(2);
    SetEtc(2);
    Losses(2);
    //<------------------steel--------------------->
    //<---------------cast_iron-------------------->
    $(".clas3").click(function () {
        let CP = $("#CP3");
        if ($("#clasLA3").prop("checked")) {
            CP.empty();
            CP.append($('<option value="50">50</option>'));
            CP.append($('<option value="80">80</option>'));
            CP.append($('<option value="100">100</option>'));
            CP.append($('<option value="125">125</option>'));
            CP.append($('<option value="150">150</option>'));
            CP.append($('<option value="200">200</option>'));
            CP.append($('<option value="250">250</option>'));
            CP.append($('<option value="300">300</option>'));
        }
        if ($("#clasA3").prop("checked")) {
            CP.empty();
            CP.append($('<option value="350">350</option>'));
            CP.append($('<option value="400">400</option>'));
            CP.append($('<option value="450">450</option>'));
            CP.append($('<option value="500">500</option>'));
            CP.append($('<option value="600">600</option>'));
            CP.append($('<option value="700">700</option>'));
            CP.append($('<option value="800">800</option>'));
            CP.append($('<option value="900">900</option>'));
            CP.append($('<option value=1000">1000</option>'));
            CP.append($('<option value="1200">1200</option>'));
        }
    });
    LResult(3);
    ClearText(3);
    SetEtc(3);
    Losses(3);
    //<---------------cast_iron-------------------->
    //<------------asbestos_cement----------------->
    $(".type4").click(function () {
        let CP = $("#CP4");
        if ($("#type14").prop("checked")) {
            CP.empty();
            CP.append($('<option value="100">100</option>'));
            CP.append($('<option value="150">150</option>'));
            CP.append($('<option value="200">200</option>'));
            CP.append($('<option value="250">250</option>'));
            CP.append($('<option value="300">300</option>'));
            CP.append($('<option value="350">350</option>'));
            CP.append($('<option value="400">400</option>'));
            CP.append($('<option value="500">500</option>'));
        }
        if ($("#type24").prop("checked")) {
            CP.empty();
            CP.append($('<option value="200">200</option>'));
            CP.append($('<option value="250">250</option>'));
            CP.append($('<option value="300">300</option>'));
            CP.append($('<option value="350">350</option>'));
            CP.append($('<option value="400">400</option>'));
            CP.append($('<option value="500">500</option>'));
        }
        if ($("#type34").prop("checked")) {
            CP.empty();
            CP.append($('<option value="200">200</option>'));
            CP.append($('<option value="300">300</option>'));
        }
        if ($("#type14").prop("checked")) {
            $("#VT154H").css("display", "none");
            $("#VT64").prop("checked", true);
        } else {
            $("#VT154H").css("display", "");
        }
    });
    LResult(4);
    ClearText(4);
    SetEtc(4);
    Losses(4);
    //<------------asbestos_cement----------------->
    //<---------------plastic---------------------->
    $("#MRS5").change(function () {
        let CP = $("#SDR5");
        CP.empty();
        switch (+$("#MRS5").val()) {
            case 1:
                CP.append($('<option value="1">SDR21 (S10)</option>'));
                CP.append($('<option value="2">SDR13.6 (S6.3)</option>'));
                CP.append($('<option value="3">SDR9 (S4)</option>'));
                CP.append($('<option value="4">SDR6 (S2.5)</option>'));
                CP.change();
                break;
            case 2:
                CP.append($('<option value="1">SDR41 (S20)</option>'));
                CP.append($('<option value="2">SDR26 (S12.5)</option>'));
                CP.append($('<option value="3">SDR17.6 (S8.3)</option>'));
                CP.append($('<option value="4">SDR11 (S5)</option>'));
                CP.change();
                break;
            case 3:
                CP.append($('<option value="1">SDR26 (S12.5)</option>'));
                CP.append($('<option value="2">SDR21 (S10)</option>'));
                CP.append($('<option value="3">SDR17.6 (S8.3)</option>'));
                CP.append($('<option value="4">SDR17 (S8)</option>'));
                CP.append($('<option value="5">SDR13.6 (S6.3)</option>'));
                CP.append($('<option value="6">SDR11 (S5)</option>'));
                CP.append($('<option value="7">SDR9 (S4)</option>'));
                CP.change();
                break;
            case 4:
                CP.append($('<option value="1">SDR17 (S8)</option>'));
                CP.append($('<option value="2">SDR13.6 (S6.3)</option>'));
                CP.append($('<option value="3">SDR11 (S5)</option>'));
                CP.change();
                break;
        }
    });
    $("#SDR5").change(function () {
        let CP = $("#CP5");
        CP.empty();
        switch (+$("#MRS5").val()) {
            case 1:
                switch (+$("#SDR5").val()) {
                    case 1:
                        CP.append($('<option value="32">32</option>'));
                        CP.append($('<option value="40">40</option>'));
                        CP.append($('<option value="50">50</option>'));
                        CP.append($('<option value="63">63</option>'));
                        CP.append($('<option value="75">75</option>'));
                        CP.append($('<option value="90">90</option>'));
                        CP.append($('<option value="110">110</option>'));
                        CP.append($('<option value="125">125</option>'));
                        CP.append($('<option value="140">140</option>'));
                        CP.append($('<option value="160">160</option>'));
                        break;
                    case 2:
                        CP.append($('<option value="25">25</option>'));
                        CP.append($('<option value="32">32</option>'));
                        CP.append($('<option value="40">40</option>'));
                        CP.append($('<option value="50">50</option>'));
                        CP.append($('<option value="63">63</option>'));
                        CP.append($('<option value="75">75</option>'));
                        CP.append($('<option value="90">90</option>'));
                        CP.append($('<option value="110">110</option>'));
                        CP.append($('<option value="125">125</option>'));
                        CP.append($('<option value="140">140</option>'));
                        CP.append($('<option value="160">160</option>'));
                        break;
                    case 3:
                        CP.append($('<option value="16">16</option>'));
                        CP.append($('<option value="20">20</option>'));
                        CP.append($('<option value="25">25</option>'));
                        CP.append($('<option value="32">32</option>'));
                        CP.append($('<option value="40">40</option>'));
                        CP.append($('<option value="50">50</option>'));
                        CP.append($('<option value="63">63</option>'));
                        CP.append($('<option value="75">75</option>'));
                        CP.append($('<option value="90">90</option>'));
                        CP.append($('<option value="110">110</option>'));
                        CP.append($('<option value="125">125</option>'));
                        break;
                    case 4:
                        CP.append($('<option value="10">10</option>'));
                        CP.append($('<option value="12">12</option>'));
                        CP.append($('<option value="16">16</option>'));
                        CP.append($('<option value="20">20</option>'));
                        CP.append($('<option value="25">25</option>'));
                        CP.append($('<option value="32">32</option>'));
                        CP.append($('<option value="40">40</option>'));
                        CP.append($('<option value="50">50</option>'));
                        CP.append($('<option value="63">63</option>'));
                        CP.append($('<option value="75">75</option>'));
                        CP.append($('<option value="90">90</option>'));
                        CP.append($('<option value="110">110</option>'));
                        CP.append($('<option value="125">125</option>'));
                        break;
                }
                break;
            case 2:
                switch (+$("#SDR5").val()) {
                    case 1:
                        CP.append($('<option value="63">63</option>'));
                        CP.append($('<option value="75">75</option>'));
                        CP.append($('<option value="90">90</option>'));
                        CP.append($('<option value="110">110</option>'));
                        CP.append($('<option value="125">125</option>'));
                        CP.append($('<option value="140">140</option>'));
                        CP.append($('<option value="160">160</option>'));
                        CP.append($('<option value="180">180</option>'));
                        CP.append($('<option value="200">200</option>'));
                        CP.append($('<option value="250">250</option>'));
                        CP.append($('<option value="280">280</option>'));
                        CP.append($('<option value="315">315</option>'));
                        CP.append($('<option value="355">355</option>'));
                        CP.append($('<option value="400">400</option>'));
                        CP.append($('<option value="450">450</option>'));
                        CP.append($('<option value="500">500</option>'));
                        CP.append($('<option value="560">560</option>'));
                        CP.append($('<option value="630">630</option>'));
                        CP.append($('<option value="710">710</option>'));
                        CP.append($('<option value="800">180</option>'));
                        CP.append($('<option value="900">900</option>'));
                        CP.append($('<option value="1000">1000</option>'));
                        CP.append($('<option value="1200">1200</option>'));
                        break;
                    case 2:
                        CP.append($('<option value="40">40</option>'));
                        CP.append($('<option value="63">63</option>'));
                        CP.append($('<option value="75">75</option>'));
                        CP.append($('<option value="90">90</option>'));
                        CP.append($('<option value="110">110</option>'));
                        CP.append($('<option value="125">125</option>'));
                        CP.append($('<option value="140">140</option>'));
                        CP.append($('<option value="160">160</option>'));
                        CP.append($('<option value="180">180</option>'));
                        CP.append($('<option value="200">200</option>'));
                        CP.append($('<option value="250">250</option>'));
                        CP.append($('<option value="280">280</option>'));
                        CP.append($('<option value="315">315</option>'));
                        CP.append($('<option value="355">355</option>'));
                        CP.append($('<option value="400">400</option>'));
                        CP.append($('<option value="450">450</option>'));
                        CP.append($('<option value="500">500</option>'));
                        CP.append($('<option value="560">560</option>'));
                        CP.append($('<option value="630">630</option>'));
                        CP.append($('<option value="710">710</option>'));
                        CP.append($('<option value="800">180</option>'));
                        CP.append($('<option value="900">900</option>'));
                        CP.append($('<option value="1000">1000</option>'));
                        CP.append($('<option value="1200">1200</option>'));
                        break;
                    case 3:
                        CP.append($('<option value="25">25</option>'));
                        CP.append($('<option value="32">32</option>'));
                        CP.append($('<option value="40">40</option>'));
                        CP.append($('<option value="50">50</option>'));
                        CP.append($('<option value="63">63</option>'));
                        CP.append($('<option value="75">75</option>'));
                        CP.append($('<option value="90">90</option>'));
                        CP.append($('<option value="110">110</option>'));
                        CP.append($('<option value="125">125</option>'));
                        CP.append($('<option value="140">140</option>'));
                        CP.append($('<option value="160">160</option>'));
                        CP.append($('<option value="180">180</option>'));
                        CP.append($('<option value="200">200</option>'));
                        CP.append($('<option value="250">250</option>'));
                        CP.append($('<option value="280">280</option>'));
                        CP.append($('<option value="315">315</option>'));
                        CP.append($('<option value="355">355</option>'));
                        CP.append($('<option value="400">400</option>'));
                        CP.append($('<option value="450">450</option>'));
                        CP.append($('<option value="500">500</option>'));
                        CP.append($('<option value="560">560</option>'));
                        CP.append($('<option value="630">630</option>'));
                        CP.append($('<option value="710">710</option>'));
                        CP.append($('<option value="800">180</option>'));
                        CP.append($('<option value="900">900</option>'));
                        CP.append($('<option value="1000">1000</option>'));
                        break;
                    case 4:
                        CP.append($('<option value="16">16</option>'));
                        CP.append($('<option value="20">20</option>'));
                        CP.append($('<option value="25">25</option>'));
                        CP.append($('<option value="32">32</option>'));
                        CP.append($('<option value="40">40</option>'));
                        CP.append($('<option value="50">50</option>'));
                        CP.append($('<option value="63">63</option>'));
                        CP.append($('<option value="75">75</option>'));
                        CP.append($('<option value="90">90</option>'));
                        CP.append($('<option value="110">110</option>'));
                        CP.append($('<option value="125">125</option>'));
                        CP.append($('<option value="140">140</option>'));
                        CP.append($('<option value="160">160</option>'));
                        CP.append($('<option value="180">180</option>'));
                        CP.append($('<option value="200">200</option>'));
                        CP.append($('<option value="250">250</option>'));
                        CP.append($('<option value="280">280</option>'));
                        CP.append($('<option value="315">315</option>'));
                        CP.append($('<option value="355">355</option>'));
                        CP.append($('<option value="400">400</option>'));
                        CP.append($('<option value="450">450</option>'));
                        CP.append($('<option value="500">500</option>'));
                        CP.append($('<option value="560">560</option>'));
                        CP.append($('<option value="630">630</option>'));
                        break;
                }
                break;
            case 3:
                switch (+$("#SDR5").val()) {
                    case 1:
                        CP.append($('<option value="50">50</option>'));
                        CP.append($('<option value="63">63</option>'));
                        CP.append($('<option value="75">75</option>'));
                        CP.append($('<option value="90">90</option>'));
                        CP.append($('<option value="110">110</option>'));
                        CP.append($('<option value="125">125</option>'));
                        CP.append($('<option value="140">140</option>'));
                        CP.append($('<option value="160">160</option>'));
                        CP.append($('<option value="180">180</option>'));
                        CP.append($('<option value="200">200</option>'));
                        CP.append($('<option value="250">250</option>'));
                        CP.append($('<option value="280">280</option>'));
                        CP.append($('<option value="315">315</option>'));
                        CP.append($('<option value="355">355</option>'));
                        CP.append($('<option value="400">400</option>'));
                        CP.append($('<option value="450">450</option>'));
                        CP.append($('<option value="500">500</option>'));
                        CP.append($('<option value="560">560</option>'));
                        CP.append($('<option value="630">630</option>'));
                        CP.append($('<option value="710">710</option>'));
                        CP.append($('<option value="800">180</option>'));
                        CP.append($('<option value="900">900</option>'));
                        CP.append($('<option value="1000">1000</option>'));
                        CP.append($('<option value="1200">1200</option>'));
                        break;
                    case 2:
                        CP.append($('<option value="40">40</option>'));
                        CP.append($('<option value="50">50</option>'));
                        CP.append($('<option value="63">63</option>'));
                        CP.append($('<option value="75">75</option>'));
                        CP.append($('<option value="90">90</option>'));
                        CP.append($('<option value="110">110</option>'));
                        CP.append($('<option value="125">125</option>'));
                        CP.append($('<option value="140">140</option>'));
                        CP.append($('<option value="160">160</option>'));
                        CP.append($('<option value="180">180</option>'));
                        CP.append($('<option value="200">200</option>'));
                        CP.append($('<option value="250">250</option>'));
                        CP.append($('<option value="280">280</option>'));
                        CP.append($('<option value="315">315</option>'));
                        CP.append($('<option value="355">355</option>'));
                        CP.append($('<option value="400">400</option>'));
                        CP.append($('<option value="450">450</option>'));
                        CP.append($('<option value="500">500</option>'));
                        CP.append($('<option value="560">560</option>'));
                        CP.append($('<option value="630">630</option>'));
                        CP.append($('<option value="710">710</option>'));
                        CP.append($('<option value="800">180</option>'));
                        CP.append($('<option value="900">900</option>'));
                        CP.append($('<option value="1000">1000</option>'));
                        CP.append($('<option value="1200">1200</option>'));
                        break;
                    case 3:
                        CP.append($('<option value="63">63</option>'));
                        CP.append($('<option value="75">75</option>'));
                        CP.append($('<option value="90">90</option>'));
                        CP.append($('<option value="110">110</option>'));
                        CP.append($('<option value="125">125</option>'));
                        CP.append($('<option value="140">140</option>'));
                        CP.append($('<option value="160">160</option>'));
                        CP.append($('<option value="180">180</option>'));
                        CP.append($('<option value="200">200</option>'));
                        CP.append($('<option value="250">250</option>'));
                        CP.append($('<option value="280">280</option>'));
                        CP.append($('<option value="315">315</option>'));
                        CP.append($('<option value="355">355</option>'));
                        CP.append($('<option value="400">400</option>'));
                        CP.append($('<option value="450">450</option>'));
                        CP.append($('<option value="500">500</option>'));
                        CP.append($('<option value="560">560</option>'));
                        CP.append($('<option value="630">630</option>'));
                        CP.append($('<option value="710">710</option>'));
                        CP.append($('<option value="800">180</option>'));
                        CP.append($('<option value="900">900</option>'));
                        CP.append($('<option value="1000">1000</option>'));
                        break;
                    case 4:
                        CP.append($('<option value="32">32</option>'));
                        CP.append($('<option value="40">40</option>'));
                        CP.append($('<option value="50">50</option>'));
                        CP.append($('<option value="63">63</option>'));
                        CP.append($('<option value="75">75</option>'));
                        CP.append($('<option value="90">90</option>'));
                        CP.append($('<option value="110">110</option>'));
                        CP.append($('<option value="125">125</option>'));
                        CP.append($('<option value="140">140</option>'));
                        CP.append($('<option value="160">160</option>'));
                        CP.append($('<option value="180">180</option>'));
                        CP.append($('<option value="200">200</option>'));
                        CP.append($('<option value="250">250</option>'));
                        CP.append($('<option value="280">280</option>'));
                        CP.append($('<option value="315">315</option>'));
                        CP.append($('<option value="355">355</option>'));
                        CP.append($('<option value="400">400</option>'));
                        CP.append($('<option value="450">450</option>'));
                        CP.append($('<option value="500">500</option>'));
                        CP.append($('<option value="560">560</option>'));
                        CP.append($('<option value="630">630</option>'));
                        CP.append($('<option value="710">710</option>'));
                        CP.append($('<option value="800">180</option>'));
                        CP.append($('<option value="900">900</option>'));
                        CP.append($('<option value="1000">1000</option>'));
                        break;
                    case 5:
                        CP.append($('<option value="25">25</option>'));
                        CP.append($('<option value="32">32</option>'));
                        CP.append($('<option value="40">40</option>'));
                        CP.append($('<option value="50">50</option>'));
                        CP.append($('<option value="63">63</option>'));
                        CP.append($('<option value="75">75</option>'));
                        CP.append($('<option value="90">90</option>'));
                        CP.append($('<option value="110">110</option>'));
                        CP.append($('<option value="125">125</option>'));
                        CP.append($('<option value="140">140</option>'));
                        CP.append($('<option value="160">160</option>'));
                        CP.append($('<option value="180">180</option>'));
                        CP.append($('<option value="200">200</option>'));
                        CP.append($('<option value="250">250</option>'));
                        CP.append($('<option value="280">280</option>'));
                        CP.append($('<option value="315">315</option>'));
                        CP.append($('<option value="355">355</option>'));
                        CP.append($('<option value="400">400</option>'));
                        CP.append($('<option value="450">450</option>'));
                        CP.append($('<option value="500">500</option>'));
                        CP.append($('<option value="560">560</option>'));
                        CP.append($('<option value="630">630</option>'));
                        CP.append($('<option value="710">710</option>'));
                        CP.append($('<option value="800">180</option>'));
                        break;
                    case 6:
                        CP.append($('<option value="20">20</option>'));
                        CP.append($('<option value="25">25</option>'));
                        CP.append($('<option value="32">32</option>'));
                        CP.append($('<option value="40">40</option>'));
                        CP.append($('<option value="50">50</option>'));
                        CP.append($('<option value="63">63</option>'));
                        CP.append($('<option value="75">75</option>'));
                        CP.append($('<option value="90">90</option>'));
                        CP.append($('<option value="110">110</option>'));
                        CP.append($('<option value="125">125</option>'));
                        CP.append($('<option value="140">140</option>'));
                        CP.append($('<option value="160">160</option>'));
                        CP.append($('<option value="180">180</option>'));
                        CP.append($('<option value="200">200</option>'));
                        CP.append($('<option value="250">250</option>'));
                        CP.append($('<option value="280">280</option>'));
                        CP.append($('<option value="315">315</option>'));
                        CP.append($('<option value="355">355</option>'));
                        CP.append($('<option value="400">400</option>'));
                        CP.append($('<option value="450">450</option>'));
                        CP.append($('<option value="500">500</option>'));
                        CP.append($('<option value="560">560</option>'));
                        CP.append($('<option value="630">630</option>'));
                        break;
                    case 7:
                        CP.append($('<option value="16">16</option>'));
                        CP.append($('<option value="20">20</option>'));
                        CP.append($('<option value="25">25</option>'));
                        CP.append($('<option value="32">32</option>'));
                        CP.append($('<option value="40">40</option>'));
                        CP.append($('<option value="50">50</option>'));
                        CP.append($('<option value="63">63</option>'));
                        CP.append($('<option value="75">75</option>'));
                        CP.append($('<option value="90">90</option>'));
                        CP.append($('<option value="110">110</option>'));
                        CP.append($('<option value="125">125</option>'));
                        CP.append($('<option value="140">140</option>'));
                        CP.append($('<option value="160">160</option>'));
                        CP.append($('<option value="180">180</option>'));
                        CP.append($('<option value="200">200</option>'));
                        CP.append($('<option value="250">250</option>'));
                        CP.append($('<option value="280">280</option>'));
                        CP.append($('<option value="315">315</option>'));
                        CP.append($('<option value="355">355</option>'));
                        CP.append($('<option value="400">400</option>'));
                        CP.append($('<option value="450">450</option>'));
                        CP.append($('<option value="500">500</option>'));
                        break;
                }
                break;
            case 4:
                switch (+$("#SDR5").val()) {
                    case 1:
                        CP.append($('<option value="50">50</option>'));
                        CP.append($('<option value="63">63</option>'));
                        CP.append($('<option value="75">75</option>'));
                        CP.append($('<option value="90">90</option>'));
                        CP.append($('<option value="110">110</option>'));
                        CP.append($('<option value="125">125</option>'));
                        CP.append($('<option value="140">140</option>'));
                        CP.append($('<option value="160">160</option>'));
                        CP.append($('<option value="180">180</option>'));
                        CP.append($('<option value="200">200</option>'));
                        CP.append($('<option value="250">250</option>'));
                        CP.append($('<option value="280">280</option>'));
                        CP.append($('<option value="315">315</option>'));
                        CP.append($('<option value="355">355</option>'));
                        CP.append($('<option value="400">400</option>'));
                        CP.append($('<option value="450">450</option>'));
                        CP.append($('<option value="500">500</option>'));
                        CP.append($('<option value="560">560</option>'));
                        CP.append($('<option value="630">630</option>'));
                        CP.append($('<option value="710">710</option>'));
                        CP.append($('<option value="800">180</option>'));
                        CP.append($('<option value="900">900</option>'));
                        CP.append($('<option value="1000">1000</option>'));
                        break;
                    case 2:
                        CP.append($('<option value="40">40</option>'));
                        CP.append($('<option value="50">50</option>'));
                        CP.append($('<option value="63">63</option>'));
                        CP.append($('<option value="75">75</option>'));
                        CP.append($('<option value="90">90</option>'));
                        CP.append($('<option value="110">110</option>'));
                        CP.append($('<option value="125">125</option>'));
                        CP.append($('<option value="140">140</option>'));
                        CP.append($('<option value="160">160</option>'));
                        CP.append($('<option value="180">180</option>'));
                        CP.append($('<option value="200">200</option>'));
                        CP.append($('<option value="250">250</option>'));
                        CP.append($('<option value="280">280</option>'));
                        CP.append($('<option value="315">315</option>'));
                        CP.append($('<option value="355">355</option>'));
                        CP.append($('<option value="400">400</option>'));
                        CP.append($('<option value="450">450</option>'));
                        CP.append($('<option value="500">500</option>'));
                        CP.append($('<option value="560">560</option>'));
                        CP.append($('<option value="630">630</option>'));
                        CP.append($('<option value="710">710</option>'));
                        CP.append($('<option value="800">180</option>'));
                        break;
                    case 3:
                        CP.append($('<option value="32">32</option>'));
                        CP.append($('<option value="40">40</option>'));
                        CP.append($('<option value="50">50</option>'));
                        CP.append($('<option value="63">63</option>'));
                        CP.append($('<option value="75">75</option>'));
                        CP.append($('<option value="90">90</option>'));
                        CP.append($('<option value="110">110</option>'));
                        CP.append($('<option value="125">125</option>'));
                        CP.append($('<option value="140">140</option>'));
                        CP.append($('<option value="160">160</option>'));
                        CP.append($('<option value="180">180</option>'));
                        CP.append($('<option value="200">200</option>'));
                        CP.append($('<option value="250">250</option>'));
                        CP.append($('<option value="280">280</option>'));
                        CP.append($('<option value="315">315</option>'));
                        CP.append($('<option value="355">355</option>'));
                        CP.append($('<option value="400">400</option>'));
                        CP.append($('<option value="450">450</option>'));
                        CP.append($('<option value="500">500</option>'));
                        CP.append($('<option value="560">560</option>'));
                        CP.append($('<option value="630">630</option>'));
                        break;
                }
                break;
        }
    });
    LResult(5);
    ClearText(5);
    SetEtc(5);
    Losses(5);
    //<---------------plastic---------------------->
    //<----------reinforced_concrete--------------->
    LResult(6);
    ClearText(6);
    SetEtc(6);
    Losses(6);
    //<----------reinforced_concrete--------------->
    //<---------------fiberglass------------------->
    LResult(7);
    ClearText(7);
    SetEtc(7);
    Losses(7);
    //<---------------fiberglass------------------->
    //<-----------------glass---------------------->
    LResult(8);
    ClearText(8);
    SetEtc(8);
    Losses(8);
    //<-----------------glass---------------------->
    //<----------------copper---------------------->
    let depth9 = [0.6, 0.7, 0.8, 0.9, 1.0, 1.2, 1.5, 2.0, 2.5, 3.0];
    $("#dh9").change(function () {
        let CP = $("#depth9");
        CP.empty();
        switch (+$("#dh9").val()) {
            case 6:
            case 8:
            case 12:
                CP.append($('<option value="' + depth9[0] + '">' + depth9[0] + '</option>'));
                CP.append($('<option value="' + depth9[2] + '">' + depth9[2] + '</option>'));
                CP.append($('<option value="' + depth9[4] + '">' + depth9[4] + '</option>'));
                break;
            case 10:
                for (let i = 0; i < 3; i++) {
                    CP.append($('<option value="' + depth9[i] + '">' + depth9[i] + '</option>'));
                }
                CP.append($('<option value="' + depth9[4] + '">' + depth9[4] + '</option>'));
                break;
            case 15:
                CP.append($('<option value="' + depth9[1] + '">' + depth9[1] + '</option>'));
                CP.append($('<option value="' + depth9[2] + '">' + depth9[2] + '</option>'));
                CP.append($('<option value="' + depth9[4] + '">' + depth9[4] + '</option>'));
                break;
            case 18:
                CP.append($('<option value="' + depth9[2] + '">' + depth9[2] + '</option>'));
                CP.append($('<option value="' + depth9[4] + '">' + depth9[4] + '</option>'));
                break;
            case 22:
            case 28:
                for (let i = 3; i < 7; i++) {
                    CP.append($('<option value="' + depth9[i] + '">' + depth9[i] + '</option>'));
                }
                break;
            case 35:
            case 42:
                CP.append($('<option value="' + depth9[5] + '">' + depth9[5] + '</option>'));
                CP.append($('<option value="' + depth9[6] + '">' + depth9[6] + '</option>'));
                break;
            case 54:
                for (let i = 5; i < 8; i++) {
                    CP.append($('<option value="' + depth9[i] + '">' + depth9[i] + '</option>'));
                }
                break;
            case 64:
            case 88.9:
                CP.append($('<option value="' + depth9[7] + '">' + depth9[7] + '</option>'));
                break;
            case 66.7:
                CP.append($('<option value="' + depth9[5] + '">' + depth9[5] + '</option>'));
                break;
            case 76.1:
                CP.append($('<option value="' + depth9[6] + '">' + depth9[6] + '</option>'));
                CP.append($('<option value="' + depth9[7] + '">' + depth9[7] + '</option>'));
                break;
            case 108:
                CP.append($('<option value="' + depth9[6] + '">' + depth9[6] + '</option>'));
                CP.append($('<option value="' + depth9[8] + '">' + depth9[8] + '</option>'));
                break;
            case 133:
                CP.append($('<option value="' + depth9[6] + '">' + depth9[6] + '</option>'));
                CP.append($('<option value="' + depth9[9] + '">' + depth9[9] + '</option>'));
                break;
            case 159:
                CP.append($('<option value="' + depth9[7] + '">' + depth9[7] + '</option>'));
                CP.append($('<option value="' + depth9[9] + '">' + depth9[9] + '</option>'));
                break;
            case 219:
            case 267:
                CP.append($('<option value="' + depth9[9] + '">' + depth9[9] + '</option>'));
                break;
        }
    });
    $(".temp9").click(function () {
        if ($("#temp91").prop("checked")) {
            $("#temp9").prop("disabled", true);
        } else {
            $("#temp9").removeProp("disabled");
        }
        if ($("#temp94").prop("checked")) {
            $("#HC9").removeProp("disabled");
        } else {
            $("#HC9").prop("disabled", true);
        }
    });
    $(".CR9").click(function () {
        for (let i = 1; i < 13; i++) {
            if ($("#resistance9" + i).prop("checked")) {
                $("#pieces9" + i).removeProp("disabled");
            } else {
                $("#pieces9" + i).prop("disabled", true);
            }
        }
    });
    $("#etc92").click(function () {
        if ($("#etc92").prop("checked")) {
            $("#tetc92").removeProp("disabled");
        } else {
            $("#tetc92").prop("disabled", true);
        }
    });
    $("#reset9").click(function () {
        for (let i = 1; i < 13; i++) {
            $("#resistance9" + i).prop("checked", false);
            $("#pieces9" + i).prop("disabled", true);
            $("#pieces9" + i).val(1);
            $("#etc92").prop("checked", false);
            $("#tetc92").prop("disabled", true);
            $("#tetc92").val("");
        }
    });
    LResult(9);
    ClearText(9);
    SetEtc(9);
    Losses(9);
    //<----------------copper---------------------->
    $("form").submit(function (event) {
        event.preventDefault();

        $.ajax({
            url: 'https://atri-energo.ru/converter/table_shevelow/send', //'https://atri-energo.ru/converter/table_shevelow/send'
            type: "POST",
            data: new FormData(this),
            contentType: false,
            cache: false,
            processData: false,
            success: function (result) {
                let res = JSON.parse(result);

                function mainRes(n) {
                    $("#dText" + n).html(res.dp);
                    $("#vText" + n).html(res.V);
                    $("#iText" + n).html(res.ires);
                    $("#iText" + n).attr("title", "Потери давления на 1 м "+(res.ires / 1000 * 9806.38).toFixed(2)+" Па/м");
                }

                switch (res.nameType) {
                    case 1:
                        mainRes(1);
                        break;
                    case 2:
                        mainRes(2);
                        break;
                    case 3:
                        mainRes(3);
                        break;
                    case 4:
                        mainRes(4);
                        break;
                    case 5:
                        mainRes(5);
                        break;
                    case 6:
                        mainRes(6);
                        break;
                    case 7:
                        mainRes(7);
                        break;
                    case 8:
                        mainRes(8);
                        break;
                    case 9:
                        mainRes(9);
                        $("#hText9").html(res.H);
                        break;
                    case 10:
                        mainRes(10);
                        break;
                    case 11:
                        mainRes(11);
                        break;
                }
            }
        });
    });
});