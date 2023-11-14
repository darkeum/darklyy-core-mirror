class CatalogGroups{
    constructor(name){
        this.name = name;
    }
    add(request){
        return new Promise((resolve, reject) => {
            $.post(`/administration/module/catalog_v2/${this.name}/add`, request, function(id){
                resolve(id);
            });
        });
    }
    delete(id){
        return new Promise((resolve, reject) => {
            $.post(`/administration/module/catalog_v2/${this.name}/delete/${id}`, function(){
                resolve();
            });
        });
    }
    update(id, request){
        return new Promise((resolve, reject) => {
            $.post(`/administration/module/catalog_v2/${this.name}/update/${id}`, request, function(){
                resolve();
            })
        })
    }
}

const CatalogGroupsOrganizations = new CatalogGroups("groups_organizations");
const CatalogGroupsTemplate = new CatalogGroups("groups_template");