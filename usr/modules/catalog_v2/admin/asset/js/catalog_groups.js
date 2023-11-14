const CatalogGroups = {
    add(request){
        return new Promise((resolve, reject) => {
            $.post("/administration/module/catalog_v2/groups/add", request, function(id){
                resolve(id);
            });
        });
    },
    delete(id){
        return new Promise((resolve, reject) => {
            $.post("/administration/module/catalog_v2/groups/delete/"+id, function(){
                resolve();
            });
        });
    },
    update(id, request){
        return new Promise((resolve, reject) => {
            $.post("/administration/module/catalog_v2/groups/update/"+id, request, function(){
                resolve();
            })
        })
    }
};