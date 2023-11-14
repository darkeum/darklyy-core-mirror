<link rel="stylesheet" href="/usr/tpl/dev2/assest/css/specification/specification_edit.css">
{%SELECTED_CSS%}
<form id="mainForm" onsubmit="return false;">
	<input type="hidden" name="page" value="1" id="page">
	<input type="hidden" name="category" value="" id="category">
	{%SELECTED_HEAD%}
	<div class="single-profile p-0 mt-2">	
		<div class="mt-2 grid_container">
			<div id="grid_container_load">
				<div class="grid_container__container">
					<div class="floatingCirclesG">
						<div class="f_circleG frotateG_01"></div>
						<div class="f_circleG frotateG_02"></div>
						<div class="f_circleG frotateG_03"></div>
						<div class="f_circleG frotateG_04"></div>
						<div class="f_circleG frotateG_05"></div>
						<div class="f_circleG frotateG_06"></div>
						<div class="f_circleG frotateG_07"></div>
						<div class="f_circleG frotateG_08"></div>
					</div>
				</div>
			</div>
			<div class="grid_container__search">
				<div class="input-group mb-3">
					<input type="text" class="form-control" placeholder="Введите для поиска" name="search" id="search">
					<div class="input-group-append">
					  <button class="btn btn-outline-secondary" type="button" onclick="ll({act: 'html'});">Поиск</button>
					</div>
				  </div>
			</div>
			<div class="grid_container__find">
				<div id="grid_container__find_load_all">
					<div class="grid_container__container">
						<div class="floatingCirclesG">
							<div class="f_circleG frotateG_01"></div>
							<div class="f_circleG frotateG_02"></div>
							<div class="f_circleG frotateG_03"></div>
							<div class="f_circleG frotateG_04"></div>
							<div class="f_circleG frotateG_05"></div>
							<div class="f_circleG frotateG_06"></div>
							<div class="f_circleG frotateG_07"></div>
							<div class="f_circleG frotateG_08"></div>
						</div>
					</div>
				</div>
				<div class="find_table table-head">
					<div class="find_table__title">Наименование</div>
					<div class="find_table__sku">Артикул</div>
					<div class="find_table__man">Производитель</div>
					<div class="find_table__price">Цена</div>
					<div class="find_table__actions">Действия</div>
				</div>
				<div id="find_table_body" class="table-body all_text" onscroll="scroll_load(this);">{%TOV%}</div>
			</div>
			<div class="grid_container__find_load">
				<div id="fountainG">
					<div id="fountainG_1" class="fountainG"></div>
					<div id="fountainG_2" class="fountainG"></div>
					<div id="fountainG_3" class="fountainG"></div>
					<div id="fountainG_4" class="fountainG"></div>
					<div id="fountainG_5" class="fountainG"></div>
					<div id="fountainG_6" class="fountainG"></div>
					<div id="fountainG_7" class="fountainG"></div>
					<div id="fountainG_8" class="fountainG"></div>
				</div>
			</div>
			<div class="grid_container__categories all_text">{%CATEGORIES%}</div>
			<div class="grid_container__filter all_text" id="filter"></div>
			<div class="grid_container__goods">
				<div class="goods_table table-head">
					<div class="goods_table__title">Наименование</div>
					<div class="goods_table__sku">Артикул</div>
					<div class="goods_table__man">Производитель</div>
					<div class="goods_table__count">Количество</div>
					<div class="goods_table__price">Цена</div>
					<div class="goods_table__total">Сумма</div>
					<div class="goods_table__actions">Действия</div>
				</div>
				<div class="table-body all_text" id="goods_table">{%GOODS%}</div>
			</div>
		</div>
	</div>
</form>

[script]
<script type="text/javascript" src="/usr/tpl/dev2/assest/vendor/jquery-3.4.1/jquery-3.4.1.min.js?v=5e4129400044f"></script>
[/script]
<script
  src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"
  integrity="sha256-VazP97ZCwtekAsvgPBSUwPFKdrwD3unUfSGVYrahUqU="
  crossorigin="anonymous"></script>
<script>
    $.widget.bridge('uibutton', $.ui.button);
    $.widget.bridge('uitooltip', $.ui.tooltip);
</script>
[script]
<script type="text/javascript" src="/usr/tpl/dev2/assest/vendor/popper/popper.min.js?v=5ea6af0bb1530"></script>
<script type="text/javascript" src="/usr/tpl/dev2/assest/vendor/bootstrap-4.3.1/dist/js/bootstrap.min.js?v=5e422375b7758"></script>

<script src="/usr/tpl/dev2/assest/js/priceList.js" charset="utf-8"></script>
<link rel="stylesheet" type="text/css" href="/usr/tpl/dev2/assest/css/style.css">
<link rel="stylesheet" type="text/css" href="/usr/tpl/dev2/assest/css/simple.css">
<link rel="stylesheet" type="text/css" href="/usr/tpl/dev2/assest/css/jquery.arcticmodal-0.3.css">
<script src="/usr/tpl/dev2/assest/js/jquery.arcticmodal-0.3.min.js" type="text/javascript" charset="utf-8"></script>
<script src="/usr/tpl/dev2/assest/js/catalogView.js" charset="utf-8"></script>
[/script]

<script src="/usr/other/sub/price/js/price.js"></script>
<script src="/usr/tpl/dev2/assest/js/selected/selected.js"></script>
{%SELECTED_SCRIPT%}
