function getFastBar(){
    return $("#fastBar");
}
const fastBar = getFastBar();
$(function(){
    //open bar
    $(document).on("click", "#slideBar", function(){
        const fastBar = getFastBar();
        if(fastBar.data("fullvisable") == 0){
            fastBar.data("height", fastBar.height());
            fastBar.animate({
                height: '80%',
            }, 800);
            fastBar.data("fullvisable", 1);
            $("#slideBarArrow").removeClass("fa-arrow-up");
            $("#slideBarArrow").addClass("fa-arrow-down");
        } else {
            fastBar.animate({
                height: fastBar.data("height"),
            }, 800);
            fastBar.data("fullvisable", 0);
            $("#slideBarArrow").removeClass("fa-arrow-down");
            $("#slideBarArrow").addClass("fa-arrow-up");
        }
    });
    /*
    let ctrlKey = false; //17
    let cKey = false; //67
    let vKey = false; //86
    $(document).on("keydown", function(e){
        //console.log(e.keyCode);
        if(e.keyCode == 17) ctrlKey = true;
        if(e.keyCode == 67) cKey = true;
        if(e.keyCode == 86) vKey = true;
    });

    $(document).on("keyup", function(e){
        if(ctrlKey && cKey){
            saveToLocalStorage();
        }
        if(ctrlKey && vKey){
            let data = localStorage.getItem("saveBufer");
            fastBarUniversalSave(data);
        }
        ctrlKey = false;
        cKey = false;
        vKey = false;
    });
    */
    function saveToLocalStorage(){
        const fastBar = getFastBar();
        let checks = $(".checks");
        let module = fastBar.data("module");
        let status = fastBar.data("status");
        let save = {"module_copy":module, "status":status, "save":[]};
        for(let i=0;i<checks.length;i++){
            if($(checks[i]).prop("checked")){
                save["save"].push({
                    id: +$(checks[i]).val(), 
                    cid: +$(checks[i]).data("cid"),
                });
            }
        }
        save = JSON.stringify(save);
        localStorage.removeItem("saveBufer");
        localStorage.setItem("saveBufer", save);
        alert(save);
    }

    function fastBarUniversalSave(data){
        const fastBar = getFastBar();
        let json = JSON.parse(data);
        let module = fastBar.data("module");
        json["id"] = fastBar.data("id");
        json["module_insert"] = module;
        data = JSON.stringify(json);
        $.post("/ajax.php?do=fastBarUniversalSave",{data:data}, function(){
            const func_name = fastBar.data("update_func");
            if(func_name === undefined){
                $.post(fastBar.data("update_url"), function(res){
                    $("#"+fastBar.data("update")).html(res);
                });
            } else {
                func_name();
            }
        });
    }

    async function clipboard(data){
        const fastBar = getFastBar();
        const json = JSON.parse(data);
        const module = fastBar.data("module");
        json["id"] = fastBar.data("id");
        json["module_insert"] = module;
        data = JSON.stringify(json);
        const lw = new LW;
        lw.on();
        const clipboard = fastBar.data("clipboard");
        if(clipboard === undefined){
            $.post("/ajax.php?do=clipboard", {data})
            .done(async function(error){
                if(error != ""){
                    swal("Ошибка!", `Отправьте скриншот ошибки программисту для исправления (${error})`, "error");
                }
                const func_name = fastBar.data("update_func");
                if(func_name === undefined){
                    $.post(fastBar.data("update_url"), function(res){
                        $("#"+fastBar.data("update")).html(res);
                        lw.off();
                    });
                } else {
                    await window[ func_name ]();
                    lw.off();
                }
            })
            .fail(function(xhr, status, error) {
                swal("Ошибка!", `Отправьте скриншот ошибки программисту для исправления (${error})`, "error");
                lw.off();
            });
        } else {
            await window[ clipboard ](data, {
                link: "/ajax.php?do=clipboard_data",
            });
            lw.off();
        }
    }

    function getMenu(){
        return $("#clipboardMenu");
    }

    function getActiveClass(){
        return "context-menu--active";
    }

    function get_paste_dis_class(){
        return "paste--no_active";
    }

    function get_context_menu__item_paste(){
        return $("#context-menu__item-paste");
    }

    let menuState = 0;

    $(document).on("click", "#context-menu__item-copy", function(){
        saveToLocalStorage();
    });

    function toggleMenuOn() {
        if ( menuState !== 1 ) {
            menuState = 1;
            getMenu().addClass(getActiveClass());
            const copyDisabled = copy_disabled();
            if(!copyDisabled){
                get_context_menu__item_paste().on("click", function(){
                    clipboard(localStorage.getItem('saveBufer'));
                });
            } else {
                get_context_menu__item_paste().addClass(get_paste_dis_class());
            }
        }
    }

    function toggleMenuOff() {
        if ( menuState !== 0 ) {
            menuState = 0;
            getMenu().removeClass(getActiveClass());
            get_context_menu__item_paste().removeClass(get_paste_dis_class())
            get_context_menu__item_paste().off("click");
        }
    }

    function get_container(){
        const fastBar = getFastBar();
        return fastBar.data("container");
    }

    $(document).on("contextmenu", get_container()+" tr", function(e){
        const cmd = localStorage.getItem("cmd");
        if(cmd !== "admin"){
            e.preventDefault();
            toggleMenuOn();
            positionMenu(e);
        }
    });

    $(document).on("click", function(){
        toggleMenuOff();
    });

    function getPosition(e) {
        return {
            x: e.pageX ?? 0,
            y: e.pageY ?? 0,
        }
    }

    function positionMenu(e) {
        menuPosition = getPosition(e);

        let clickCoordsX = menuPosition.x;
        let clickCoordsY = menuPosition.y;

        let menuWidth = getMenu().outerWidth() + 4;
        let menuHeight = getMenu().outerHeight() + 4;

        let windowWidth = $(document).innerWidth();
        let windowHeight = $(document).innerHeight();


        if ( (windowWidth - clickCoordsX) < menuWidth ) {
            getMenu().offset({left: (windowWidth - menuWidth)});
            } else {
            getMenu().offset({left: clickCoordsX});
        }

        if ( (windowHeight - clickCoordsY) < menuHeight ) {
            getMenu().offset({top: (windowHeight - menuHeight)});
            } else {
            getMenu().offset({top: clickCoordsY});
        }
    }

    function inputStyleCmd(elem, status){
        switch(status){
            case "success":
                $(elem).animate({
                    backgroundColor: "green",
                }, 500);
                $(elem).animate({
                    backgroundColor: "white",
                }, 500);
            break;
            case "error":
                $(elem).animate({
                    backgroundColor: "red",
                }, 500);
                $(elem).animate({
                    backgroundColor: "white",
                }, 500);
            break;
        }
    }

    $(document).on("keyup", ".cmd", function(e){
        if(e.keyCode === 13){
            let This = $(this);
            let value = $(This).val().trim();
            switch(value){
                default:
                    inputStyleCmd(This, 'error');
                break;
                case "admin":
                    inputStyleCmd(This, 'success');
                    localStorage.setItem("cmd", "admin");
                break;
                case "user":
                    inputStyleCmd(This, 'success');
                    localStorage.removeItem("cmd");
                break;
                case "mine":
                    inputStyleCmd(This, 'success');
                    mineGame();
                break;
            }
            $(This).val(null);
        }
    });
});

function copy_disabled(value = null){
    const fastBar = getFastBar();
    const copyDisabled = fastBar.data("copy_disabled");
    if(value === null){
        return copyDisabled;
    } else {
        fastBar.data("copy_disabled", value);
    }
}

function check_copy_disabled(){
    const copyDisabled = copy_disabled();
    return copyDisabled;
}