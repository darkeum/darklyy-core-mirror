const CatalogGroupsDiscount = {
    add(request){
        return new Promise((resolve, reject) => {
            $.post("/administration/module/catalog_v2/groups_discount/add", request, function(id){
                resolve(id);
            });
        });
    },
    delete(id){
        return new Promise((resolve, reject) => {
            $.post("/administration/module/catalog_v2/groups_discount/delete/"+id, function(){
                resolve();
            });
        });
    },
    update(id, request){
        return new Promise((resolve, reject) => {
            $.post("/administration/module/catalog_v2/groups_discount/update/"+id, request, function(){
                resolve();
            })
        })
    }
}