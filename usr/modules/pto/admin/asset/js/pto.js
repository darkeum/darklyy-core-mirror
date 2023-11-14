$pto = {
    form: $("#pto_form"),
    pto_class_name: null,
    add(){
        const pto_class_name = this.pto_class_name;
        this.form.on("submit", async function(e){
            e.preventDefault();
            const form = $(this);
            const request = form.serialize();
            const lw = new LW;
            lw.on();
            const pto = new pto_class_name;
            await pto.add(request).then(json => {
                try{
                    const data = JSON.parse(json);
                    form[0].reset();
                    $(".pto_select2").val(null).trigger("change");
                    swal("Успешно!", "Создано", "success");
                } catch(e){
                    swal("Ошибка добавления!", `${e} (${json})`, "error");
                }
            });
            lw.off();
        });
    },
    update(){
        const pto_class_name = this.pto_class_name;
        this.form.on("submit", async function(e){
            e.preventDefault();
            const form = $(this);
            const id = form.data("id");
            const request = form.serialize();
            const lw = new LW;
            lw.on();
            const pto = new pto_class_name;
            await pto.update(id, request).then(data => {
                if(data == ""){
                    swal("Успешно!", "Обновлено", "success");
                } else {
                    swal("Ошибка обновления!", `${data}`, "error");
                }
            });
            lw.off();
        });
    },
}