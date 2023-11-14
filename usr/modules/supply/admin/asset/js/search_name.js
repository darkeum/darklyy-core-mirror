function search_name(){
    let search = $("#searchVal").val();
    $.post("/administration/module/supply/search_name_list", {search: search}, function(data){
        $("#result").html(data);
    });
}