class InputLoadFtp{
    constructor(request){
        this.selector = request.selector;
        this.url = request.url;
    }
    init(){
        const This = this;
        $(this.selector).on("change", async function(){
            const item = $(this);
            const input = item.data("input");
            const formData = new FormData();
            formData.append('file', item[0].files[0]);
            const lw = new LW;
            lw.on();
            await This.load_file(formData).then(data => {
                $(input).val(data.attach_id);
            });
            lw.off();
        });
    }
    load_file(data){
        return new Promise((resolve, reject) => {
            $.ajax({
                type: 'POST',
                url: this.url,
                data,
                processData: false,
                contentType: false,
                dataType: "json",
            }).done(data => {
                resolve(data);
            });
        });
    }
}