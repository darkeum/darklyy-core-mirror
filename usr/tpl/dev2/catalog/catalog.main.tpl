<div class="item-list">
	<div class="row">
		<div class="col-md-3 no-padding photobox">
            <div class="add-image">
				<span class="photo-count">
					<i class="fa fa-camera"></i> 1 
				</span> 
				<a href="{%FULL_LINK%}">
					<img class="thumbnail no-margin" src="{%PREVIEW%}" alt="{%TITLE%}">
				</a>
			</div>
		</div>
		<div class="col-md-7 add-desc-box">
            <div class="ads-details">
                <h5 class="add-title">[more]{%TITLE%}[/more]</h5>
                <span class="info-row"> 
					<span class="item-location">{%SHORT:300%}</span> 
				</span>
                <div class="prop-info-box">
					<div class="prop-info">
                        <div class="clearfix prop-info-block">
                            <span class="title ">{%SKU%}</span>
                            <span class="text">Артикул</span>
                        </div>
						<div class="clearfix prop-info-block middle">
                            <span class="title prop-area" style="text-transform: uppercase;">{%UNIT%}</span>
                            <span class="text">Ед. измерения</span>
                        </div>
                        <div class="clearfix prop-info-block">
                            <span class="title prop-room">{%MAN%}</span>
                            <span class="text">Производитель</span>
                        </div>
					</div>
                </div>
			</div>
		</div>
        <div class="col-md-2 text-right  price-box">
			<a class="btn btn-border-thin  btn-save"   title="save ads"  data-toggle="tooltip" data-placement="left">
                <i class="icon icon-heart"></i>
            </a>
            <a class="btn btn-border-thin  btn-share ">
                <i class="icon icon-export" data-toggle="tooltip" data-placement="left"  title="share"></i>
            </a>
			<h3 class="item-price "> <strong>{%PRICE%}</strong></h3>
            <div style="clear: both"></div>
			<a class="btn btn-success btn-sm bold" onclick="add_to_cart('{%ID%}')" href="javascript:void(0);"> В корзину   </a>
		</div>
	</div>
</div>