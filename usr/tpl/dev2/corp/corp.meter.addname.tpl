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
				<form class="form-horizontal" action="/corp/meter/savename" method="POST">
					<fieldset>
						<div class="control-group">
							<label class="control-label" for="typeahead">Тип прибора</label>
							<div class="controls">
								<select id="select01" name="type" class="chzn-select">
									{%TYPE%}
								</select>
								<p class="help-block">Укажите тип прибора!</p>
							</div>
						</div>												
						<div class="control-group">
							<label class="control-label" for="typeahead">Наименование прибора</label>
							<div class="controls">
								<input type="text" class="span6"  id="name" name="name" value="{%NAME%}" >
								<p class="help-block">Введите наименование прибора!</p>
							</div>
                        </div>                       
						{%EDIT_ID%}
                        <div class="control-group">
							<div class="controls">
								<button type="submit" class="btn btn-primary">Сохранить наименование</button>
								<button type="reset" class="btn">Отмена</button>
							</div>
						</div>
					</fieldset>
				</form>
			</div>
		</div>
	</div>
</div>