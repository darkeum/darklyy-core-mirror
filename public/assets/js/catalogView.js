function viewProsmotr(aThis){

    $.get(
        $(aThis).attr('data-src'),

        function onAjaxSuccess(data){

            var c = $('<div class="box-modal box-modal-b-view" />');
            c.html(data);
            c.prepend('<div class="box-modal_close arcticmodal-close">X</div>');

            $.arcticmodal({
                content: c
            });

            console.log("!!!");
        }
    );
}

function orderVkladka(aThis, b){
    return new Promise((resolve, reject) => {
        const cl_active = 'active';
        $('.nav-tabs li.active').removeClass(cl_active);
        $(aThis).parent().addClass(cl_active);
        $('.tab-pane').hide(300);
    
        switch(b){
            case 1:
                this_view($('#tab-description'));
            break;
            case 2:
                this_view($('#tab-additional_information'));
            break;
            case 3:
                this_view($('#tab-reviews'));
            break;
            case 4:
                this_view($('#tab-graphs'));
            break;
        }
    
        function this_view(elem){
            elem.delay(350).show(300, function(){
                resolve();
            });
        }
    });
}