<div class="row-fluid">
	<div class="span12" style="margin-top: 15px;"> 
		<div class="navbar">
			<div class="navbar-inner">
				<ul class="breadcrumb">
					<li><a href="/corp/meter">Список приборов</a></li>
				</ul>
			</div>
		</div>
	</div>
</div>
<div class="row-fluid">
	<div class="block">
		<div class="navbar navbar-inner block-header">
             <div class="muted pull-left">{%TITLE%}</div>
        </div>
        <div class="block-content collapse in">
			<div class="span12">
				<form class="form-horizontal" action="/corp/meter/save" method="POST">
					<fieldset>
						<div class="control-group">
							<label class="control-label" for="typeahead">Наименование объекта</label>
							<div class="controls">
								<input type="text" class="span6" name="name" value="{%NAME%}" id="typeahead" >
								<p class="help-block">Введите наименование объекта обслуживания!</p>
							</div>
						</div>
						<div class="control-group">
							<label class="control-label" for="typeahead">Адрес объекта</label>
							<div class="controls">
								<input type="text" class="span6"  id="address" name="address" value="{%ADDRESS%}" >
								<p class="help-block">Укажите адрес объекта пользуясь подсказками!</p>
							</div>
                        </div>
                        <div class="control-group">
							<label class="control-label" for="textarea2">Комментарий</label>
							<div class="controls">
								<textarea class="input-xlarge textarea" name="comment" placeholder="Введите текст" style="width: 810px; height: 200px">{%COMMENT%}</textarea>
							</div>
                        </div>
						{%EDIT_ID%}
                        <div class="control-group">
							<div class="controls">
								<button type="submit" class="btn btn-primary">Сохранить узел учёта</button>
								<button type="reset" class="btn">Отмена</button>
							</div>
						</div>
					</fieldset>
				</form>
			</div>
		</div>
	</div>
</div>
<link href="https://cdn.jsdelivr.net/npm/suggestions-jquery@17.10.0/dist/css/suggestions.min.css" type="text/css" rel="stylesheet" />
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
<!--[if lt IE 10]>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery-ajaxtransport-xdomainrequest/1.0.1/jquery.xdomainrequest.min.js"></script>
<![endif]-->
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/suggestions-jquery@17.10.0/dist/js/jquery.suggestions.min.js"></script>
<script type="text/javascript">
    $("#address").suggestions({
        token: "ddeafb9ff43e79578243d8f031f6e9d8b047ca0e",
        type: "ADDRESS",
        count: 5,
        /* Вызывается, когда пользователь выбирает одну из подсказок */
        onSelect: function(suggestion) {
            console.log(suggestion);
        }
    });
</script>