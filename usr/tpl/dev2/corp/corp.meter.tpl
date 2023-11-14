<div class="row-fluid">
                       <div class="span12" style="margin-top: 15px;"> 
                        	<div class="navbar">
                            	<div class="navbar-inner">
	                                <ul class="breadcrumb">
	                               
	                                    <li>
	                                        <a href="/corp/meter/add">Добавить новый прибор</a>
	                                    </li>
	                                </ul>
                            	</div>
                        	</div>
                    	</div>
						</div>
						<div class="row-fluid">
                        <div class="span12">
                            <!-- block -->
                            <div class="block" >
                                <div class="navbar navbar-inner block-header">
                                    <div class="muted pull-left">Текущая заявка</div>
                                    <div class="pull-right"><span class="badge badge-info">Открыта до </span>

                                    </div>
                                </div>
                                <div class="block-content collapse in" style="margin: 0;">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Наименование</th>                                              
												<th>Адрес</th>
												<th>Состояние</th>
												<th>Действие</th>
                                            </tr>
                                        </thead>
                                        <tbody>
											{%LIST%}
                                           
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <!-- /block -->
                        </div>
                    
                    </div>