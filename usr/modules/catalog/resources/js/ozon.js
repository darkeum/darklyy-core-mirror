function createOzon(catalogID)
{
    $.ajax({
        url: '/administration/module/markets/ozon/createCard',         /* Куда пойдет запрос */
        method: 'post',                     /* Метод передачи (post или get) */
        dataType: 'json',                   /* Тип данных в ответе (xml, json, script, html). */
        data: {catalogID: catalogID},       /* Параметры передаваемые в запросе. */
        success: function(data){            /* функция которая будет выполнена после успешного запроса.  */
            alert(data.message);            /* В переменной data содержится ответ от index.php. */
        }
    });
}