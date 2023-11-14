function load_ftp(selector){
    const id = $(selector).val();
    if(id == ""){
        swal("Внимание!", "файл не указан!", "warning");
    } else {
        location.href = `/administration/module/pto/load_ftp/${id}`;
    }
}