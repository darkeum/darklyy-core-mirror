function copy_spec(spec){
    navigator.clipboard.writeText(spec)
  .then(() => {
    swal("Успешно!", "Спец. слово успешно скопирвоано в буфер обмена", "success");
  })
  .catch(err => {
    swal("Ошибка!", "Непредвиденная ошибка: "+err, "error");
  });
}