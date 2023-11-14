function union_modal(){
    $("#union_modal_body_table").html("");
    $("#union_modal").modal("show");
}
function union_modal_body(data){
    $("#union_modal_body_table").html(data);
}
function union_s(){
    $("#union_modal_btn").prop("disabled", false);
    union_modal();
    load_list_union().then(data => {
        const str = parse_data_union(data);
        union_modal_body(str);
        if(data.length == 0){
            $("#union_modal_btn").prop("disabled", true);
        }
    });
}