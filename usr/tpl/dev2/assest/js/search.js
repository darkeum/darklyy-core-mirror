$(function(){
    
//Живой поиск
let flag = true;
$('.who').bind("keydown input click change cut copy paste", function() {
    if(this.value.length >= 2){
	if(flag){
		flag = false;
        $.ajax({
            type: 'post',
            url: "https://atri-energo.ru/ajax.php?do=search", //Путь к обработчику
            data: {'referal':this.value},
            response: 'text',
            success: function(data){

                $(".search_result").html('<div class="dv-result">' + data + '</div>').slideDown(); //Выводим полученые данные в списке
                flag = true;

                if(!data){

                    $(".search_result").html('');

                }

           }
       });
	}
    }
});

/*
$(".who").on("blur", function(){
	$('.search_result').slideUp();
});
*/
	
//При выборе результата поиска, прячем список и заносим выбранный результат в input
$(".main-container").on("click", function(){
    s_user = $(this).text();
    //$(".who").val(s_user).attr('disabled', 'disabled'); //деактивируем input, если нужно
    $(".search_result").slideUp();
});

});

