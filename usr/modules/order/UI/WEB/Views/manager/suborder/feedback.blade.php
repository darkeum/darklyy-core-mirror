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
					{{-- <a href="/administration/module/request/view/{{ $suborder->request->id }}" class="btn btn-outline-primary">Перейти к расчету</a> --}}
			  	</div>		
			</div>	
			<div class="row">	
				<div class="col-md-12">
					@if(isset($suborder->order_id)) 
					<small>Номер заявки:</small><br/>
					<b>{{$suborder->order_id}}</b>
					<br/><br/>
				@endif
				@if(isset($suborder->request_id))    
					<small>Номер расчета:</small><br/>
					<b>{{$suborder->request_id}}</b>
					<br/><br/>
				@endif
				</div>	       		
			</div>	
  		</div>
	</div>
</div>
