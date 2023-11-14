<style>
	.img-container {
		background-size: cover;
		width: 100%;
		position: relative;
	}
	
	.regForm {
		display: inline-block;
		position: absolute;
		left: 0%;
		width: 100%;
		z-index: 2;
		text-align: center;
		background: none;
	}
	
	.closeBtn {
		display: inline-block;
		position: absolute;
		top: 5px;
		right: 5px;
		z-index: 2;
		background: none;
		border: 1px solid;
		font-size: 20px;
		font-weight: bold;
		color: #fff;
	}
	
	.box input[type="text"],
	.box input[type="email"],
	.box input[type="password"] {
		border: 0;
		background: none;
		display: block;
		margin: 20px auto;
		text-align: center;
		border: 2px solid #ffffff;
		padding: 10px 10px;
		width: 250px;
		outline: none;
		color: white;
		border-radius: 24px;
		transition: 0.25s
	}
	
	.box h1 {
		color: white;
		text-transform: uppercase;
		font-weight: 500
	}
	
	.box input[type="text"]:focus,
	.box input[type="email"]:focus,
	.box input[type="password"]:focus {
		width: 300px;
		border-color: #ffffff;
	}
	
	.box input[type="text"]::placeholder,
	.box input[type="email"]::placeholder {
		color: #fff;
	}
	
	.box button {
		border: 0;
		background: none;
		display: block;
		margin: 20px auto;
		text-align: center;
		border: 2px solid #ffffff;
		padding: 14px 40px;
		outline: none;
		color: white;
		border-radius: 24px;
		transition: 0.25s;
		cursor: pointer;
		background-color: #ffffff26;
	}
	
	.box button:hover {
		background: #ffffff;
		color: #000000;
	}
</style>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js" integrity="sha512-pHVGpX7F/27yZ0ISY+VVjyULApbDlD0/X0rgGbTqCE7WFW5MezNTWG/dnhtbBuICzsd0WQPgpE4REBLv+UqChw==" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/mobile-detect/1.4.4/mobile-detect.min.js"></script>
[guest]
<script type="text/javascript">
	$(window).on('load', function() {

		var cookieValue = $.cookie("regDisabled2");
		if (cookieValue == undefined) {
			let detect = new MobileDetect(window.navigator.userAgent);
			console.log("Mobile: " + detect.mobile());
			if (detect.mobile() == null) {
				$('#formBg').prop('src', '/usr/tpl/dev2/assest/images/ny2021full.jpg');
				$('#regForm').css('top', '40%');
			}
		   // $('#myModal').modal('show');

		}
	});
</script>
[/guest]
<script type="text/javascript">
	$(function() {
		$("#firstStep").on('submit', function(e) {
			e.preventDefault();
			console.log('valid');
			$.post('ajax.php?do=senSMSCode', $('#firstStep').serialize(), function(response) {
				result = $.parseJSON(response);
				if (result.checkResult == '1') {
					$('#phoneRegLogin').val($('#phoneReg').val());
					$('#tokenIdLogin').val(result.tokenId);
					$('#firstStep').hide();
					$('#codeStep').show();
				} else {
					$('#phoneRegReg').val($('#phoneReg').val());
					$('#tokenIdReg').val(result.tokenId);
					$('#firstStep').hide();
					$('#regStep').show();
				}
			});
		});
	});
</script>
<script type="text/javascript">
	$(function() {
		$("#codeStep").on('submit', function(e) {
			e.preventDefault();
			console.log('valid');
			$.post('ajax.php?do=authByPhone', $('#codeStep').serialize(), function(response) {
				result = $.parseJSON(response);
				if (result.checkAuth == '1') {
					//location.reload();
					location.href = '/profile/present';
				} else {
					//  $('#firstStep').hide();
					//  $('#regStep').show();
				}
			});
		});
		$("#regStep").on('submit', function(e) {
			e.preventDefault();
			console.log('valid');
			$.post('ajax.php?do=regByPhone', $('#regStep').serialize(), function(response) {
				result = $.parseJSON(response);
				if (result.checkReg == '1') {

					location.href = '/profile/present';
				}
			});
		});
	});



	function regStart() {
		$('#regBtn').hide();
		$('#hideBtn').hide();
		$('#firstStep').show();
	}
	jQuery(document).ready(function() {

		$('#phoneReg').mask('+0 (000) 000 00 00', {
			placeholder: "+_ (___) ___ __ __"
		});
	});

	function completeReg() {

	};

	function regDisabled() {
		$.cookie('regDisabled2', '1', {
			expires: 10
		});
		$('#myModal').modal('hide');
	}
</script>
<div id="myModal" class="modal fade in" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-lg modal-dialog-centered" role="document">
		<div class="modal-content">

			<div class="img-container">
				<img style="width: 100%;" id="formBg" src="/usr/tpl/dev2/assest/images/ny2021.jpg?v2">
				<div class="regForm box" id="regForm" style="top: 25%;">
					<button id="regBtn" class="" onclick="regStart();">Зарегистрироваться или войти</button>
					<button id="hideBtn" class="" onclick="regDisabled();">Не показывать </button>
					<form id="firstStep" style="display: none;" method="POST" action="#">
						<input name="phoneReg" type="text" id="phoneReg" required />
						<button id="firstStepSend">Получить код </button>
					</form>
					<form id="regStep" style="display: none;">

						<input type="text" name="regName" id="regName" placeholder="Введите ваше ФИО" required />
						<input type="email" name="regMail" id="regMail" placeholder="Введите ваш E-mail" required />
						<input type="text" name="regCode" id="regCode" placeholder="Код из СМС" required/>
						<input name="tokenId" type="hidden" id="tokenIdReg" value="" />
						<input name="phoneReg" type="hidden" id="phoneRegReg" value="" />
						<button id="regStepSend" class="">Зарегистрироваться </button>
					</form>
					<form id="codeStep" style="display: none;">
						<input name="regCode" type="text" id="regCodeLogin" placeholder="Код из СМС" required />
						<input name="tokenId" type="hidden" id="tokenIdLogin" value="" />
						<input name="phoneReg" type="hidden" id="phoneRegLogin" value="" />
						<button id="codeStepSend" class="">Войти </button>
					</form>
				</div>
				<button type="button" class="close closeBtn" data-dismiss="modal">&times;</button>
			</div>


		</div>
	</div>
</div>