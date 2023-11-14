<div class="col-md-12">
	<div class="card">		
		<div class="card-body card-body-cert">
			<h3 class="h2">{%TITLE%}</h3>
			<p><a data-fancybox="gallery" href="{%PREVIEW_FULL%}" target="_blank"><img class="img_left" style="float: left;" title="{%TITLE%}" src="{%PREVIEW_THUMB_WEBP%}" alt="{%TITLE%}"></a></p>
			<p>
				<b>Выдан:</b>&nbsp;{%ORG_NAME%}<br>
				<b>Регистрационный номер:</b>&nbsp; {%NUMB%}<br>
				<b>Дата регистрации:</b>&nbsp;{%DATE_START%}<br>
				<b>Срок действия:</b>&nbsp;{%DATE_END%}
			</p>
			<p>{%TEXT%}</p>
		</div>
		<div class="col-md-12">
			<div class="document-group">
				<div class="document-group-title-uni"><strong>Результат выполнения</strong></div>	
				<div class="document-group__documents">
					<a href="{%PDF%}" target="_blank" class="ui-link document">
						<i class="document__icon document__icon_pdf"></i>
						<div class="document__title document__title__cert">
							<div class="document__name">{%TITLE%}.pdf</div>
							<div class="document__size">161.82 Кб</div>
						</div>
					</a>	
					<a href="{%PREVIEW_FULL%}" target="_blank" class="ui-link document">
						<i class="document__icon document__icon_dwg"></i>
						<div class="document__title document__title__cert">
							<div class="document__name">{%TITLE%}.jpg</div>
							<div class="document__size">2.5 Мб</div>
						</div>
					</a>	
				</div>
			</div>
		</div>
	</div>
</div>