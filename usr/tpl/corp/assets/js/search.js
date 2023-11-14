$(function(){
    
//Живой поиск
$('.who').bind("change keyup input click", function() {
    if(this.value.length >= 2){
        $.ajax({
            type: 'post',
            url: "https://atri-energo.ru/ajax.php?do=search_admin", //Путь к обработчику
            data: {'referal':this.value},
            response: 'text',
            success: function(data){
                $(".search_result").html(data).fadeIn(); //Выводим полученые данные в списке
           }
       })
    }
})



})

