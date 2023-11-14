class Command{
    delete(id){
        return new Promise((resolve, reject) => {
            $.post("/administration/command/delete/"+id, function(){
                resolve();
            });
        });
    }
}