$("#category").select2({
    placeholder: "Выберите категорию",
    maximumSelectionLength: 2,
    language: "ru",
});

$("#title").on("change", function() {
    $("#sub").prop("disabled", false);
});