class Model{
    url_add = false;
    url_update = false;
    url_delete = false;
    url(){
        return false;
    }
    constructor(){
        const url = this.url();
        this.url_add = this.url_add ? this.url_add : url+"/add";
        this.url_update = this.url_update ? this.url_update : url+"/update/";
        this.url_delete = this.url_delete ? this.url_delete : url+"/delete/";
    }
    add(request){
        return new Promise((resolve, reject) => {
            $.post(this.url_add, request, function(data){
                resolve(data);
            });
        });
    }
    update(id, request){
        return new Promise((resolve, reject) => {
            $.post(this.url_update+id, request, function(data){
                resolve(data);
            });
        });
    }
    delete(id){
        return new Promise((resolve, reject) => {
            $.post(this.url_delete+id, request, function(data){
                resolve(data);
            });
        });
    }
}