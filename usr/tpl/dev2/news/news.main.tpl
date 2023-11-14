<div class="col-md-12 mb-3">
	<div class="news-block">
		<div class="news-info">
			<div class="row ml-1 align-items-center text-center">
				<div class="col-3 col-md-12 text-center news-info-col">
					<div class="news-info-right"><i class="fad fa-folder-open" aria-hidden="true"></i><span class="news-name-cat">{%CATEGORY%}</span></div>
				</div>
					<div class="col-3 col-md-12 text-center news-info-col">
					<div class="news-info-right"><i class="fad fa-calendar" aria-hidden="true"></i><span>{%MYDATE:d%}.{%MYDATE:m%}</span></div>
				</div>
					<div class="col-3 col-md-12 text-center news-info-col">
					<div class="news-info-right"><i class="fad fa-eye" aria-hidden="true"></i><span>{%VIEWS%}</span></div>
				</div>
					<div class="col-3 col-md-12 text-center news-info-col">
					<div class="news-info-right">
						<span class="favorite-btn" data-id="{%ID%}" data-module="news" data-text="false">
							<i class="fad fa-heart" aria-hidden="true"></i>
							<span class="favorite-counter">{%FAVORITE%}</span>
						</span>
					</div>
				</div>
			</div>
		</div>
		<div class="news-context">
			<div class="row">
				<div class="col-md-12"><h2 class="news-title"><a href="{%FULL_LINK%}" title="{%TITLE%}" alt="{%TITLE%}">{%TITLE:70%}</a></h2></div>
				<div class="col-md-4"><div class="news-image" style="background: url({%PREVIEW%}) no-repeat center center"></div></div>
				<div class="col-md-8"><div class="news-text">{%SHORT:500%}</div></div>
				<div class="col-md-12 text-right align-self-end"><div class="news-element-next"><a href="{%FULL_LINK%}">читать далее...</a></div></div>
			</div>
		</div>
	</div>		
</div>	