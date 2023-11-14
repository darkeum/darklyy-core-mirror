async function end(id, status, comment = null){
    const form = new FormData;
    form.append("status", status);
    form.append("comment", comment);
    return await ( await fetch(`/administration/module/control/end/${id}`, {
        method: "POST",
        body: form,
    }) ).json();
}