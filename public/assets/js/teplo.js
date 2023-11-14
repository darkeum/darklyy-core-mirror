function teploForm(aThis){	
		var myValues = $(".teplo-grid input[name='file[]']").map(function () {
		return this.value;
	}).get();
	
	const data = {
		t_gre_temper_in: $('.teplo-grid input[name=t_gre_temper_in]').val(),
        t_gre_temper_out: $('.teplo-grid input[name=t_gre_temper_out]').val(),
        t_gre_mass: $('.teplo-grid input[name=t_gre_mass]').val(),
        t_gre_mvst: $('.teplo-grid input[name=t_gre_mvst]').val(),
        t_gre_mvst_2: $('.teplo-grid input[name=t_gre_mvst_2]').val(),
        t_nagr_temper_out: $('.teplo-grid input[name=t_nagr_temper_out]').val(),
		t_nagr_temper_in: $('.teplo-grid input[name=t_nagr_temper_in]').val(),
        t_nagr_mass: $('.teplo-grid input[name=t_nagr_mass]').val(),
        t_nagr_mvst: $('.teplo-grid input[name=t_nagr_mvst]').val(),
        t_nagr_mvst_2: $('.teplo-grid input[name=t_nagr_mvst_2]').val(),
        t_typesr: $('.teplo-grid select[name=t_typesr] option:selected').text(),
        t_prim: $('.teplo-grid select[name=t_prim] option:selected').text(),
        t_typesreda: $('.teplo-grid select[name=t_typesreda] option:selected').text(),
        t_edizm: $('.teplo-grid select[name=t_edizm] option:selected').text(),
        t_edizm_2: $('.teplo-grid select[name=t_edizm_2] option:selected').text(),
        t_edizm_3: $('.teplo-grid select[name=t_edizm_3] option:selected').text(),
        t_raschdavl: $('.teplo-grid select[name=t_raschdavl] option:selected').text(),
        t_nagr: $('.teplo-grid input[name=t_nagr]').val(),
        t_organizaciya: $('.teplo-grid input[name=t_organizaciya]').val(),
        t_inn: $('.teplo-grid input[name=t_inn]').val(),
        t_name: $('.teplo-grid input[name=t_name]').val(),
        t_mail: $('.teplo-grid input[name=t_mail]').val(),
        t_phone: $('.teplo-grid input[name=t_phone]').val(),
        t_obect: $('.teplo-grid input[name=t_obect]').val(),
        t_oid: $('.teplo-grid select[name=oid]').val(),
        object_id: $('.teplo-grid select[name=object_id]').val(),
        t_zapas: $('input[name=t_zapas]').val(),
        t_zapas_2: $('input[name=t_zapas_2]').val(),
        t_rashod: $('input[name=t_rashod]').val(),
        t_t1: $('input[name=t_t1]').val(),
        t_t2: $('input[name=t_t2]').val(),
        t_nagr_1: $('input[name=t_nagr_1]').val(),
        t_nagr_2: $('input[name=t_nagr_2]').val(),
        t_nagr_22: $('input[name=t_nagr_22]').val(),
        t_nagr_auto: (($('#t_nagr_auto').is(':checked')) ? $('input[name=t_nagr_auto]').val() : '0'),
        t_type: $('input[name="t_type"]:checked').val(),
        t_flan: (($('#t_flan').is(':checked')) ? $('input[name=t_flan]').val() : '0'),
        t_optimizator: (($('#t_optimizator').is(':checked')) ? $('input[name=t_optimizator]').val() : '0'),
        t_file: myValues,  
        t_mess: $('.teplo-grid textarea[name=t_mess]').val(),
        type: 'hand'
	};
	let x1 = (data.t_gre_temper_in != '')  ? 1 : 0;
	let x2 = (data.t_gre_temper_out != '')  ? 1 : 0;
	let x3 = (data.t_nagr_temper_out != '')  ? 1 : 0;
	let x4 = (data.t_nagr_temper_in != '')  ? 1 : 0;
	let x5 = (data.t_gre_mass != '')  ? 1 : 0;
	let x6 = (data.t_nagr_mass != '')  ? 1 : 0;
	let x7 = (data.t_nagr != '')  ? 1 : 0;
	let check = true;
	let error = {};
	
	
	let y1 = data.t_gre_temper_in;
	let y2 = data.t_gre_temper_out;
	let y3 = data.t_nagr_temper_out;
	let y4 = data.t_nagr_temper_in;
	
	//проверка на значение	
	/*
	if(!(((x1+x2+x5+x7 == 3)&&(x3+x4+x6 == 2))||((x3+x4+x6+x7 == 3)&&(x1+x2+x5 == 2))))
	{
		if((x1+x2+x5+x7 > 3)||(x3+x4+x6+x7 > 3)||((x1+x2+x5+x7 == 3)&&(x3+x4+x6 > 2))||((x3+x4+x6+x7 == 3)&&(x1+x2+x5 > 2)))
		{
			check = false;
			error.main = 'Не соблюдено уравнение теплового баланса';	
		}
		else
		{
			check = false;
			error.main = 'Заполните все необходимы поля';
		}
	}
	else
	{
		if(((+data.t_gre_mass < 0.001)||(+data.t_gre_mass > 1)||isNaN(+data.t_gre_mass))&&(data.t_gre_mass != '')){
			check = false;
			error.t_gre_mass = 'Неверное значение массового расхода на подаче';
		}	
		if(((+data.t_gre_mvst < 0.01)||(+data.t_gre_mvst > 10)||isNaN(+data.t_gre_mvst))&&(data.t_gre_mvst != '')){
			check = false;
			error.t_gre_mvst = 'Неверное значение потери напора на подаче';
		}
		if(((+data.t_nagr_mass < 0.001)||(+data.t_nagr_mass > 1)||isNaN(+data.t_nagr_mass))&&(data.t_nagr_mass != '')){
			check = false;
			error.t_nagr_mass = 'Неверное значение массового расхода на обратке';
		}
		if(((+data.t_nagr_mvst < 0.01)||(+data.t_nagr_mvst > 10)||isNaN(+data.t_nagr_mvst))&&(data.t_nagr_mvst != '')){
			check = false;
			error.t_nagr_mvst = 'Неверное значение потери напора на обратке';
		}
		
		if(!(y1>=(y3+5)))
		{
			check = false;
			error.t_nagr_mvst = 'Температура на входе в ПТО должна быть больше, чем на выходе (min 5°C)';
		}
		if(!(y2>=(y4+5)))
		{
			check = false;
			error.t_nagr_mvst = 'Температура на выходе в ПТО должна быть больше, чем на входе (min 5°C)';
		}		
		if(!(y1>y2))
		{
			check = false;
			error.t_nagr_mvst = 'Неверно заполнены температуры на греющей стороне';
		}
		if(!(y4>y3))
		{
			check = false;
			error.t_nagr_mvst = 'Неверно заполнены температуры на нагреваемой стороне';
		}
	}*/
	
	console.log(check);
	console.log(data);
	console.log(+data.t_nagr_mass);
	
	if(!check)
	{	
		let = errorText = '';
		$('#errorBlock').css("display", "block");
		for (key in error) {
			errorText += error[key]+'<br>';
		}
		$('#errorText').html(errorText);
		return;	
	}
	else
	{
		$('#errorBlock').css("display", "none");	
		
	}

	var tMessage = $(aThis).parents('.block_cont');
	$('.progressWizard').css("display", "flex");
	
	//$(aThis).remove();
	//tMessage.after('<div class="messageOk mt-3"><i class="fas fa-spinner fa-spin"></i> Запрос выполняется, ожидайте!</div>');
	f();
    $.post(
        "/project/calculation/heat/send", data,
        function (data){
			var result = $.parseJSON(data);
			if (result.type == 'success')
			{
				var tMessage = $('.messageOk');
				$('.progressWizard').css("display", "none");	
				$('#rUrl').attr("href", "https://atri-energo.ru/profile/request/view/"+result.requestID);				
				if(result.typeRequst == 'hand')
				{
					$('#itsOkHand').css("display", "flex");
				}
				else
				{
					$('#itsOk').css("display", "flex");
				}
            }
			else
			{
				$('.progressWizard').css("display", "none");	
				$('#errorBlock').css("display", "block");
				$('#errorText').html(result.error);
			}
        }
    );

}



var i = 0, howManyTimes = 4;
function f() {
    next();
    i++;
    if( i < howManyTimes ){
        setTimeout( f, 3050 );
    }
}



	let step = 'step1';

const step1 = document.getElementById('step1');
const step2 = document.getElementById('step2');
const step3 = document.getElementById('step3');
const step4 = document.getElementById('step4');

function next() {
  if (step === 'step1') {
    step = 'step2';
    step1.classList.remove("is-active");
    step1.classList.add("is-complete");
    step2.classList.add("is-active");

  } else if (step === 'step2') {
    step = 'step3';
    step2.classList.remove("is-active");
    step2.classList.add("is-complete");
    step3.classList.add("is-active");

  } else if (step === 'step3') {
    step = 'step4d';
    step3.classList.remove("is-active");
    step3.classList.add("is-complete");
    step4.classList.add("is-active");

  } else if (step === 'step4d') {
    step = 'complete';
    step4.classList.remove("is-active");
    step4.classList.add("is-complete");

  } else if (step === 'complete') {
    step = 'step1';
    step4.classList.remove("is-complete");
    step3.classList.remove("is-complete");
    step2.classList.remove("is-complete");
    step1.classList.remove("is-complete");
    step1.classList.add("is-active");
  }
}

