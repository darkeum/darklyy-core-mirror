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
				<form class="form-horizontal" action="/corp/meter/detailsave" method="POST">
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
							<label class="control-label" for="select01">Укажите модель</label>
                            <div class="controls">
								<select id="select01" name="mid" class="chzn-select">
									{%MODEL_LIST%}
								</select>
								<p class="help-block">Если нет подходящего - <a href="/corp/meter/addname">добавьте новый</a>!</p>
							</div>
                        </div>						
						<div class="control-group">
							<label class="control-label" for="typeahead">Номер прибора</label>
							<div class="controls">
								<input type="text" class="span6"  id="number" name="number" value="{%NUMBER%}" >
								<p class="help-block">Укажите номер прибора!</p>
							</div>
                        </div>
                        <div class="control-group">
							<label class="control-label" for="date01">Дата поверки</label>
							<div class="controls">
								<input type="text" class="input-xlarge datepicker" id="date_last" name="date_last" value="{%DATE_LAST%}">
								<p class="help-block">Укажите дату последней поверки</p>
							</div>
                        </div>
						<div class="control-group">
							<label class="control-label" for="typeahead">Период обслуживания</label>
							<div class="controls">
								<input type="text" class="span6"  id="address" name="period" value="{%PERIOD%}" >
								<p class="help-block">Введите период обслуживания в годах!</p>
							</div>
                        </div>
						<div class="control-group">
                        <label class="control-label" for="fileInput">Паспорт прибора</label>
							<div class="controls">
							<input class="input-file uniform_on" id="fileInput" name="pasport" type="file">
							</div>
                        </div>
						{%EDIT_ID%}
						{%CID%}
                        <div class="control-group">
							<div class="controls">
								<button type="submit" class="btn btn-primary">Сохранить прибор</button>
								<button type="reset" class="btn">Отмена</button>
							</div>
						</div>
					</fieldset>
				</form>
			</div>
		</div>
	</div>
</div>