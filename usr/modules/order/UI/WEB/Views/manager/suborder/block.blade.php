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
@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-jsonview/1.2.3/jquery.jsonview.min.js" integrity="sha512-ff/E/8AEnLDXnTCyIa+l80evPRNH8q5XnPGY/NgBL645jzHL1ksmXonVMDt7e5D34Y4DTOv+P+9Rmo9jBSSyIg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
		<script>
		var json = 	{!!json_encode($suborder->block_data, JSON_UNESCAPED_UNICODE)!!};

$(function() {
  $("#json").JSONView(json);
  // with options
  //$("#json-collasped").JSONView(json, { collapsed: true });
});
		</script>
		@endpush
<div class="col-md-12">
	<div class="card">
		<div class="card-header">
			<h4 class="card-title">Данные по заявке</h4>
		</div>
		<div class="card-body">

			<ul class="nav nav-tabs nav-fill" id="myTab" role="tablist">
				<li class="nav-item">
					<a class="nav-link active" id="home-tab-fill" data-bs-toggle="tab" href="#home-fill" role="tab" aria-controls="home-fill" aria-selected="true">Общее</a>
				</li>
				<li class="nav-item">
					<a class="nav-link" id="profile-tab-fill" data-bs-toggle="tab" href="#profile-fill" role="tab" aria-controls="profile-fill" aria-selected="false">Блок ввода</a>
				</li>
				<li class="nav-item">
					<a class="nav-link" id="messages-tab-fill" data-bs-toggle="tab" href="#messages-fill" role="tab" aria-controls="messages-fill" aria-selected="false">Блок отопления</a>
				</li>
				<li class="nav-item">
					<a class="nav-link" id="settings-tab-fill" data-bs-toggle="tab" href="#settings-fill" role="tab" aria-controls="settings-fill" aria-selected="false">Блок ГВС</a>
				</li>
			</ul>

			@php
				$data = $suborder->block_data['data']['data'];
				$blockVvod = $suborder->block_data['blockVvod'];
				$bov = $suborder->block_data['bov'];
				$gvs = $suborder->block_data['gvs'];
			@endphp

			<div class="tab-content pt-1">
				<div class="tab-pane active" id="home-fill" role="tabpanel" aria-labelledby="home-tab-fill">
					<div class="row">
						<div class="col-4 mb-3">
							@if($data["sfWidth"])
								Ширина помещения:
							<b>{{$data["sfWidth"]}} м.</b>
							@endif
						</div>
						<div class="col-4 mb-3">
							@if($data["sfLenght"])
								Длина помещения:
							<b>{{$data["sfLenght"]}} м.</b>
							@endif
						</div>
						<div class="col-4 mb-3">
							@if($data["sfHeight"])
								Высота помещения:
							<b>{{$data["sfHeight"]}} м.</b>
							@endif
						</div>
						<div class="col-3 mb-3">
							@if($data["sfHeightBuilding"])
								Высота здания:
							<b>{{$data["sfHeightBuilding"]}}</b>
							@endif
						</div>
						<div class="col-3 mb-3">
							@if($data["sfHeightBuildingType"]) 
								Тип:
							<b>{{$data["sfHeightBuildingType"]}}</b>
							@endif
						</div>
						<div class="col-3 mb-3">
							@if($data["message"]) 
								Причечания:
							<b>{{$data["message"]}}</b>
							@endif
						</div>
						<div class="col-3 mb-3">
							@if($data["nKolon"])
								Количество:
							<b>{{$data["nKolon"]}}</b>
							@endif
						</div>
					</div>



				</div>
				<div class="tab-pane" id="profile-fill" role="tabpanel" aria-labelledby="profile-tab-fill">
					@foreach ($blockVvod as $item)
						<div class="row mb-3 border-bottom">
							<div class="col-2 mb-2">
								@if($item["q"])
									Q 
								<b>{{$item["q"]}} {{$item["qType"]}}</b>
								@endif
							</div>
							<div class="col-2 mb-2">
								@if($item["t1"])
									T1 
								<b>{{$item["t1"]}} ℃</b>
								@endif
							</div>
							<div class="col-2 mb-2">
								@if($item["t2"])
									T2 
								<b>{{$item["t2"]}} ℃</b>
								@endif
							</div>
							@if($item["p1"])
								<div class="col-2 mb-2">
									P1 
								<b>{{$item["p1"]}} {{$item["p1Type"]}}</b>
								
							</div>
							@endif
							@if($item["p2"])
								<div class="col-2 mb-2">
									P2 
								<b>{{$item["p2"]}} {{$item["p2Type"]}}</b>
								
							</div>
							@endif
							@if($item["t3"])
								<div class="col-2 mb-2">
									T3
								<b>{{$item["t3"]}} ℃</b>
								
								</div>
							@endif
							@if($item["t4"])
								<div class="col-2 mb-2">
									T4 
								<b>{{$item["t4"]}} ℃</b>
								
							</div>
							@endif
							@if($item["p3"])
								<div class="col-2 mb-2">								
									P3 
								<b>{{$item["p3"]}} {{$item["p3Type"]}}</b>
								
							</div>
							@endif
							
							@if($item["p4"])
								<div class="col-2 mb-2">
									P4 
								<b>{{$item["p4"]}} {{$item["p4Type"]}}</b>
							
							</div>
							@endif

							<div class="col-2 mb-2">
									Узел учета
								<b>@if($item['uzel_ucheta']==true) Да @else Нет @endif</b>
							</div>

							<div class="col-2 mb-2">
									Подпитка
								<b>@if($item['podpitka']==true) Да @else Нет @endif</b>
							</div>

							<div class="col-2 mb-2">
									Регулятор давления 
							<b>@if($item['regulator_davleniya']==true) {{$item['SFregDavVal']}} @else Нет @endif</b>

							</div>

							<div class="col-2 mb-2">
									Ограничение расхода
							<b>@if($item['regulator_davleniya']==true) {{$item['SFregDavVal']}} @else Нет @endif</b>

							</div>

						
						
						</div>
					@endforeach
				</div>
				<div class="tab-pane" id="messages-fill" role="tabpanel" aria-labelledby="messages-tab-fill">
					@foreach ($bov as $item)
						<div class="row mb-3 border-bottom">

							@if($item["q"])
								<div class="col-2 mb-2">
									Q
								<b>{{$item["q"]}} {{$item["qType"]}}</b>								
							</div>
							@endif

							@if($item["t1"])
								<div class="col-2 mb-2">
									T1
								<b>{{$item["t1"]}} ℃</b>								
							</div>
							@endif

							@if($item["t2"])
								<div class="col-2 mb-2">
									T2
								<b>{{$item["t2"]}} ℃</b>								
							</div>
							@endif

							@if($item["p1"])
								<div class="col-2 mb-2">
									P1
								<b>{{$item["p1"]}} {{$item['p1Type']}}</b>								
							</div>
							@endif

							@if($item["p2"])
								<div class="col-2 mb-2">
									P2
								<b>{{$item["p2"]}} {{$item['p2Type']}}</b>								
							</div>
							@endif

							@if($item["t11"])
								<div class="col-2 mb-2">
									T11
								<b>{{$item["t11"]}} ℃</b>								
							</div>
							@endif

							@if($item["t21"])
								<div class="col-2 mb-2">
									T21
								<b>{{$item["t11"]}} ℃</b>								
							</div>
							@endif
							
							@if($item["dp"])
								<div class="col-2 mb-2">
									ΔP
								<b>{{$item["dp"]}}</b>								
							</div>
							@endif
							
							@if($item["h"])
								<div class="col-2 mb-2">
									H
								<b>{{$item["h"]}} м.</b>								
							</div>
							@endif

							@if($item["bov_type"])
								<div class="col-2 mb-2">
									Тип схемы
								<b>{{$item["bov_type"]}}</b>								
							</div>
							@endif

							@if($item["nasos_type"])
								<div class="col-2 mb-2">
									Насос 
								<b>{{$item["nasos_type"]}}</b>								
							</div>
							@endif

							@if($item["ras_nasos"])
								<div class="col-2 mb-2">
									Расположение насоса 
								<b>{{$item["ras_nasos"]}}</b>								
							</div>
							@endif

							@if($item["teplo_sel"])
								<div class="col-2 mb-2">
									Кол-во теплообменников
								<b>{{$item["teplo_sel"]}}</b>								
							</div>
							@endif

							@if($item["teploPr"])
								<div class="col-2 mb-2">
									Распределение нагрузки
								<b>{{$item["teploPr"]}}</b>								
							</div>
							@endif

							@if($item["reg_t"])
								<div class="col-2 mb-2">
									Регулятор температуры 
								<b>{{$item["reg_t"]}}</b>								
							</div>
							@endif

							@if($item["SFkontr"])
								<div class="col-2 mb-2">
									Контроллер
								<b>{{$item["SFkontr"]}}</b>								
							</div>
							@endif

							@if($item["SFregKlapan"])
								<div class="col-2 mb-2">
									Регулирующий клапан 
								<b>{{$item["SFregKlapan"]}}</b>								
							</div>
							@endif
								
							@if($item["SFshkafAvtomatiky"])
								<div class="col-2 mb-2">
									Шкаф автоматики
								<b>{{$item["SFshkafAvtomatiky"]}}</b>								
							</div>
							@endif
							
							@if($item["SFtypeBlock"])
								<div class="col-2 mb-2">
									Тип блока
								<b>{{$item["SFtypeBlock"]}}</b>								
							</div>
							@endif

							@if($item["bp"])
								<div class="col-2 mb-2">
										Блок подпитки										
										<ul>
											<li>Насосная станция <b>{{$item['bp']["SFnasosStan"]}}</b></li>
											<li>Повысительный насос <b>{{$item['bp']["SFnasosPov"]}}</b></li>
											<li>Клапан соленоидный <b>{{$item['bp']["SFklapanSol"]}}</b></li>
											<li>Расширительный бак <b>{{$item['bp']["SFrasshirBak"]}}</b></li>
										</ul>						
								</div>
							@endif

							@if($item["SFpch"])
								<div class="col-2 mb-2">
									Регулирующий клапан 
								<b>{{$item["SFpch"]}}</b>								
							</div>
							@endif

						</div>
					@endforeach
				</div>
				<div class="tab-pane" id="settings-fill" role="tabpanel" aria-labelledby="settings-tab-fill">
					@foreach ($gvs as $item)
						<div class="row mb-3 border-bottom">

							@if($item["qGVS"])
								<div class="col-2 mb-2">
									Q
								<b>{{$item["qGVS"]}} {{$item["qSelGVS"]}}</b>								
							</div>
							@endif

							@if($item["GVSt1"])
								<div class="col-2 mb-2">
									T1л
								<b>{{$item["GVSt1"]}} ℃</b>								
							</div>
							@endif

							@if($item["GVSt2"])
								<div class="col-2 mb-2">
									T2л
								<b>{{$item["GVSt2"]}} ℃</b>								
							</div>
							@endif

							@if($item["p1"])
								<div class="col-2 mb-2">
									P1
								<b>{{$item["p1"]}} {{$intem['p1Type']}}</b>								
							</div>
							@endif


							@if($item["p2"])
								<div class="col-2 mb-2">
									P2
								<b>{{$item["p2"]}} {{$intem['p2Type']}}</b>								
							</div>
							@endif

							
							@if($item["GVSt3"])
								<div class="col-2 mb-2">
									T3
								<b>{{$item["GVSt3"]}} ℃</b>								
							</div>
							@endif

							@if($item["GVStv1"])
								<div class="col-2 mb-2">
									Tв1
								<b>{{$item["GVStv1"]}} ℃</b>								
							</div>
							@endif
							
							@if($item["pb1"])
								<div class="col-2 mb-2">
									Pв1
								<b>{{$item["pb1"]}} {{$item['pb1SelGVS']}}</b>								
							</div>
							@endif

							@if($item["kontrGVS"])
								<div class="col-2 mb-2">
									Контроллер
								<b>{{$item["kontrGVS"]}}</b>								
							</div>
							@endif
							
							@if($item["regKlapanGVS"])
								<div class="col-2 mb-2">
									Регулирующий клапан 
								<b>{{$item["regKlapanGVS"]}}</b>								
							</div>
							@endif

							@if($item["shkafAvtomatikyGVS"])
								<div class="col-2 mb-2">
									Шкаф автоматики 
								<b>{{$item["shkafAvtomatikyGVS"]}}</b>								
							</div>
							@endif

							@if($item["nasos_type_gvs"])
								<div class="col-2 mb-2">
									Насос 
								<b>{{$item["nasos_type_gvs"]}}</b>								
							</div>
							@endif

							@if($item["teplo_sel_gvs"])
								<div class="col-2 mb-2">
									Кол-во теплообменников
								<b>{{$item["teplo_sel_gvs"]}}</b>								
							</div>
							@endif

							@if($item["teploPr_gvs"])
								<div class="col-2 mb-2">
									Распределение нагрузки
								<b>{{$item["teploPr_gvs"]}}</b>								
							</div>
							@endif

							@if($item["reg_t_gvs"])
								<div class="col-2 mb-2">
									Регулятор температуры 
								<b>{{$item["reg_t_gvs"]}}</b>								
							</div>
							@endif 

							@if($item["SFshemaPodkl"])
								<div class="col-2 mb-2">
									Схема подключения
								<b>{{$item["SFshemaPodkl"]}}</b>								
							</div>
							@endif

							@if($item["SFpch"])
								<div class="col-2 mb-2">
									Преобразователь частоты
								<b>{{$item["SFpch"]}}</b>								
							</div>
							@endif
						</div>
					@endforeach
				</div>
			</div>
		</div>
	</div>
</div>
