let statusUpdate = (id, status, id_detail) => {
    let json = {};
    json[id] = status;
    json = JSON.stringify(json);
    window.Livewire.emit('update', {json, id_detail})
}

let statusUpdateAll = (id_detail) => {
    let checks = $('.checks');
    let status = $('#act').val();
    let json = {};
    for(let i=0; i < checks.length; i++){
        if($(checks[i]).prop('checked')){
            let tr = $(checks[i]).parent().parent().parent();
            let id = $(tr).data('id');
            json[id] = status;
        }
    }
    json = JSON.stringify(json);
    window.Livewire.emit('update', {json, id_detail})
}