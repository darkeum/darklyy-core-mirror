$(function(){
    $(".pto_select2").select2({
        placeholder: "Выберите значение"
    });
    $pto.pto_class_name = PTOView;

    const ilf = new InputLoadFtp({selector: ".input_load_ftp", url: `/administration/module/pto/load_file`});
    ilf.init();
});