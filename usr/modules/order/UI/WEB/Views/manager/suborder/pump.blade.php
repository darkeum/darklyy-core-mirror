<style>
	h6, .h6 {
    font-size: 13px;
    color: #423748;
}

h5, .h5, h6, .h6 {
    margin-top: 18px;
    margin-bottom: 18px;
}
h1, h2, h3,  h5, h6, .h1, .h2, .h3, .h5, .h6 {
    font-family: inherit;
    font-weight: 400;
    line-height: 1.1;
    color: inherit;
    text-rendering: optimizelegibility;
}
</style>
		
<div class="col-md-12">
	<div class="card">
		<div class="card-body">
			<div class="row mb-1">
				<div class="col-md-6 border-bottom">
  					<h2 class="pb-2">Данные по заявке</h2>
				</div>	
				<div class="col-md-6 text-end border-bottom">
					grtg
			  	</div>		
			</div>	
			<div class="row">
				<div class="col-3 mb-3">
					@if(isset($suborder->order_id)) 
						Номер заявки:
					<b>{{$suborder->order_id}}</b>
					@endif
				</div>
				<div class="col-3 mb-3">
					@if(isset($suborder->pressure))    
						Напор (H):
						<b>{{$suborder->pressure}}</b>
					@endif
				</div>
				<div class="col-3 mb-3">
					@if(isset($suborder->qPump))    
						Расход (G):
						<b>{{$suborder->qPump}}</b>
					@endif
				</div>				
				<div class="col-3 mb-3">
					@if(isset($suborder->pressure))    
						Напор (H):
						<b>{{$suborder->pressure}}</b>
					@endif
				</div>
				<div class="col-3 mb-3">
					@if(isset($suborder->type))    
						Назначение насосного оборудования:
						<b>
							@switch($suborder->type)
								@case(1)
									Отопление
									@break
								@case(2)
									Циркуляция ГВС
									@break
								@case(3)
									Подпитка отопления
									@break
								@case(4)
									Повысительный хвс/гвс
									@break
								@case(5)
									Пожарные насосы
									@break									
							@endswitch
						</b>
					@endif
				</div>
				<div class="col-3 mb-3">
					@if(isset($suborder->r1))    
						Частотный преобразователь:
						<b>
							@switch($suborder->r1)
								@case(1)
									Применить ПЧ
									@break
								@case(2)
									Без ПЧ
									@break				
							@endswitch
						</b>
					@endif
				</div>
				<div class="col-3 mb-3">
					@if(isset($suborder->r2))    
						Напряжение:
						<b>
							@switch($suborder->r2)
								@case(1)
									220
									@break
								@case(2)
									380
									@break				
							@endswitch
						</b>
					@endif
				</div>	 	 	       		
			</div>	
  		</div>
	</div>
</div>
