function fast_view(This){
    const src =  $(This).data("src");
    $("#fast_view_modal_frame").children().remove();
    $("#fast_view_modal_frame").append(`<iframe src="${src}" frameborder="0" style="width: 100%; height: 80vh;"></iframe>`);
    $("#fast_view_modal").modal("show");
}