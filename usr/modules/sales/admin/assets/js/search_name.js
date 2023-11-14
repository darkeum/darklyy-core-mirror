function search_name(){
    let search = $("#searchVal").val();
    $.post("/administration/module/sales/search_name_list", {search: search}, function(data){
        $("#result").html(data);
    });
}