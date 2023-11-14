function append_documentation(id){
    location.href = '/administration/module/documentation/append/'+id;
}
function edit_documentation(id){
    location.href = '/administration/module/documentation/edit/'+id;
}
function change_fields(id, field, value){
    $.post("/administration/module/documentation/moderation", {id, field, value}, function(){
        location.reload();
    });
}
function pre_send_comment(id){
    let comment = $("#comments_textarea").val();
    $("#comments_textarea").val("");
    if(comment.replace(/ /g, "").length > 0){
        send_comment(id, comment);
    }
}
function send_comment(id, comment){
    $.post("/administration/module/documentation/comment_set", {id, comment}, function(){
        get_comment(id);
    });
}
function get_comment(id){
    $.post("/administration/module/documentation/comment_get", {id}, function(data){
        $("#comments").html(data);
    })
}
function documentation_search(This){
    let value = $(This).val();
    if(value.length > 2){
        $.post("/administration/module/documentation/search", {search: value}, function(data){
            $("#header_search-container").html(data);
            $("#header_search-container").slideDown();
        });
    } else {
        search_container_hide();
    }
}
function search_container_hide(){
    $("#header_search-container").slideUp();
}
$(document).on("click", function(){
    search_container_hide();
});