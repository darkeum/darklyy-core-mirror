async function update_body_lw(){
    const lw = new LW;
    lw.on();
    await update_body();
    lw.off();
}

function m_delete(id){
    swal({
        title: "Вы уверены?",
        text: "Удалить текущую позицию",
        icon: "warning",
        buttons: true,
        dangerMode: true,
    })
    .then(async (willDelete) => {
        if (willDelete) {
            const lw = new LW;
            lw.on();
            await delete_item(id);
            await update_body();
            lw.off();
        }
    });
}

