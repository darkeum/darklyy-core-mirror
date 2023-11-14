//<script src="/usr/other/sub/relations/asset/js/relations.js"></script>


class Relations{
    add(params) {
        return new Promise((resolve, reject) => {
            $.post("/ajax.php?do=relationsAddAjax", params, function(){
                resolve();
            });
        });
    }
    delete(params){
        return new Promise((resolve, reject) => {
            $.post("/ajax.php?do=relationsDeleteAjax", params, function(){
                resolve();
            });
        });
    }
    find(params){
        return new Promise((resolve, reject) => {
            $.post("/ajax.php?do=relationsFindAjax", params, function(){
                resolve();
            });
        });
    }
}