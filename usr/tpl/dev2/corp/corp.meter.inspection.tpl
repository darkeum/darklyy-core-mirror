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
				<form class="form-horizontal" action="/corp/meter/inspectionok" method="POST">
					<fieldset>						
                        <div class="control-group">
							<label class="control-label" for="date01">Дата поверки</label>
							<div class="controls">
								<input type="text" class="input-xlarge datepicker" id="date_last" name="date_last" value="{%DATE_LAST%}">
								<p class="help-block">Укажите дату поверки</p>
							</div>
                        </div>					
						<div class="control-group">
                        <label class="control-label" for="fileInput">Сертификат</label>
							<div class="controls">
							<input class="input-file uniform_on" id="fileInput" name="pasport" type="file">
							</div>
                        </div>
						{%EDIT_ID%}
                        <div class="control-group">
							<div class="controls">
								<button type="submit" class="btn btn-primary">Провести поверку прибора</button>
								<button type="reset" class="btn">Отмена</button>
							</div>
						</div>
					</fieldset>
				</form>
			</div>
		</div>
	</div>
</div>