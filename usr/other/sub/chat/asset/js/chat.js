const CHAT = {
    template_message: template_portal,
    add(request) {
        return new Promise((resolve, reject) => {
            $.post("/ajax.php?do=add_chat", request, function(id) {
                resolve(id);
            });
        });
    },
    uid_change(request) {
        //{chat_id:"", id:[], status:"", }
        //id -> uid
        return new Promise((resolve, reject) => {
            $.post("/ajax.php?do=change_uid_chat", request, function() {
                resolve();
            });
        });
    },
    find_append_or_create(request) {
        //{module:"", pid:"",}
        return new Promise((resolve, reject) => {
            const uid = $("#chat_uid").val();
            find_chat_all({ module: request.module, pid: request.pid }).then(data => {
                data.forEach(cc => {
                    this.uid_change({ chat_id: cc.id, id: [uid], status: "true" }).then(() => {
                        resolve();
                    });
                });
            }, () => {
                this.add(request).then(() => {
                    resolve();
                })
            });
        });
    },
};

function chat_footer_enter() {
    let pressed = new Set();
    const item = $("#chat_footer-input");
    item.on("keydown", function(e) {
        pressed.add(e.keyCode);
    });

    item.on("keyup", function(e) {

        if (pressed.has(13) && !pressed.has(16)) {
            let message = $(this).val();
            addMessage(message);
        }

        pressed.delete(e.keyCode);
    });
}

$(() => {
    console.log("Chat active!");

    chat_footer_enter();

    $("body").on("keydown", function() {
        // console.log(keyCode);
    });

    if ("notif_init" in CHAT) {
        CHAT.notif_init();
    }
});

//websocket
ws = new WebSocket(`wss://atri-energo.ru/websocket?token=${$("#chat_token").val()}&wss_type=chat`);
ws.onopen = function() {
    console.log("connection success");
};
ws.onmessage = function(e) {
    const json = JSON.parse(e.data);
    const chat_id = get_chat_id();

    is_up_chat_list(json[0]["chat_id"]);
    is_update_last_message(json[0]);

    if (chat_id === +json[0]["chat_id"]) {
        append_message(template_message(false, json[0]));
        message_scroll_top();
        read_chat_message(chat_id);
    } else {
        if ('add_count_no_read_message' in CHAT) {
            CHAT.add_count_no_read_message(json[0]["chat_id"]);
        }
        if ('notif_onmessage' in CHAT) {
            CHAT.notif_onmessage(json);
        }
    }
    is_update_all_no_read_message_count();
};
ws.onclose = function() {
    console.log("connection close");
};

function soundPlayOnMessageChat() {
    const audio = new Audio(); // Создаём новый элемент Audio
    audio.src = '/usr/other/sub/chat/asset/audio/zvuk-icq.mp3'; // Указываем путь к звуку "клика"
    audio.autoplay = true; // Автоматически запускаем
}

function is_update_all_no_read_message_count() {
    if ('update_all_no_read_message_count' in CHAT) {
        CHAT.update_all_no_read_message_count();
    }
}

function change_chat(id, field, value) {
    return new Promise((resolve, reject) => {
        $.post("/ajax.php?do=change_chat", { id, field, value }, function() {
            resolve();
        })
    });
}

function append_text_input(text) {
    const elem = $("#chat_footer-input");
    const old_text = elem.val();
    const new_text = old_text + text;
    elem.val(new_text);
}

function find_chat(module, pid) {
    return new Promise((resolve, reject) => {
        $.post("/ajax.php?do=find_chat", { module, pid }, function(chat_data) {
            if (chat_data === "[]") {
                reject();
            } else {
                resolve(JSON.parse(chat_data));
            }
        });
    });
}

function find_chat_all(data) {
    return new Promise((resolve, reject) => {
        $.post("/ajax.php?do=find_chat_all", data, function(chat_data) {
            if (chat_data === "[]") {
                reject();
            } else {
                resolve(JSON.parse(chat_data));
            }
        });
    });
}

function get_chat_id() {
    let chat_id = $("#chat_id").val();
    if (chat_id === "" || chat_id === undefined || chat_id === null) {
        return false;
    } else {
        return +chat_id;
    }
}

function addMessage(message) {
    let data = {};
    let new_message;
    if (typeof message == 'object') {
        new_message = message.message;
        data = message;
    } else {
        new_message = message;
    }
    new_message = new_message.trim();
    const message_test = new_message.replace(/ /g, "");
    if (message_test.length > 0 && message_test !== "" && message !== undefined && message !== null) {
        const chat_id = $("#chat_id").val();
        data.chat_id = chat_id;
        data.message = new_message;
        data.reply = $("#chat_reply").val();
        $("#chat_footer-input").val("");
        reply_message();
        $.post("/ajax.php?do=add_message_chat", data, function(message_id) {
            load_message(message_id);
            is_up_chat_list(chat_id);
            read_chat_message(chat_id).then(() => {
                is_update_all_no_read_message_count();
            });
            notif_send_message(chat_id, message_id);
        });
    }
}

function is_up_chat_list(id) {
    if ('up_chat_list' in CHAT) {
        CHAT.up_chat_list(id);
    }
}

function is_update_last_message(data) {
    if ('update_last_message' in CHAT) {
        CHAT.update_last_message(data);
    }
}

function loadChatList(chat_id = null) {
    return new Promise((resolve, reject) => {
        const limit = change_list_page();
        $.post("/ajax.php?do=load_chat_list", { chat_id, limit }, function(data) {
            resolve(data);
        });
    })
}

function change_chat_id(chat_id) {
    $("#chat_id").val(chat_id);
}

function check_chat_site() {
    let chat_id = get_chat_id();
    if (chat_id) {
        $.post("/ajax.php?do=check_chat_site", { chat_id }, function(data) {
            if (data === "") {
                toggle_chat_site();
            } else {
                toggle_chat_site(JSON.parse(data));
            }
        });
    } else {
        toggle_chat_site();
    }
}

function checkChatId() {
    const chat_id = $("#chat_id").val();
    if (chat_id === "" || chat_id === undefined || chat_id === null) {
        $("#chat_footer-input").prop("disabled", true);
        $("#file").prop("disabled", true);
        $("#chat_header-users").hide();
        $("#chat_footer").hide();
        return false;
    } else {
        $("#chat_footer-input").prop("disabled", false);
        $("#file").prop("disabled", false);
        $("#chat_header-users").show();
        $("#chat_footer").show();
        return true;
    }
}

function changeChat(chat_id, title) {
    if (chat_id && title) {
        change_chat_id(chat_id);
        $("#chat_header-title").html(title);
        $("#chat_title").val(title);
        change_page(0);
        load_message();
        check_chat_site();
        checkChatId();
        if ('read_chat_message_portal' in CHAT) {
            CHAT.read_chat_message_portal(chat_id);
        }
        is_active_chat(chat_id);
        read_chat_message(chat_id).then(() => {
            is_update_all_no_read_message_count();
        });
    }
}

function is_active_chat(chat_id) {
    if ('active_chat' in CHAT) {
        CHAT.active_chat(chat_id);
    }
}

function toggle_chat_site(data = null) {
    const elem = $("#chat_header-site");
    if (data === null) {
        elem.hide();
    } else {
        elem.show();
        elem.data("id", data.id);
        elem.data("title", data.title);
    }
}

function load_users_list(chat_id) {
    if (chat_id) {
        $.post("/ajax.php?do=load_users_list", { chat_id }, function(data) {
            $("#chat_list-users-form").html(data);
        });
    }
}

function load_message(message_id = false, before = false) {
    return new Promise((resolve, reject) => {
        let chat_id = $("#chat_id").val();
        if (chat_id) {
            let uid = +$("#chat_uid").val();
            let limit = +$("#chat_page").val();
            $.post("/ajax.php?do=load_chat_message", { chat_id, limit, message_id }, json => {
                let data = JSON.parse(json);
                if (!before && !message_id) {
                    $("#chat_body-message").empty();
                }
                if (data.length > 0) {
                    data.forEach((elem, i) => {
                        let flag = false;
                        if (+elem.uid === uid) {
                            flag = true;
                        }
                        if (before) {
                            prepend_message(template_message(flag, elem));
                        } else {
                            append_message(template_message(flag, elem));
                            is_update_last_message(elem);
                        }
                    });
                    if (!before) {
                        message_scroll_top();
                    }
                }
                resolve();
            });
        }
    });
}

function template_message(flag, elem) {
    return CHAT.template_message(flag, elem);
}

function template_portal(flag, elem) {
    const no_file = +elem.file === 0 || elem.file === undefined;
    const backplate = +elem.backplate === 0 ? '' : 'message_backplate';
    if (no_file && (elem.message === null || elem.message.length == 0)) return null;
    let str = `
        <div data-id="${elem.id}" class="message ${flag ? 'message-right' : 'message-left'}" id="chat_message_${elem.id}">
            ${flag ? `` : `<div class="message_avatar"><img src="${elem.avatar}"></div>`}
            <div class="${backplate} ${no_file ? 'message_text' : 'message_file'}">`;
                // console.log(elem);
                if("reply" in elem && "id" in elem.reply){
                    const reply_message = template_portal_message(elem.reply);
                    str += `<div class="message_reply message-padding">
                        ${reply_message}
                    </div>`;
                }
                str += `<div class="message-padding">`;
                str += template_portal_message(elem);
                str +=`<div class="c_flex">
                        <div class="message_text-date">${elem.date}</div>
                        <div><i class="fas fa-reply reply-btn" title="Ответ" onclick="reply_message(${elem.id});"></i></div>
                    </div>
                </div>
            </div>
        </div>`;
        return str;
}

function template_portal_message(elem){
    const no_file = +elem.file === 0 || elem.file === undefined;
    let str = ``;
    str += `<div class="c_flex">`;
    if(!("reply" in elem)){
        str += `<div class="message_reply-symbol">&raquo;</div>`;
    }
    str += `<div class="message_text-fio">${elem.fio}</div>`;
    str += `</div>`;
    if(no_file){
        const message = prepare_message(elem.message);
        str += `<div class="message_text-text">${message}</div>`; 
    } else {
        str += `<div class="message_text-file">`;
        switch(elem.fileExtension){
            default:
                str += `<a target="_blank" href="/download/${elem.file}">${elem.fileName}</a>`;
            break;
            case "jpg":
            case "jpeg":
            case "bmp":
            case "png":
                str += `<a href="${elem.fileUrl}" class="message_fancybox"><img src="/${elem.fileUrl}" class="message_image_img"></a>`;
            break;
            case "pdf":
                str += `<a data-type="iframe" href="/${elem.fileUrl}" class="message_fancybox message_iframe_a"></a>
                    <iframe scrolling="no" src="/${elem.fileUrl}" frameborder="0" class="message_iframe"></iframe>`;
            break;
        }
        str += `</div>`;
    }
    return str;
}

function reply_message(id = null){
    const footer = $("#chat_footer");
    const id_name = "chat_reply_container";
    $("#"+id_name).remove();
    if(id !== null){
        const chat_message = $("#chat_message_"+id);
        const text = chat_message.find(".message_text-text").html();
        footer.prepend(`<div id="${id_name}">
            <input type="hidden" id="chat_reply" value="${id}">
            <div>${text}</div>
            <div><i class="fas fa-times cp" onclick="reply_message();"></i></div>
        </div>`);
    }
}

function prepare_message(message){
    let new_message = prepare_message_smile(message);
    new_message = prepare_message_sticker(new_message);
    new_message = convertNewLinesToBr(new_message);
    return new_message;
}

function prepare_message_smile(message){
    let new_message = message;
    const allow_smile = get_allow_smile();
    for(let i = 0; i < allow_smile.length; i++){
        const smile = allow_smile[i].replace(/:/g, "");
        const src = `/usr/other/sub/chat/asset/img/smiles/${smile}.gif`;
        const rep = `<img src="${src}" alt="${allow_smile[i]}" title="${allow_smile[i]}">`;
        const reg = new RegExp(allow_smile[i], 'g');
        new_message = new_message.replace(reg, rep);
    }
    return new_message;
}

function prepare_message_sticker(message){
    let new_message = message;
    const allow_sticker = get_allow_sticker();
    for(let i = 0; i < allow_sticker.length; i++){
        const sticker = allow_sticker[i].replace(/!/g, "");
        const src = `/usr/other/sub/chat/asset/img/stickers/${sticker}.png`;
        const rep = `<img src="${src}" alt="${allow_sticker[i]}" title="${allow_sticker[i]}" class="chat_sticker">`;
        const reg = new RegExp(allow_sticker[i], 'g');
        new_message = new_message.replace(reg, rep);
    }
    return new_message;
}

function convertNewLinesToBr(str) {
    str = str.replace(/(?:\r\n|\r|\n)/g, '<br />');
    return str;
}

function get_allow_smile(){
    const smile = JSON.parse($("#smile_allow_list").val());
    return smile;
}

function get_allow_sticker(){
    const sticker = JSON.parse($("#sticker_allow_list").val());
    return sticker;
}

function append_message(message){
    let elem = $("#chat_body-message");
    elem.append(message);
    return elem.children().eq(-1);
}

function prepend_message(message){
    let elem = $("#chat_body-message");
    elem.prepend(message);
    return elem.children().eq(0);
}

function message_scroll_top(){
    //chat_body-message
    $("#chat_body").animate({ scrollTop: ($("#chat_body-message")[0].scrollHeight * 2) }, "slow");
    $(".message_fancybox").fancybox();
}

function load_file(This){
    let chat_id = $("#chat_id").val();
    let array_length = $(This)[0].files.length;
    let fio = $("#chat_fio").val();
    let message = "Файл";

    let date_ = new Date();

    let year = date_.getFullYear();
    let month = ("00" + (+date_.getMonth() + 1)).slice(-2);
    let day = ("00" + date_.getDate()).slice(-2);
    let hours = ("00" + date_.getHours()).slice(-2);
    let minutes = ("00" + date_.getMinutes()).slice(-2);
    let seconds = ("00" + date_.getSeconds()).slice(-2);
    let date = day + "." + month + "." + year + " " + hours + ":" + minutes + ":" + seconds;

    for(let i = 0; i < array_length; i++){
        let data = new FormData();
        data.append('file', $(This)[0].files[i]);
        message = $(This)[0].files[i].name;
        data.append('chat_id', chat_id);

        let elem = append_message(template_message(true, {fio, message, date})).children().eq(0);

        $.ajax({
            url: '/ajax.php?do=chat_save_file',
            data: data,
            processData: false,
            contentType: false,
            type: 'POST',
            xhr: function(){
                let xhr = $.ajaxSettings.xhr(); 
                xhr.upload.addEventListener('progress', function(evt){ 
                    if (evt.lengthComputable) { 
                        let percentComplete = Math.ceil(evt.loaded / evt.total * 100);
                        elem.css({"background" : `linear-gradient(90deg, rgba(238,106,151,1) 0%, rgba(255,255,255,1) ${percentComplete}%, rgba(255,255,255,1) 100%)`});
                    }
                }, false);
                return xhr;
            },
            success: function (message_id) {
                load_message(message_id).then(() => {
                    elem.remove();
                });
            }
        });
    }
    message_scroll_top();
   
    $(This).val("");
}   

function changeChild(status, i){
    $(".c_parent_"+i).prop("checked", status);
    let id = [];
    $(".c_parent_"+i).each((j, elem) => {
        id.push($(elem).val());
    });
    change_uid(id, status);
}

function change_uid(id = [], status){
    let chat_id = get_chat_id();
    if(chat_id){
        let uid = $("#chat_uid").val();
        if(id[0] === uid){
            closeChat();
        }
        CHAT.uid_change({chat_id, id, status});
    } else {
        swal("Ошибка", "Чат не выбран!", "error");
    }
}

function load_before_message(This){
    let flag = $(This).data("flag");
    if(flag !== false){
        let scroll = +$(This).scrollTop();
        if(scroll === 0){
            $(This).data("flag", true);
            change_page("plus");
            let lastScrollHeight = $(This)[0].scrollHeight;
            load_message(false, true).then(() => {
                $(This).removeData("flag");
                let scrollDiff = $(This)[0].scrollHeight - lastScrollHeight;
                $(This)[0].scrollTop += scrollDiff;
            });
        }
    }
}

function change_page(value = null){
    const elem = $("#chat_page");
    let page = elem.val();
    if(value === null){
        return page;
    }
    switch(value){
        default:
            page = value;
        break;
        case "plus":
            page++;
        break;
        case "minus":
            page--;
        break;
    }
    elem.val(page);
}

function load_after_list(This){
    let flag = $(This).data("flag");
    if(flag !== false){
        if(This.scrollHeight - This.scrollTop === This.clientHeight){
            $(This).data("flag", true);
            change_list_page("plus");
            load_chat_lists(false).then(() => {
                $(This).removeData("flag");
            });
        }
    }
}

function change_list_page(value = null){
    const elem = $("#chat_list_page");
    let page = elem.val();
    if(value === null){
        return page;
    }
    switch(value){
        default:
            page = value;
        break;
        case "plus":
            page++;
        break;
        case "minus":
            page--;
        break;
    }
    elem.val(page);
}

function read_chat_message(chat_id){
    return new Promise((resolve, reject) => {
        $.post("/ajax.php?do=read_chat_message", {chat_id}, function(){
            resolve();
        });
    });
}

function toggle_smile_bar(){
    const elem = $("#chat_smile_bar");
    elem.slideToggle();
    if("lazy_load_smiles" in CHAT){
        CHAT.lazy_load_smiles();
    }
}

function notif_send_message(chat_id, message_id){
    return new Promise((resolve, reject) => {
        $.post("/ajax.php?do=notif_send_message", {chat_id, message_id}, function(){
            resolve();
        })
    });
}

function location_this(This, data = "link"){
    const link = $(This).data(data);
    window.open(link);
}

function get_all_no_read_message_count(){
    return new Promise((resolve, reject) => {
        $.post("/ajax.php?do=get_all_no_read_message_count", function(count){
            resolve(count);
        });
    });
}

async function get_chat_data(chat_id){
    const form = new FormData;
    form.append("chat_id", chat_id);
    return await ( await fetch(`/ajax.php?do=get_chat_data`, {
        method: "POST",
        body: form
    }) ).json();
}