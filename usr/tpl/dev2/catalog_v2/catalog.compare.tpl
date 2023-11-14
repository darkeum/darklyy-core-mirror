<div class="wrap-compare-by">
	<div class="wrap-compare">
		<h1><i class="fa fa-balance-scale" aria-hidden="true"></i> Сравнение товаров <button class="btn compare-del" type="button" href="javascript:void(0);" onclick="compareClear()" name="ms2_action">Очистить лист <i class="fa fa-times" aria-hidden="true"></i></button></h1>
		<div class="wrap-content">
			<div class="compare-cats">{%CATS%}</div>
			<div class="compare-con-wrap-div">
				<div class="compare-next" onclick="compareNext()"><i class="fa fa-angle-right" aria-hidden="true"></i></div>
				<div class="compare-prev" onclick="comparePrev()"><i class="fa fa-angle-left" aria-hidden="true"></i></div>
				<div class="compare-con-wrap">
					<div class="compare-con">
						{%CONTENT%}
					</div>
				</div>
			</div>
			{%ATTRIBUTE%}
		</div>

	</div>
</div>
<script src="usr/tpl/dev2/assest/js/compare.js" charset="utf-8"></script>