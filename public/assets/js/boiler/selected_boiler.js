$(function(){
   
});

function univ_save(url){
    const form = $("#mainForm").serialize();
    $("#grid_container_load").show();
    $.post(url, form, function(id){
        opener.openData(id).then(() => {
            window.close();
        });
    });
}

function save_tov(){
    univ_save("/profile/boiler/add_item_save");
}

function save_tov_to_data(){
    univ_save("/profile/boiler/add_item_to_data_save");
}

