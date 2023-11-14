function confirm_view(id){
    $.post("/administration/module/catalog_v2/change_log_confirm/"+id, function(){
        $('#jqChangeLog').trigger('reloadGrid');
    });
}