loadData();

function decodeHTML(html) {
    let txt = $('<div></div>');
    txt.html(html);
    return txt.text();
};

const delay = 600;

const dictionary = {
    q: "Нагрузка",
    t1: "Температура в подающем трубопроводе",
    t2: "Температура в обратном трубопроводе",
    p1: "Давление в подающем трубопроводе",
    p2: "Давление в обратном трубопроводе",
    t11: "Температура в подающем трубопроводе отопления",
    t21: "Температура в обратном трубопроводе отопления",
    dp: "Потери давления в системе отопления",
    h: "Высота здания",
    pch: "Насос",
    typeNag: "Вводной параметр",
    qGVS: "Нагрузка",
    GVSgt1: "Расход горячей воды",
    GVSt1: "Температура в подающем трубопроводе в летний период",
    GVSt2: "Температура в обратном трубопроводе в летний период",
    GVSt3: "Температура горячей воды",
    GVStv1: "Температура холодной воды",
    pb1: "Давление в трубопроводе холодной воды",
    shemaPodkl: "Схема подключения",
    povNasos: "Повысительный насос",
    typeBlock: "Система теплопотребления",
};

const fieldCount = {
    block_vvoda: {
        countField: [0, 0],
        countLoad: [0, 0],
    },
    block_otopleniya: {
        countField: [6, 2],
        countLoad: [1, 1],
    },
    block_podpitky: {
        countField: [6, 0],
        countLoad: [1, 1],
    },
    block_gvs: {
        countField: [6, 2],
        countLoad: [1, 1],
    },
};

const dataField = [
    {
        name: "Насосы",
        data: [
            {
                name: "расход",
                type: "text",
                unit: "м³/ч",
            },
            {
                name: "напор",
                type: "text",
                unit: "м.в.ст.",
            },
            {
                name: "напряжение",
                type: "select",
                value: ["220", "380"],
                unit: "В",
            },
            {
                name: "мощность",
                type: "text",
                unit: "кВт",
            },
            {
                name: "номинальный ток",
                type: "text",
                unit: "А",
            },
            {
                name: "модель",
                type: "text",
                unit: "",
            },
        ]
    },
    {
        name: "Теплообменник",
        data: [
            {
                name: "модель",
                type: "text",
                unit: "",
            },
            {
                name: "потери по нагреваемой стороне",
                type: "text",
                unit: "м.в.ст.",
            },
        ]
    }
];

let getBlockData = {}
function loadData() {
    let id = $("#mainId").val();
    let getBlockDataSave = {};
    $("#blockInfo").empty();
    $("#blockCharacteristic").empty();
    $("#blockFormMain").empty();
    let str = '';
    let flag = false;
    let json;
    $.post("/ajax.php?do=getDataSelectionRequest", { id: id, field: "detail_save" }, function (data) {
        if (data !== "") {
            flag = true;
            json = JSON.parse(data);
            str += '<ul class="blockInfoOne">';
            for (let i = 0; i < json["blocks"].length; i++) {
                let elem = json["blocks"][i];
                str += '<li class="' + (+elem["id"] === -1 ? "blockInfoOneTitleError blockInfoOneTitle" : "blockInfoOneTitle") + '" id="' + elem["name"] + '__' + elem["index"] + '_status" data-key="' + elem["index"] + '">' + elem["type"] + ' (' + elem["code"] + ')</li>';
            }
            str += '</ul>';
        } else {
            str = "<div>Данные не заполнены!</div>";
        }
        str += '<input type="button" value="DWG" id="getDetailBlock" class="btn btn-success">';
        $("#blockInfo").append(str);



        if (flag) {
            $.post("/ajax.php?do=getDataSelectionRequest", { id: id, field: "", status: "all" }, function (data) {
                getBlockData = JSON.parse(data);
                $.post("/ajax.php?do=getDataSelectionRequest", { id: id, field: "view_request" }, function (data) {
                    if (data !== "") {
                        getBlockDataSave = JSON.parse(data);
                    }

                    for (let jsonKey in json["blocks"]) {
                        str = "";
                        let key = json["blocks"][jsonKey]["index"];
                        let p = json["blocks"][jsonKey]["name"];

                        let emptyFlag = true;
                        str += '<div id="' + p + '__' + key + '" class="conBlock">';
                        str += '<input type="hidden" value="" name="">';
                        for (let j = 0; j <= dataField.length; j++) {
                            if (fieldCount[p]["countField"][j] > 0) {
                                emptyFlag = false;
                                str += '<h5 class="title">' + dataField[j]["name"] + '</h5>';
                                for (let i = 0; i < fieldCount[p]["countField"][j]; i++) {
                                    let name = dataField[j]["data"][i]["name"];
                                    let unit = dataField[j]["data"][i]["unit"];
                                    str += '<div class="form-group">';
                                    str += '<div class="col-sm-12"><label>' + ucFirst(name) + '</label></div>';
                                    str += '<div class="input-group">';
                                    let rusToLatin = rus_to_latin(name);
                                    let id_name = p + "__" + key + "__" + rusToLatin;
                                    let value = "";
                                    if (p in getBlockDataSave) {
                                        if (key in getBlockDataSave[p]) {
                                            if (rusToLatin in getBlockDataSave[p][key]) {
                                                value = getBlockDataSave[p][key][rusToLatin]
                                            }
                                        }
                                    }
                                    switch (dataField[j]["data"][i]["type"]) {
                                        default:
                                        case "text":
                                            str += '<input id="' + id_name + '" name="' + id_name + '" class="form-control changeVal" value="' + value + '" type="text" placeholder="Введите ' + name + '">';
                                            break;
                                        case "select":
                                            str += '<select id="' + id_name + '" name="' + id_name + '" class="form-control changeVal">';
                                            str += '<option value="" ' + (value === "" ? 'selected' : '') + ' disabled>Выберите значение</option>';
                                            for (let k = 0; k < dataField[j]["data"][i]["value"].length; k++) {
                                                let kElem = dataField[j]["data"][i]["value"][k];
                                                str += '<option value="' + kElem + '" ' + (value === kElem ? 'selected' : '') + '>' + kElem + '</option>';
                                            }
                                            str += '</select>';
                                            break;
                                    }
                                    str += '<span class="input-group-addon">' + unit + '</span>';
                                    str += '</div>';
                                    str += '</div>';
                                }
                                if (fieldCount[p]["countLoad"][j] > 0) {
                                    str += '<div class="form-group">';
                                    str += '<div class="col-sm-12"><label>Файл:</label></div>';
                                    for (let f = 0; f < fieldCount[p]["countLoad"][j]; f++) {
                                        let value = "";
                                        let value_name = "";
                                        if (p in getBlockDataSave) {
                                            if (key in getBlockDataSave[p]) {
                                                if ("file" + j in getBlockDataSave[p][key]) {
                                                    value = getBlockDataSave[p][key]["file" + j];
                                                    value_name = getBlockDataSave[p][key]["file" + j + "_name"];
                                                }
                                            }
                                        }
                                        let name_file = p + '__' + key + '__file' + j;
                                        str += '<input type="file" id="' + name_file + '" name="' + name_file + '" class="form-control inpFile" style="display: ' + (value === "" ? 'block' : 'none') + '">';
                                        if (value !== "") {
                                            str += retFileDiv(value_name, name_file, value);
                                        }
                                    }
                                    str += '</div>';
                                }
                            }
                        }
                        if (emptyFlag) {
                            str += 'Данные не требуются!';
                        }
                        str += '</div>';
                        $("#blockFormMain").append(str);
                        if(p !== "block_vvoda"){
                            checkStatus(p + "__" + key);
                        }
                    }
                });
            });
        }

        /*---*/
    });
}

function ucFirst(str) {
    if (!str) return str;

    return str[0].toUpperCase() + str.slice(1);
}

function rus_to_latin(str) {

    var ru = {
        'а': 'a', 'б': 'b', 'в': 'v', 'г': 'g', 'д': 'd',
        'е': 'e', 'ё': 'e', 'ж': 'j', 'з': 'z', 'и': 'i',
        'к': 'k', 'л': 'l', 'м': 'm', 'н': 'n', 'о': 'o',
        'п': 'p', 'р': 'r', 'с': 's', 'т': 't', 'у': 'u',
        'ф': 'f', 'х': 'h', 'ц': 'c', 'ч': 'ch', 'ш': 'sh',
        'щ': 'shch', 'ы': 'y', 'э': 'e', 'ю': 'u', 'я': 'ya', ' ': '_'
    }, n_str = [];

    str = str.replace(/[ъь]+/g, '').replace(/й/g, 'i');

    for (var i = 0; i < str.length; ++i) {
        n_str.push(
            ru[str[i]]
            || ru[str[i].toLowerCase()] == undefined && str[i]
            || ru[str[i].toLowerCase()].replace(/^(.)/, function (match) { return match.toUpperCase() })
        );
    }

    return n_str.join('');
}

$(document).on("click", ".blockInfoOneTitle", function () {
    $("#blockCharacteristic").show("slide", { direction: "left" }, delay, function () {
        $("#blockForm").show("slide", { direction: "left" }, delay, function () {
            $("#saveBtn").show();
        });
    });
    $("#blockCharacteristic").empty();

    let key = $(this).data("key");

    let str = '';

    const noDic = false;

    // 2 окно
    str += '<ul class="blockCharacteristicOne">';
    let p = Object.keys(getBlockData["iDatailEditor"][key]["data"])[0];
    for (i in getBlockData["iDatailEditor"][key]["data"][p]) {
        if (i in dictionary || noDic) {
            let elem = getBlockData["iDatailEditor"][key]["data"][p][i];
            let type;
            switch (i) {
                default:
                    if (dictionary[i] === undefined) {
                        str += '<li class="blockCharacteristicOneTitle">' + i + ': ' + elem + '</li>';
                    } else {
                        str += '<li class="blockCharacteristicOneTitle">' + dictionary[i] + ': ' + elem + '</li>';
                    }
                    break;
                case "q":
                case "dp":
                    str += '<li class="blockCharacteristicOneTitle">' + dictionary[i] + ': ' + elem + ' ' + getBlockData["iDatailEditor"][key]["data"][p][i + "Type"] + '</li>';
                    break;
                case "t11":
                case "t21":
                case "GVSt1":
                case "GVSt2":
                case "GVSt3":
                case "GVStv1":
                    str += '<li class="blockCharacteristicOneTitle">' + dictionary[i] + ': ' + elem + '℃</li>';
                    break;
                case "h":
                    str += '<li class="blockCharacteristicOneTitle">' + dictionary[i] + ': ' + elem + ' м</li>';
                    break;
                case "pch":
                    str += '<li class="blockCharacteristicOneTitle">' + dictionary[i] + ': ' + (+elem === 1 ? "с ПЧ" : "без ПЧ ") + '</li>';
                    break;
                case "typeNag":
                    str += '<li class="blockCharacteristicOneTitle">' + dictionary[i] + ': ' + (+elem === 1 ? "Мощность" : "Расход") + '</li>';
                    break;
                case "qGVS":
                    if (+getBlockData["iDatailEditor"][key]["data"][p]["typeNag"] === 1) {
                        str += '<li class="blockCharacteristicOneTitle">' + dictionary[i] + ': ' + elem + ' ' + getBlockData["iDatailEditor"][key]["data"][p]["qSelGVS"] + '</li>';
                    }
                    break;
                case "GVSgt1":
                    if (+getBlockData["iDatailEditor"][key]["data"][p]["typeNag"] === 2) {
                        str += '<li class="blockCharacteristicOneTitle">' + dictionary[i] + ': ' + elem + ' м³/ч</li>';
                    }
                    break;
                case "pb1":
                    str += '<li class="blockCharacteristicOneTitle">' + dictionary[i] + ': ' + elem + ' ' + getBlockData["iDatailEditor"][key]["data"][p]["pb1SelGVS"] + '</li>';
                    break;
                case "shemaPodkl":
                    switch (+elem) {
                        case 1:
                            type = "Согласно СП";
                            break;
                        case 2:
                            type = "Параллельная";
                            break;
                        case 3:
                            type = "Смешанная";
                            break;
                    }
                    str += '<li class="blockCharacteristicOneTitle">' + dictionary[i] + ': ' + type + '</li>';
                    break;
                case "typeBlock":
                    switch (+elem) {
                        case 1:
                            type = "Отопления";
                            break;
                        case 2:
                            type = "Вентиляции";
                            break;
                        case 3:
                            type = "Технологии";
                            break;
                    }
                    str += '<li class="blockCharacteristicOneTitle">' + dictionary[i] + ': ' + type + '</li>';
                    break;
            }
        }
    }
    //t 
    for (i in getBlockData["iData"]) {
        if (i in dictionary || noDic) {
            let elem = getBlockData["iData"][i];
            switch (p) {
                case "block_vvoda":
                case "block_otopleniya":
                    if (dictionary[i] === undefined) {
                        str += '<li class="blockCharacteristicOneTitle">' + i + ': ' + elem + '</li>';
                    } else {
                        str += '<li class="blockCharacteristicOneTitle">' + dictionary[i] + ': ' + elem + '℃</li>';
                    }
                    break;
            }
        }
    }
    //p
    for (i in getBlockData["iDataGeneral"]) {
        if (i in dictionary || noDic) {
            let elem = getBlockData["iDataGeneral"][i];
            switch (p) {
                case "block_vvoda":
                case "block_otopleniya":
                case "block_gvs":
                    if (dictionary[i] === undefined) {
                        str += '<li class="blockCharacteristicOneTitle">' + i + ': ' + elem + '</li>';
                    } else {
                        str += '<li class="blockCharacteristicOneTitle">' + dictionary[i] + ': ' + elem + ' ' + getBlockData["iDataGeneral"][i + "Type"] + '</li>';
                    }
                    break;
            }
        }
    }
    str += '</ul>';
    $("#blockCharacteristic").append(str);

    // 3 окно
    $(".conBlock").hide();
    $("#" + p + '__' + key).show();
});

$(document).on("change", ".changeVal", function () {
    let id = $(this).attr("id").split("__");
    checkStatus(id[0] + '__' + id[1]);
});

function checkStatus(id) {
    let flag = true;
    $('[id^="' + id + '__"]').each(function (i, elem) {
        if (+$(elem).attr("id").indexOf(id + '__file') === -1) {
            if ($(elem).val() === "" || $(elem).val() === null) {
                flag = false;
                return false;
            }
        }
    });
    if (flag) {
        $("#" + id + "_status").addClass("confirmFilling");
    } else {
        $("#" + id + "_status").removeClass("confirmFilling");
    }
}

$(document).on("click", "#saveBtn", function () {
    let data = new FormData($("#blockFormMain")[0]);
    data.set("selection_request_id", $("#mainId").val());
    $.ajax({
        url: "/ajax.php?do=saveSelectionRequest",
        data: data,
        processData: false,
        contentType: false,
        type: 'POST',
        success: function (data) {
            console.log("Success send!");
            swal("Успешно!", "Ваши данные сохранены", "success");
        }
    });
});

function retFileDiv(text, id, value) {
    return '<div class="col-sm-12 mb10">' + text + '<span class="ml20"><i class="fas fa-trash-alt text-danger removeFile cp"></i></span><input type="hidden" value="' + value + '" name="' + id + '_save"></div>';
}

$(document).on("change", ".inpFile", function () {
    $(this).hide();
    let id = $(this).attr("id");
    let value = $(this).val();
    let text = value.split('\\');
    text = text[text.length - 1];
    $(this).after(retFileDiv(text, id, ""));
});

$(document).on("click", ".removeFile", function () {
    let parent = $(this).parents("div")[0];
    parent = $(parent);
    parent.prev().show();
    parent.prev().val("");
    parent.remove();
});

$(document).on("click", "#getDetailBlock", function(){
    let id = $("#mainId").val();
    $(".raschetStoimostyResult").html("Загрузка данных...");
    $("#raschetStoimostyModal").modal("show");
    $.post("/administration/module/selection/getDetailBlock", {id:id}, function(data){
        let res = onAjaxSuccess(data);
        $('.raschetStoimostyResult').html(res);
    });
});

function onAjaxSuccess(data) {

    //$('.raschetStoimostyResult').html(data);
    //$('.raschetStoimostyLoading').css('display','none');
    //$('.raschetStoimostyResult').css('display','block');

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
    //res += '<div class="modalBottom"><button type="button" class="btn btn-dark arcticmodal-close">Отмена</button><button type="button" onclick="window.open(\'/selection/my\');" class="btn btn-danger">Мои расчеты</button></div>';

    return res;
}

