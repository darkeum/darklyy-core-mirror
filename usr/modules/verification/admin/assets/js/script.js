$("#comment").editable({
    title: 'Введите комментарий',
    rows: 10,
    url: "/ajax.php?do=saveCommentsVer",
    emptytext: "Отсутствует",
});