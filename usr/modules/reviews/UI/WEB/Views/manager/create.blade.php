@extends('reviews::manager.body')

@section('title', "Отзывы клиентов")

@section('content')
    
    <form action="@isset($unit){{ route('admin.reviews.update',$unit) }}@else{{ route('admin.reviews.store') }}@endisset" method="POST" enctype="multipart/form-data">
        @isset($unit)
            @method("put")
        @endisset
        <div class="card">
            <div class="card-header">
                <h4>
                    @isset($unit)
                    Редактирование отзыва
                    @else
                    Создание отзыва
                    @endisset
                </h4>
            </div>

            <div class="card-body row">
                <div class="col-md-6">
					<div class="">
						<div class="">	
							<div class="mb-1">
								<label for="title" class="form-label">@lang('reviews::lang.reviews.editor.title')</label>
								    <input type="text" value="@isset($unit){{$unit->title}}@endisset" class="form-control" name="title" id="title" aria-describedby="titleHelp"  placeholder="@lang('reviews::lang.reviews.editor.title.placeholder')" />
								<div id="titleHelp" class="form-text">@lang('reviews::lang.reviews.editor.title.description')</div>
				  			</div>
							<div class="mb-1">
								<label for="subtitle" class="form-label">@lang('reviews::lang.reviews.editor.subtitle')</label>
								    <input type="text" value="@isset($unit){{$unit->subtitle}}@endisset" class="form-control" name="subtitle" id="subtitle" aria-describedby="subtitleHelp"  placeholder="@lang('reviews::lang.reviews.editor.subtitle.placeholder')" />
								<div id="subtitleHelp" class="form-text">@lang('reviews::lang.reviews.editor.subtitle.description')</div>
				  			</div>
							<div class="mb-1">
								<label for="category" class="form-label">@lang('reviews::lang.reviews.editor.category')</label>
									<select class="form-select" name="category" id="category" aria-describedby="categoryHelp">
                                    @foreach ($categories as $cat)
                                        <option @selected(isset($unit) && $unit->category == $cat['id']) value="{{ $cat['id'] }}">{{ $cat['name'] }}</option>
                                    @endforeach
									</select>
								<div id="categoryHelp" class="form-text">@lang('reviews::lang.reviews.editor.category.description')</div>
				  			</div>
							<div class="mb-1">
								<label for="oid" class="form-label">@lang('reviews::lang.reviews.editor.oid')</label>
								<select class="form-select select2" name="oid" id="oid" aria-describedby="categoryHelp">
                                    @isset($unit)
                                        <option value="{{$unit->oid}}">{{$org->name}}</option>
                                    @endisset
                                </select>
								<div id="oidHelp" class="form-text">@lang('reviews::lang.reviews.editor.oid.description')</div>
				  			</div>
							<div class="mb-1">
								<label for="type" class="form-label">@lang('reviews::lang.reviews.editor.type')</label>
									<select class="form-select" name="type" id="type">
										@foreach ($types as $key => $type)
                                            <option @selected(isset($unit) && $unit->type == $key) value="{{ $key }}" >{{ $type }}</option>
                                        @endforeach
									</select>
								<div id="typeHelp" class="form-text">@lang('reviews::lang.reviews.editor.type.description')</div>
				  			</div>
							<div class="mb-1">
								<label for="city" class="form-label">@lang('reviews::lang.reviews.editor.city')</label>
								    <input type="text" value="@isset($unit){{$unit->city}}@endisset" class="form-control" name="city" id="city" aria-describedby="city" placeholder="@lang('reviews::lang.reviews.editor.city')" />
								<div id="cityHelp" class="form-text">@lang('reviews::lang.reviews.editor.city.description')</div>
						 	</div>
							<div class="mb-1">
								<label for="date_coop" class="form-label">@lang('reviews::lang.reviews.editor.date_coop')</label>
								    <input type="text" value="@isset($unit){{$unit->date_coop}}@endisset" class="form-control flatpickr" name="date_coop" id="date_coop" aria-describedby="date_coop"  placeholder="@lang('reviews::lang.reviews.editor.date_coop.placeholder')" />
								<div id="date_coopHelp" class="form-text">@lang('reviews::lang.reviews.editor.date_coop.description')</div>
						 	</div>
							<div class="mb-1">
								<label for="tags" class="form-label">@lang('reviews::lang.reviews.editor.tags')</label>
								    <input type="text" value="@isset($unit)@foreach($unit->tags as $tag){{$tag}}, @endforeach @endisset" class="form-control" name="tags" id="tags" aria-describedby="tags"  placeholder="@lang('reviews::lang.reviews.editor.tags.placeholder')" />
								<div id="tagsHelp" class="form-text">@lang('reviews::lang.reviews.editor.tags.description')</div>
						 	</div>
							<div class="mb-1">
								<label for="preview" class="form-label">@lang('reviews::lang.reviews.editor.preview')</label>
								<div class="input-group">									
								 	<input type="file" id="preview" name="preview" class="form-control" placeholder="@lang('reviews::lang.reviews.editor.preview.placeholder')"/>								
						   		</div>
								<div id="previewHelp" class="form-text">@lang('reviews::lang.reviews.editor.preview.description')</div>   
							</div>				
						</div>
					</div>
				</div>
                <div class="col-md-6">
					<div class="md-3">
						<div class="">	
							<div class="mb-1">
								<label for="content_text" class="form-label">@lang('reviews::lang.reviews.editor.content')</label>
								    <textarea rows="5" name="content" class="form-control editor-here" id="content_text" placeholder="@lang('reviews::lang.reviews.editor.content.description')">@isset($unit){!! $unit->content !!}@endisset</textarea>
								<div id="" class="form-text">@lang('reviews::lang.reviews.editor.content.description')</div>
							</div>								
						</div>		
					</div>
					<div class="">
						<div class="">	
							<div class="mb-1">
								<label for="fulltitle" class="form-label">@lang('reviews::lang.reviews.editor.fulltitle')</label>
								    <input type="text" value="@isset($unit){{$unit->fulltitle}}@endisset" class="form-control" name="fulltitle" id="fulltitle" aria-describedby="fulltitleHelp" placeholder="@lang('reviews::lang.reviews.editor.fulltitle.placeholder')" />
								<div id="fulltitleHelp" class="form-text">@lang('reviews::lang.reviews.editor.fulltitle.description')</div>
							</div>
							<div class="mb-1">
								<label for="description" class="form-label">@lang('reviews::lang.reviews.editor.description')</label>
								    <input type="text" value="@isset($unit){{$unit->description}}@endisset" class="form-control" name="description" id="description" aria-describedby="descriptionHelp"  placeholder="@lang('reviews::lang.reviews.editor.description.placeholder')" />
								<div id="descriptionHelp" class="form-text">@lang('reviews::lang.reviews.editor.description.description')</div>
							</div>
							<div class="mb-1">
								<label for="keywords" class="form-label">@lang('reviews::lang.reviews.editor.keywords')</label>
								    <input type="text" value="@isset($unit){{$unit->keywords}}@endisset" class="form-control" name="keywords" id="keywords" aria-describedby="keywordsHelp"  placeholder="@lang('reviews::lang.reviews.editor.keywords.placeholder')" />
								<div id="keywordsHelp" class="form-text">@lang('reviews::lang.reviews.editor.keywords.description')</div>
							</div>
						</div>
					</div>
				</div>
            </div>

            <div class="card-footer d-flex justify-content-between">
                <a class="btn btn-primary" href="{{ route('admin.reviews.index') }}">Отмена</a>
                <button class="btn btn-success" type="submit">@isset($unit)Изменить @else Создать @endif</button>
            </div>
        </div>
    </form>

@endsection


@push('styles')
  <!-- vendor css files -->
  <link rel="stylesheet" href="{{ asset(mix('vendors/css/forms/select/select2.min.css')) }}">
  <link rel="stylesheet" href="{{ asset(mix('vendors/css/pickers/flatpickr/flatpickr.min.css')) }}">
  <link rel="stylesheet" href="{{ asset(mix('css/base/plugins/forms/pickers/form-flat-pickr.css')) }}">
  <link rel="stylesheet" href="{{ asset(mix('vendors/css/editors/quill/quill.snow.css')) }}">
  <link rel="stylesheet" href="{{ asset(mix('css/base/plugins/forms/form-quill-editor.css')) }}">
@endpush

@push('scripts')
    <script src="{{ asset(mix('vendors/js/forms/select/select2.full.min.js')) }}"></script>
    <script src="{{ asset(mix('js/scripts/forms/form-select2.js')) }}"></script>
    <script src="{{ asset('vendors/js/forms/select/i18n/'. app()->getLocale() .'.js') }}"></script>

    <script src="{{ asset(mix('vendors/js/pickers/flatpickr/flatpickr.min.js')) }}"></script>
    <script src="{{ asset('vendors/js/pickers/flatpickr/dist/l10n/ru.js') }}"></script>


    <script src="{{ asset(mix('vendors/js/editors/quill/katex.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/editors/quill/highlight.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/editors/quill/quill.min.js')) }}"></script>
    <script src="{{ asset('vendors/js/editors/quill/quill-textarea.js') }}"></script>
    <script>
        @if(session()->has('message'))
            Swal.fire({
                icon: 'success',
                title: "{{session()->get('message')}}",
            })
        @endif
            $('#oid').select2({
                width: "100%",
                escapeMarkup: function (text) {
                    return text;
                },
                ajax: {
                    url: "{{ route('admin.reviews.getOrgs') }}",
                    dataType: "json",
                    delay: 250,
                    processResults: function (data) {
                        return {
                            results: $.map(data, function (item) {
                                return {
                                    text: item.name,
                                    id: item.id
                                }
                            })
                        };
                    },
                    error: function (err) {
                        console.log(err)
                    },
                    cache: true,
                },
                placeholder: "Нажмите чтобы выбрать",
                minimumInputLength: 1,
                language: {
                    "noResults": function () {
                        return "Нет результатов";
                    },
                }
            });


            flatpickr.localize(flatpickr.l10ns.{!! app()->getLocale() !!});

            $('.flatpickr').flatpickr({
                altInput: true,
                altFormat: "d.m.Y",
                locale: "{!! app()->getLocale() !!}"
            });

            let editor = '.editor-here';
            let unitEditor = quill(editor, {
                bounds: editor,
                modules: {
                    formula: true,
                    syntax: true,
                    toolbar: [
                        [
                            {
                                font: []
                            },
                            {
                                size: []
                            }
                        ],
                        ['bold', 'italic', 'underline', 'strike'],
                        [
                            {
                                color: []
                            },
                            {
                                background: []
                            }
                        ],
                        [
                            {
                                script: 'super'
                            },
                            {
                                script: 'sub'
                            }
                        ],
                        [
                            {
                                header: '1'
                            },
                            {
                                header: '2'
                            },
                            'blockquote',
                            'code-block'
                        ],
                        [
                            {
                                list: 'ordered'
                            },
                            {
                                list: 'bullet'
                            },
                            {
                                indent: '-1'
                            },
                            {
                                indent: '+1'
                            }
                        ],
                        [
                            'direction',
                            {
                                align: []
                            }
                        ],
                        ['link', 'image', 'video', 'formula'],
                        ['clean']
                    ]
                },
                theme: 'snow'
            });
           
    </script>
@endpush
