
<div class="col-md-12">
	<div class="card">
		<div class="card-body">
			<div class="row mb-1">
				<div class="col-md-6 border-bottom">
  					<h2 class="pb-2">Данные по резюме</h2>
				</div>	
				<div class="col-md-6 text-end border-bottom">
					
			  	</div>		
			</div>	
			<div class="row">	
				<div class="col-md-12">
					<small>Отрасль:</small><br/>
					<b>{{ $suborder->categories->name}}</b>
					<br/><br/> 
					<small>Должность:</small><br/>
					<b>{{ $suborder->position}}</b>
					<br/><br/>
					@if(isset($suborder->summary)) 
						<small>Резюме:</small><br/>
						<b><a href="{{$suborder->summary_url}}" download>{{$suborder->summary}}</a></b>
						<br/><br/>
					@endif
					@if(isset($suborder->other_file))    
						<small>Другие файлы:</small><br/>
						<b>{{$suborder->other_file}}</b>
						<br/><br/>
					@endif
				</div>	       		
			</div>	
  		</div>
	</div>
</div>
