class Pump{
    static changeItem = 'change_item';
    add(request){
        return new Promise((resolve, reject) => {
            $.post("/administration/module/catalog_v2/pump/add", request, function(id){
                resolve(id);
            });
        });
    }
    save(request){
        return new Promise((resolve, reject) => {
            $.post("/administration/module/catalog_v2/pump/save", request, function(error){
                if(error == ""){
                    resolve();
                } else {
                    reject(error);
                }
            });
        });
    }
    update(id, request){
        return new Promise((resolve, reject) => {
            $.post("/administration/module/catalog_v2/pump/update/"+id, request, function(error){
                if(error == ""){
                    resolve();
                } else {
                    reject(error);
                }
            });
        });
    }
    delete(id){
        return new Promise((resolve, reject) => {
            $.post("/administration/module/catalog_v2/pump/delete/"+id, function(){
                resolve();
            });
        });
    }
    del_modal(id){
        return new Promise((resolve, reject) => {
            swal({
                title: "Вы уверены?",
                text: "После удаления вы не сможете восстановить эту позицию!",
                icon: "warning",
                buttons: true,
                dangerMode: true,
              })
              .then((willDelete) => {
                if (willDelete) {
                    const lw = new LW;
                    lw.on();
                    this.delete(id).then(() => {
                        lw.off();
                        resolve();
                    })
                }
              });
        })
    }
}