$(function(){
    update_all_no_read_message_count();
});

CHAT.notif_onmessage = function(json){
    let message = "";
    if(json[0]["message"].length > 100){
        message = message.substring(0, 100);
        message += "...";
    } else {
        message = json[0]["message"];
    }

    const h5 = $("<div></div>").append(`<div>Новое сообщение в: ${json[0]["chat_title"]}</div><div>${message}</div>`);

    const notif = $.notify({
      title: h5,
    }, { 
      style: 'foo',
      autoHide: false,
      clickToHide: false
    });

    $(notif.title).parents(".notifyjs-foo-base").data("chat_id", json[0]["chat_id"]);
    $(notif.title).parents(".notifyjs-foo-base").data("chat_title", json[0]["chat_title"]);

    soundPlayOnMessageChat();
}

CHAT.notif_init = function(){
    $.notify.addStyle('foo', {
        html: 
        `
        <div>
            <div class='clearfix'>
                <div class='buttons'>
                    <i class="fas fa-times no"></i>
                </div>
                <div class='title' data-notify-html='title'></div>
            </div>
        </div>
            `
    });

    $(document).on('click', '.notifyjs-foo-base .no', function() {
      $(this).trigger('notify-hide');
    });

    $(document).on('click', '.notifyjs-foo-base', function() {
      let chat_id = $(this).data("chat_id");
      let chat_title = $(this).data("chat_title");
      $(this).trigger('notify-hide');
      changeChat(chat_id, chat_title);
      show_chat_container();
    });
}

function show_chat_container(){
    $("#chat_container").show();
    $("body").css("overflow-y", "hidden");
}
function hide_chat_container(){
    $("#chat_container").hide();
    $("body").css("overflow-y", "scroll");
}

function openChat(){
    show_chat_container();
    checkChatId();
    check_chat_site();
    load_chat_lists();
    toggle_link();
    chat_info_display("show");
    restore_chat_id().then((chat_data) => {
        changeChat(chat_data.id, chat_data.title);
    }, () => {
        //заглушка
    });
} 

function toggle_link(link = ""){
    const elem = $("#chat_header-link");
    elem.hide();
    if(link != ""){
        elem.show();
        elem.data("link", link);
    }
}

function restore_chat_id(){
    return new Promise((resolve, reject) => {
        let url = window.location.pathname.split("/");
        let id = url[url.length - 1];
        let i_module = url.indexOf("module");
        if(i_module !== -1){
            let module_search = false;
            switch(url[i_module + 1]){
                case "sales":
                    module_search = "sales";
                break;
                case "supply":
                    switch(url[i_module + 2]){
                        case "detail":
                            module_search = "supply";
                        break;
                        case "order_detail":
                            module_search = "supply_order";
                        break;
                    }
                break;
                case "storage":
                    switch(url[i_module + 2]){
                        case "assembly":
                            switch(url[i_module + 3]){
                                case "detail":
                                    module_search = "storage_assembly";
                                break;
                            }
                        break;
                    }
                break;
                case "boiler":
                    switch(url[i_module + 2]){
                        case "bid":
                            module_search = "boiler_bid";
                        break;
                    }
                break;
            }

            if(module_search){
                find_chat(module_search, id).then((chat_data) => {
                    resolve(chat_data);
                }, () => {
                    reject();
                });
            } else {
                reject();
            }
        } else {
            reject();
        }
    });
}

function closeChat(){
    hide_chat_container();
    change_page(0);
    change_list_page(0);
    toggleList();
    change_chat_id("");
    $("#chat_body-message").empty();
    $("#chat_header-title").html("Выберите чат");
}

function toggleList(type = false){
    //скрытие всех окон
    event.stopPropagation();
    
    const chat_list_users = $("#chat_list-users");
    const search = $("#chat_search_con");
    const chat_list = $("#chat_list");
    const chat_bars_toggle_container = $("#chat_bars_toggle_container");
    chat_list_users.fadeOut(() => {
        search.show();
        chat_list.show(); 
        chat_bars_toggle_container.show();
    });

    if(type){
        const display = $("#"+type).css("display");
        switch(display){
            case "none":
                switch(type){
                    case "chat_list-users":
                        if(checkChatId()){
                            chat_list.hide();
                            search.hide();
                            chat_bars_toggle_container.hide();

                            chat_list_users.fadeIn();
                            $("#chat_list-users-form").html('Загрузка списка...');
                            load_users_list(get_chat_id());
                        }
                    break;
                }
            break;
        }
    }
}

function chat_lists_html(value, act = "html"){
    const elem = $("#chat_list-elems");
    switch(act){
        case "html":
            elem.html(value);
        break;
        case "append":
            elem.append(value);
        break;
    }
    
}

function load_chat_lists(first = true){
    return new Promise((resolve, reject) => {
        if(first){
            chat_lists_html('<li>Загрузка чатов...</li>');
        }
        loadChatList().then((data)=>{
            if(first){
                chat_lists_html(data);
            } else {
                chat_lists_html(data, "append");
            }
            
            is_active_chat(get_chat_id());
            resolve();
        });
    });
}

function chatSearch(value){
    if(value.length > 2){
        find_and_load_chat_lists({
            title: value,
            uid: "",
        });
    } else {
        load_chat_lists()
    }
}

function find_and_load_chat_lists(data){
    return new Promise((resolve, reject) => {
        chat_lists_html('<li>Загрузка чатов...</li>');
        $.post("/ajax.php?do=find_and_load_chat_lists", data, function(result){
            chat_lists_html(result);
            resolve();
        });
    });
}

function change_chat_elem_list(id, selector, value){
    const elem = $("#chat_list_"+id);
    const con = $("#chat_list-elems");
    if(con.find(elem).length > 0){
        elem.find(selector).eq(0).html(value);
    }
}

CHAT.up_chat_list = up_chat_list;
function up_chat_list(id){
    const elem = $("#chat_list_"+id);
    const con = $("#chat_list-elems");
    if(con.find(elem).length > 0){
        con.prepend(elem.clone());
        elem.remove();
    } else {
        change_list_page(0);
        load_chat_lists(true);
    }
}

CHAT.update_last_message = update_last_message;
function update_last_message(data){
    change_chat_elem_list(data.chat_id, ".elem-list_date", data.date);
    change_chat_elem_list(data.chat_id, ".no_read_message", data.message);
}

CHAT.active_chat = active_chat;
function active_chat(chat_id){
    const elem = $("#chat_list_"+chat_id);
    const con = $("#chat_list-elems");
    if(con.find(elem).length > 0){
        const ac = "active_chat";
        con.children().removeClass(ac);
        elem.addClass(ac);
    }
}

function count_no_read_html(chat_id, value = null){
    const elem = $("#chat_list_"+chat_id).find(".count_no_read");
    const classActiveName = "count_no_read_active";
    if(value === null){
        return +elem.text();
    } else {
        if(value == 0){
            elem.html("");
        } else {
            elem.html(value);
        }
        if(value > 0){
            if(!elem.hasClass(classActiveName)){
                elem.addClass(classActiveName);
            }
        } else {
            if(elem.hasClass(classActiveName)){
                elem.removeClass(classActiveName);
            }
        }
    }
}

CHAT.add_count_no_read_message = add_count_no_read_message;
function add_count_no_read_message(chat_id, count = 1){
    const old_count = count_no_read_html(chat_id);
    const new_value = +old_count + +count;
    count_no_read_html(chat_id, new_value);
}

CHAT.read_chat_message_portal = read_chat_message_portal;
function read_chat_message_portal(chat_id){
    const count = 0;
    count_no_read_html(chat_id, count);
}

function show_smile_type(selector){
    $(".smile_container").hide();
    $(".sticker_container").hide();
    $(selector).show();
}

function change_title(){
    if(get_chat_id()){
        const elem = $("#chat_header-title");
        if(chat_title_status() == 0){
            chat_title_status(1);
            const title = elem.text();
            elem.data("title", title);
            const input_id = "change_chat_title_input"
            elem.html(`<input type="text" value="${title}" class="form-control" id="${input_id}">
            <div class="c_flex">
            <button class="btn btn-success fas fa-check btn-xs" onclick="change_title_btn('set');"></button>
            <button class="btn btn-danger fas fa-times btn-xs" onclick="change_title_btn('cancel');"></button>
            </div>`);
            $("#"+input_id).focus().select();
        }
    }
} 

function change_title_btn(act){
    event.stopPropagation();
    chat_title_status(0);
    const elem = $("#chat_header-title");
    const input = $("#change_chat_title_input");
    const chat_id = get_chat_id();
    let new_title = "";
    switch(act){
        case "set":
            new_title = input.val();
            if(chat_id){
                change_chat(chat_id, "title", new_title);
                change_chat_elem_list(chat_id, ".elem-list_title", new_title);
            }
        break;
        case "cancel":
            new_title = elem.data("title");
        break;
    }
    elem.html(new_title);
}

function chat_title_status(status = null){
    const elem = $("#chat_header-title");
    if(status === null){
        return elem.data("change");
    } else {
        elem.data("change", status);
    }
}

CHAT.update_all_no_read_message_count = update_all_no_read_message_count;
function update_all_no_read_message_count(){
    const elem = $("#no_read_call");
    get_all_no_read_message_count().then(count => {
        elem.html(count);
        if(count > 0){
            elem.show();
        } else {
            elem.hide();
        }
    });
}

CHAT.lazy_load_smiles = lazy_load_smiles;
function lazy_load_smiles(){
    $(".lazy_load_smiles").each((i, elem) => {
        const src = $(elem).data("src");
        $(elem).attr("src", src);
        $(elem).removeAttr("data-src");
    });
    $(".lazy_load_smiles").removeClass("lazy_load_smiles");
}

function chat_info_display(display = "hide"){
    const chat_info = $("#chat_info");
    const chat_main = $("#chat_main");
    const width = $(window).width();
    switch(display){
        case "hide":
            chat_info.hide();
            // chat_main.show();
            chat_main.css("display", "flex");
        break;
        case "show":
            if(width < 980){
                chat_main.hide();
            }
            chat_info.show();
        break;
    }
}

async function openChatId(chat_id){
    const data = await get_chat_data(chat_id);
    changeChat(data.id, data.title); 
    toggle_link(data.link);
    chat_title_status(0);
}