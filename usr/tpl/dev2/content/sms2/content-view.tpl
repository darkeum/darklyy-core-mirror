[enqueue:title:auth]
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.maskedinput/1.4.1/jquery.maskedinput.min.js" integrity="sha512-d4KkQohk+HswGs6A1d6Gak6Bb9rMWtxjOa0IiY49Q3TeFd5xAzjWXDCBW9RS7m86FQ4RzM2BdHmdJnnKRYknxw==" crossorigin="anonymous"></script>
 <script>
    $(document).ready(function() {
    $("#phone").mask("+7 (999) 99-99-999");
  });
</script>
<div id="particles-js" class="snow"></div>

<div class="trees" style="    min-height: 700px;">
	<div class="limiter merry" style="    padding-top: 250px;   ">
	<div class="container-login100">
		<div class="wrap-login100" style="z-index: 10000;     box-shadow: 0 0 37px rgb(178 194 225);">
			<div class="login100-form-title" style="background-image: url(https://s3.tproger.ru/uploads/2018/12/new-year-cover-770x270.jpg);">
				<span class="login100-form-title-1">
					Авторизация  
					<span>в личном кабинете</span>
				</span>
			</div>
			<form class="login100-form validate-form" role="form"  action="/profile/login" method="post">
				<div class="wrap-input100 validate-input m-b-26" data-validate="E-mail является обязательным полем">
					<span class="label-input100">Телефон</span>
					<input class="input100" type="tel" name="phone" id="phone" placeholder="Введите ваш номер телефона">
					<span class="focus-input100"></span>
				</div>				
				<div class="container-login100-form-btn">
					<button class="login100-form-btn">
						Получить код
					</button>
				</div>				
			</form>
		</div>
	</div>
</div>
		
	<div class="fox">
		<img src="https://s3-us-west-2.amazonaws.com/s.cdpn.io/25839/critters.svg" width="300px">
	</div>
	<div class="tree">
		<img src="https://s3-us-west-2.amazonaws.com/s.cdpn.io/25839/tree.svg" width="200px">
	</div>
</div>

<style>

.snow {
  position: absolute;
  z-index: 100;
  width: 100%;
  height: 100%;
}

.footer-top{
  z-index: 10001 !important;
}

.trees {
  display: grid;
  grid-template-rows: 50% 1fr;
  grid-template-columns: 1fr 1fr 1fr;
  background: url(https://s3-us-west-2.amazonaws.com/s.cdpn.io/25839/trees.svg) repeat-x;
  position: relative;
  width: 100%;
  height: 500px;
}

.merry {
  color: #c84257;
  font-family: 'Mountains of Christmas';
  text-align: center;
  grid-row: 1;
  grid-column: 1/4;
  align-self: center;
  justify-self: center;
  font-size: 6rem;
}

.fox {
  grid-column: 3;
  grid-row: 3;
  align-self: end;
  justify-self: start;
  position: relative;
}

.tree {
  align-self: end;
  justify-self: end;
  grid-column: 1;
  grid-row: 3;
}

</style>
<script src="https://cdnjs.cloudflare.com/ajax/libs/particles.js/2.0.0/particles.js"></script>
<script>
	particlesJS("particles-js", {
  "particles": {
    "number": {
      "value": 100,
      "density": {
        "enable": true,
        "value_area": 800 } },


    "color": {
      "value": "#fff" },

    "shape": {
      "type": "circle",
      "stroke": {
        "width": 0,
        "color": "#000000" },

      "polygon": {
        "nb_sides": 5 } },


    "opacity": {
      "value": 1,
      "random": false,
      "anim": {
        "enable": false,
        "speed": 0.5,
        "opacity_min": 0.1,
        "sync": false } },


    "size": {
      "value": 10,
      "random": true,
      "anim": {
        "enable": false } },


    "line_linked": {
      "enable": false },

    "move": {
      "enable": true,
      "speed": 3,
      "direction": "bottom",
      "random": false,
      "straight": false,
      "out_mode": "out",
      "bounce": false,
      "attract": {
        "enable": false,
        "rotateX": 600,
        "rotateY": 1200 } } },



  "interactivity": {
    "detect_on": "canvas",
    "events": {
      "onhover": {
        "enable": true,
        "mode": "repulse" },

      "onclick": {
        "enable": true,
        "mode": "repulse" },

      "resize": true },

    "modes": {
      "grab": {
        "distance": 400,
        "line_linked": {
          "opacity": 0.5 } },


      "bubble": {
        "distance": 100,
        "size": 4,
        "duration": 0.3,
        "opacity": 1,
        "speed": 3 },

      "repulse": {
        "distance": 100,
        "duration": 0.4 },

      "push": {
        "particles_nb": 4 },

      "remove": {
        "particles_nb": 2 } } },



  "retina_detect": true });
</script>
