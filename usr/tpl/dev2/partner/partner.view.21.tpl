<style>
	.text-topol{
		color: #a6ce8f !important;
	}
	.share-topol > li > a
	{
		border-color:  #a6ce8f !important;
	}	
	.share-topol > li > a > i
	{
		color:  #a6ce8f !important;
	}
	
	.share-topol > li > a:hover
	{
		background-color:  #a6ce8f !important;
	}	
	.share-topol > li > a:hover > i
	{
		color:  #fff !important;
	}
</style>
<div class="col-md-8">
	<div class="cmt-bgcolor-white box-shadow pt-40 pl-30 pb-40 pr-30 mb-30">
		<div class="row">			
			<div class="col-sm-12">
				<div class="section-title">
					<div class="title-header">
						<h5 class="text-topol">Информация о партнёре</h5>
						<h2 class="title">ТОПОЛ-ЭКО - локальные <strong>очистные сооружения</strong></h2>
					</div>
					<div class="title-desc">«ТОПОЛ-ЭКО» - крупнейший в России производитель ЛОС, работающих по аэрационному принципу.</div>
				</div>
				<p>Компания <strong>«ТОПОЛ-ЭКО»</strong> - первый и крупнейший в России производитель автономных канализаций, работающих по аэрационному принципу. <br>
				Индивидуальные, комплексные и специализированные очистные сооружения, выпускаемые этим предприятием, безупречно обслуживают частные домовладения, нежилые объекты различного назначения и даже небольшие населенные пункты.</p>	
				<div class="mt-30 mb-30 cmt-bgcolor-white box-shadow pt-20 pb-15">						
					<div class="row cmt-vertical_sep">
						<div class="col-lg-4 col-md-4 col-sm-6 cmt-box-col-wrapper">
							<!-- cmt-fid -->
							<div class="cmt-fid inside cmt-fid-with-icon cmt-fid-view-lefticon text-center">
								<div class="cmt-fid-icon-wrapper cmt-textcolor-skincolor">
									<i class="far fa-shopping-cart text-topol"></i>
								</div>
								<div class="cmt-fid-contents text-center">
									<h4 class="cmt-fid-inner" style="font-size: 22px;">
										<span   
										data-appear-animation="animateDigits" 
										data-to="2000"
										data-speed="1500"
										class="timer count-number"
										style="font-size: 22px;">
										</span>
										<sub style="font-size: 22px;">тыс.</sub>
									</h4>
									<h3 class="cmt-fid-title text-center">Реализовано продукции</h3>
								</div>
							</div><!-- cmt-fid end -->
						</div>
						<div class="col-lg-4 col-md-4 col-sm-6 cmt-box-col-wrapper">
							<!-- cmt-fid -->
							<div class="cmt-fid inside cmt-fid-with-icon cmt-fid-view-lefticon text-center">
								<div class="cmt-fid-icon-wrapper cmt-textcolor-skincolor">
									<i class="far fa-drafting-compass text-topol"></i>
								</div>
								<div class="cmt-fid-contents text-center">
									<h4 class="cmt-fid-inner" style="font-size: 22px;">
										<span   
										data-appear-animation="animateDigits" 
										data-to="160"
										data-speed="1500"
										class="timer count-number"
										style="font-size: 22px;">
										</span>
										<sub style="font-size: 22px;">+</sub>
									</h4>
									<h3 class="cmt-fid-title text-center">Уникальных проектов</h3>
								</div>
							</div><!-- cmt-fid end -->
						</div>		
						<div class="col-lg-4 col-md-4 col-sm-6 cmt-box-col-wrapper">
							<!-- cmt-fid -->
							<div class="cmt-fid inside cmt-fid-with-icon cmt-fid-view-lefticon text-center">
								<div class="cmt-fid-icon-wrapper cmt-textcolor-skincolor">
									<i class="far fa-calendar text-topol"></i>
								</div>
								<div class="cmt-fid-contents text-center">
									<h4 class="cmt-fid-inner" style="font-size: 22px;">
										<span   
										data-appear-animation="animateDigits" 
										data-to="17"
										data-speed="1500"
										class="timer count-number"
										style="font-size: 22px;">
										</span>
										<sub style="font-size: 22px;">лет.</sub>
									</h4>
									<h3 class="cmt-fid-title text-center">Работает на рынке ЛОС</h3>
								</div>
							</div><!-- cmt-fid end -->
						</div>	
						
						
					</div>
				</div>
				
				<script>
					
					(function ($) {
						$.fn.countTo = function (options) {
							options = options || {};
							
							return $(this).each(function () {
								// set options for current element
								var settings = $.extend({}, $.fn.countTo.defaults, {
									from:            $(this).data('from'),
									to:              $(this).data('to'),
									speed:           $(this).data('speed'),
									refreshInterval: $(this).data('refresh-interval'),
									decimals:        $(this).data('decimals')
								}, options);
								
								// how many times to update the value, and how much to increment the value on each update
								var loops = Math.ceil(settings.speed / settings.refreshInterval),
								increment = (settings.to - settings.from) / loops;
								
								// references & variables that will change with each update
								var self = this,
								$self = $(this),
								loopCount = 0,
								value = settings.from,
								data = $self.data('countTo') || {};
								
								$self.data('countTo', data);
								
								// if an existing interval can be found, clear it first
								if (data.interval) {
									clearInterval(data.interval);
								}
								data.interval = setInterval(updateTimer, settings.refreshInterval);
								
								// initialize the element with the starting value
								render(value);
								
								function updateTimer() {
									value += increment;
									loopCount++;
									
									render(value);
									
									if (typeof(settings.onUpdate) == 'function') {
										settings.onUpdate.call(self, value);
									}
									
									if (loopCount >= loops) {
										// remove the interval
										$self.removeData('countTo');
										clearInterval(data.interval);
										value = settings.to;
										
										if (typeof(settings.onComplete) == 'function') {
											settings.onComplete.call(self, value);
										}
									}
								}
								
								function render(value) {
									var formattedValue = settings.formatter.call(self, value, settings);
									$self.html(formattedValue);
								}
							});
						};
						
						$.fn.countTo.defaults = {
							from: 0,               // the number the element should start at
							to: 0,                 // the number the element should end at
							speed: 1000,           // how long it should take to count between the target numbers
							refreshInterval: 100,  // how often the element should be updated
							decimals: 0,           // the number of decimal places to show
							formatter: formatter,  // handler for formatting the value before rendering
							onUpdate: null,        // callback method for every time the element is updated
							onComplete: null       // callback method for when the element finishes updating
						};
						
						function formatter(value, settings) {
							return value.toFixed(settings.decimals);
						}
					}(jQuery));
					
					jQuery(function ($) {
						// custom formatting example
						$('.count-number').data('countToOptions', {
							formatter: function (value, options) {
								return value.toFixed(options.decimals).replace(/\B(?=(?:\d{3})+(?!\d))/g, ',');
							}
						});
						
						// start all the timers
						$('.timer').each(count);  
						
						function count(options) {
							var $this = $(this);
							options = $.extend({}, options || {}, $this.data('countToOptions') || {});
							$this.countTo(options);
						}
					});
					
				</script>
				<p><strong>Индивидуальные очистные сооружения</strong> предназначены для эксплуатации в малоэтажных домах, на дачах и в многоэтажных зданиях, а также обеспечивают переработку стоков, поступающих от групп из нескольких малоэтажных домов, некрупных отелей и небольших коттеджных поселков. <br> Данная группа продукции включает:</p>
				<ul class="cmt-list cmt-list-style-icon cmt-list-icon-color-skincolor pt-5">
					<li>
						<i class="cmt-textcolor-skincolor fa fa-check-circle text-topol"></i>
						<span class="cmt-list-li-content">Широко известные аэрационные установки очистки сточных вод (УОСВ) <strong>«ТОПАС»</strong>, оснащенные двумя компрессорами;</span>
					</li>
					<li>
						<i class="cmt-textcolor-skincolor fa fa-check-circle text-topol"></i>
						<span class="cmt-list-li-content">Новую разработку компании – однокомпрессорные установки очистки сточных вод <strong>«ТОПАС-С»</strong>;</span>
					</li>
					<li>
						<i class="cmt-textcolor-skincolor fa fa-check-circle text-topol"></i>
						<span class="cmt-list-li-content">Очистные сооружения <strong>«ТОПАЭРО»</strong>, оснащенные более вместительной приемной камерой по сравнению с двумя вышеуказанными модельными линейками продукции.</span>
					</li>						
				</ul>
				<p class="mt-2">В зависимости от модели это оборудование рассчитано на обслуживание от четырех до двухсот человек. Оно осуществляет очистку бытовых стоков на 98%, что позволяет отводить их в окружающую среду либо использовать в технических целях. Принцип работы этого оборудования не предусматривает его откачку с помощью ассенизаторской машины. Данные УОСВ возможно устанавливать в любых типах грунта, в том числе с высоким уровнем почвенных вод.</p>					
			</div>
		</div>
	</div>
</div>
<div class="col-md-4">
	{%RIGHT_PARTNER_MENU%}	
</div>