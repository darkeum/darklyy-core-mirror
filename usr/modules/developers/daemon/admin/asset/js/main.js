formProcess.addEventListener('submit', (event) => {
    event.preventDefault();
    addDaemonInTable();
});

function addDaemonInTable() {
    const titleField = document.getElementById('titleField');
    const commandField = document.getElementById('commandField');
    const autostartField = document.getElementById('autostartField');
    const commentField = document.getElementById('commentField');

    const url = "/ajax.php?do=addDaemonInTable";
    const formData = new FormData();
    formData.append('title', titleField.value);
    formData.append('command', commandField.value);
    formData.append('autostart', autostartField.value);
    formData.append('comment', commentField.value);

    fetch(url, {
        method: "POST",
        body: formData,
    }).then(()=>{
        $('#modalAddProcess').modal('hide');
        reload_grid();
    });
};

async function startDaemon(id) {   
    await startDaemon_(id);
    reload_grid();
};

async function startDaemon_(id) {   
    const url = "/ajax.php?do=startDaemon";
    await send(id, url);
};

async function stopDaemon(id) {
    await stopDaemon_(id);
    reload_grid();
};
 
async function stopDaemon_(id) {
    const url = "/ajax.php?do=stopDaemon";
    await send(id, url);
};

async function send(id, url) {
    const formData = new FormData();
    formData.append('id', id);    
    await fetch(url, {
        method: "POST",
        body: formData,
    });
}

async function reloadDaemon(id){
    await stopDaemon_(id);
    await startDaemon_(id);
    reload_grid();
}

function reload_grid(){
    $("#jqDaemon").trigger("reloadGrid");
}