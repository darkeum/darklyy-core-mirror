@extends('orgs::manager.body')

@section('content')
	<div id="content">
		<div class="card">
			<div class="card-header">
				<h4 class="card-title">
					@if($unit ?? false)
						Подпись №{{ $unit->id }}
					@else
						Новая подпись
					@endif
				</h4>
			</div>
			<div class="card-body">
				<form id="main-form" action="@if(isset($unit)){{ route('admin.orgs.sign.update', $unit) }}@else{{ route('admin.orgs.sign.store') }}@endif" method="POST">

					@if($unit ?? false)
						@method('PUT')
					@endif

					<div class="row">

						{{-- Ответственный --}}
						<div class="col-3">
							<div class="mb-1">
								<label class="form-label" for="uid">Ответственный</label>
								<select class="form-select select2" id="uid" name="uid" required>

								@foreach ($users_list as $u)

											<option value="{{$u->id}}" @selected(isset($unit) && $unit->uid==$u->id)>
												{{ user_short_name($u,true) }}
										</option>

								@endforeach

								</select>

							</div>
						</div>
						{{-- Ответственный --}}

						{{-- Тип --}}
						<div class="col-3">
							<div class="mb-1">
								<label class="form-label" for="type">Тип</label>
								<select class="form-select " id="type" name="type" required>

								@foreach ($type_list as $key => $type)

											<option value="{{$key}}" @selected(isset($unit) && $unit->type==$key)>
												{{ $type }}
										</option>

								@endforeach

								</select>

							</div>
						</div>
						{{-- Тип --}}

						{{-- На основании --}}
						<div class="col-3">
							<div class="mb-1">
								<label class="form-label" for="base">На основании</label>
								<select class="form-select " id="base" name="base" required>

								@foreach ($base_list as $key => $base)

											<option value="{{$key}}" @selected(isset($unit) && $unit->base==$key)>
												{{ $base }}
										</option>

								@endforeach

								</select>

							</div>
						</div>
						{{-- На основании --}}

						{{-- Номер приказа --}}
						<div class="col-3">
							<div class="mb-1">
								<label class="form-label" for="base_numb">Номер приказа</label>

									<input class="form-control" id="base_numb" name="base_numb" placeholder="Введите номер приказа" value="@if($unit ?? false){{$unit->base_numb}}@endif" required>

								</select>

							</div>
						</div>
						{{-- Номер приказа --}}

						{{-- Дата регистрации --}}
						<div class="col-3">
							<div class="mb-1">
								<label class="form-label" for="base_date">Дата регистрации</label>

									<input type='date' class="form-control flatpickr" id="base_date" name="base_date" value="@if($unit ?? false){{ $unit->base_date }}@else{{ $now }}@endif" required>

								</select>

							</div>
						</div>
						{{-- Дата регистрации --}}


						{{-- Организация --}}
						<div class="col-6">
							<div class="mb-1">
								<label class="form-label" for="org">Организация</label>
								<select class="form-select select2" id="org" name="org" required @disabled(isset($unit))>

								@foreach ($orgs as $org)

											<option value="{{$org->id}}" @selected(isset($unit) && $org->id==$unit->oid)>
												{{ $org->organization->name }}
										</option>

								@endforeach

								</select>

							</div>
						</div>
						{{-- Организация --}}

						{{-- Тип должности --}}
						<div class="col-3">
							<div class="mb-1">
								<label class="form-label" for="type_post">Тип должности</label>
								<select class="form-select " id="type_post" name="type_post" required>

								@foreach ($type_post_list as $key => $type)

											<option value="{{$key}}" @selected(isset($unit) && $unit->type_post==$key)>
												{{ $type }}
										</option>

								@endforeach

								</select>

							</div>
						</div>
						{{-- Тип должности --}}

						{{-- Период действия "с" --}}
						<div class="col-3">
							<div class="mb-1">
								<label class="form-label" for="date_start">Период действия "с"</label>

									<input disabled="true" placeholder="Период действия 'с'" type='date' class="form-control flatpickr" id="date_start" name="date_start" value="@if($unit ?? false){{ $unit->date_start }}@endif" required>

								</select>

							</div>
						</div>
						{{-- Период действия "с" --}}


						{{-- Период действия "до" --}}
						<div class="col-3">
							<div class="mb-1">
								<label class="form-label" for="date_end">Период действия "до"</label>

									<input disabled="true" placeholder="Период действия 'до'" type='date' class="form-control flatpickr" id="date_end" name="date_end" value="@if($unit ?? false){{ $unit->date_end }}@endif" required>

								</select>

							</div>
						</div>
						{{-- Период действия "до" --}}


						{{-- Период действия checkbox --}}
						<div class="col-3 d-flex align-items-center">
							<div class="mb-1">
								<div class="form-check form-check-inline">
									<input class="form-check-input" type="checkbox" id="checkbox_date">
									<label class="form-check-label" for="checkbox_date">Период действия</label>
								</div>
							</div>
						</div>
						{{-- Период действия checkbox --}}

					</div>
				</div>

				{{-- Кнопки --}}
				<div class="card-footer col-12 d-flex justify-content-between">
					<a href="" class="btn btn-outline-secondary waves-effect">Отмена</a>
					<button type="submit" class="btn btn-primary waves-effect waves-float waves-light">
						@if($unit ?? false)
							Сохранить
						@else
							Создать
						@endif
					</button>
				</div>
				{{-- Кнопки --}}
			</form>
		</div>
	</div>
@endsection



{{-- Необходимые стили --}}
@push('styles')

    <link rel="stylesheet" href="{{ asset(mix('vendors/css/forms/select/select2.min.css')) }}">
	<link rel="stylesheet" href="{{ asset(mix('vendors/css/pickers/flatpickr/flatpickr.min.css')) }}">
	<link rel="stylesheet" href="{{ asset(mix('css/base/plugins/forms/pickers/form-flat-pickr.css')) }}">
@endpush
{{-- Необходимые стили --}}




{{-- Необходимые js либы --}}
@push('scripts')
    <!-- BEGIN: Page Vendor JS-->
    <script src="{{ asset(mix('vendors/js/forms/select/select2.full.min.js')) }}"></script>
    <script src="{{ asset('vendors/js/forms/select/i18n/'. app()->getLocale() .'.js') }}"></script>
	<script src="{{ asset(mix('vendors/js/pickers/flatpickr/flatpickr.min.js')) }}"></script>
    <script src="{{ asset('vendors/js/pickers/flatpickr/dist/l10n/ru.js') }}"></script>

    <!-- Page js files -->
    <script data-title="Page js files">

        $(".select2").select2({
            placeholder: "Нажмите чтобы выбрать элемент",
        });

		$('#checkbox_date').click(function(){
			if ($(this).is(':checked')){
				$('.flatpickr').prop('disabled', false);
				$('.flatpickr').prop('disabled', false);
			} else {
				$('.flatpickr').prop('disabled', true);
				$('.flatpickr').prop('disabled', true);
			}
		});

		flatpickr.localize(flatpickr.l10ns.{!! app()->getLocale() !!});

		$('.flatpickr').flatpickr({
			altInput: true,
			altFormat: "d.m.Y",
			// altFormat: "j F Y",
			// allowInput: true,
			locale: "{!! app()->getLocale() !!}"
		});

    </script>

@endpush
{{-- Необходимые js либы --}}



