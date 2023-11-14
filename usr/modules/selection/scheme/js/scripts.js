function vint1(aThis){

    if($(aThis).prop("checked")){


        $('.vint1').addClass('active-vint');


    }else{


        $('.vint1').removeClass('active-vint');


    }

}

function vint2(aThis){

    if($(aThis).prop("checked")){


        $('.vint3').addClass('active-vint');


    }else{


        $('.vint3').removeClass('active-vint');


    }

}

function shemaDoc (a) {

    if(a == 1){

        $('#ispolDoc, #b1, #b2, #b3').css('display','none');
        $('#shemaUpravleniya').css('display','block');

    }else if(a == 2){

        $('#shemaUpravleniya, #b1, #b2, #b3').css('display','none');
        $('#ispolDoc').css('display','block');

    }else if(a == 3){

        $('#ispolDoc, #shemaUpravleniya, #b3, #b2').css('display','none');
        $('#b1').css('display','block');

    }else if(a == 4){

        $('#ispolDoc, #shemaUpravleniya, #b1, #b3').css('display','none');
        $('#b2').css('display','block');

    }else if(a == 5){

        $('#ispolDoc, #shemaUpravleniya, #b1, #b2').css('display','none');
        $('#b3').css('display','block');

    }
	
	

}