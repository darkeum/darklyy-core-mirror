@extends('certificates::manager.body')

@section('title', "Сертификаты")

@section('content')
    
    <form action="@isset($unit){{ route('admin.certificates.update',$unit) }}@else{{ route('admin.certificates.store') }}@endisset" method="POST" enctype="multipart/form-data">
        @isset($unit)
            @method("put")
        @endisset
        <div class="card">
            <div class="card-header">
                <h4>
                    @isset($unit)
                        Редактирование сертификата
                    @else
                        Создание сертификата
                    @endisset
                </h4>
            </div>

            <div class="card-body row">
                <div class="col-md-6">
					<div class="">
						<div class="">	
							<div class="mb-1">
								<label for="title" class="form-label"> @lang('certificates::lang.certificates.editor.title') </label>
								    <input type="text" class="form-control" name="title" id="title" aria-describedby="titleHelp" value="{{ $unit->title ?? false}}" placeholder="@lang('certificates::lang.certificates.editor.title.placeholder')" />
								<div id="titleHelp" class="form-text"> @lang('certificates::lang.certificates.editor.title.description') </div>
				  			</div>						
							<div class="mb-1">
								<label for="category" class="form-label"> @lang('certificates::lang.certificates.editor.category') </label>
									<select class="form-select" name="category" id="category" aria-describedby="categoryHelp">
                                        @foreach($categories as $category)							
											<option value="{{ $category['id'] }}" @selected(isset($unit) and $unit->category == $category['id'])>{{ $category['name'] }}</option>
                                        @endforeach
									</select>
								<div id="categoryHelp" class="form-text"> @lang('certificates::lang.certificates.editor.category.description') </div>
				  			</div>
							<div class="mb-1">
								<label for="date_start" class="form-label"> @lang('certificates::lang.certificates.editor.date_start') </label>
								    <input type="text" class="form-control flatpickr" name="date_start" id="date_start" aria-describedby="date_startHelp" value="{{ $unit->date_start ?? false}}" placeholder=" @lang('certificates::lang.certificates.editor.date_start.placeholder') " />
								<div id="date_startHelp" class="form-text">@lang('certificates::lang.certificates.editor.date_start.description') </div>
						 	</div>
							<div class="mb-1">
								<label for="date_end" class="form-label">@lang('certificates::lang.certificates.editor.date_end') </label>
							        <input type="text" class="form-control flatpickr" name="date_end" id="date_end" aria-describedby="date_endHelp" value="{{ $unit->date_end ?? false }}" placeholder=" @lang('certificates::lang.certificates.editor.date_end.placeholder') " />
								<div id="date_endHelp" class="form-text">@lang('certificates::lang.certificates.editor.date_end.description') </div>
						 	</div>
							<div class="mb-1">
								<label for="org_name" class="form-label"> @lang('certificates::lang.certificates.editor.org_name') </label>
								    <input type="text" class="form-control" name="org_name" id="org_name" aria-describedby="org_nameHelp" value="{{ $unit->org_name ?? false}}" placeholder=" @lang('certificates::lang.certificates.editor.org_name.placeholder') " />
								<div id="org_nameHelp" class="form-text">@lang('certificates::lang.certificates.editor.org_name.description')</div>
				  			</div>
							<div class="mb-1">
								<label for="numb" class="form-label">@lang('certificates::lang.certificates.editor.numb')</label>
								    <input type="text" required class="form-control" name="numb" id="numb" aria-describedby="numbHelp" value="{{ $unit->numb ?? false}}" placeholder=" @lang('certificates::lang.certificates.editor.numb.placeholder') " />
								<div id="numbHelp" class="form-text">@lang('certificates::lang.certificates.editor.numb.description')</div>
				  			</div>
							<div class="mb-1">
								<label for="tags" class="form-label">@lang('certificates::lang.certificates.editor.tags')</label>
								    <input type="text" class="form-control" name="tags" id="tags" aria-describedby="tags" value="@isset($unit->tags)@foreach($unit->tags as $tag){{$tag}}, @endforeach @endisset" placeholder=" @lang('certificates::lang.certificates.editor.tags.placeholder') " />
								<div id="tagsHelp" class="form-text">@lang('certificates::lang.certificates.editor.tags.description')</div>
						 	</div>
							<div class="mb-1">
								<label for="preview" class="form-label">@lang('certificates::lang.certificates.editor.preview')</label>
								<div class="input-group">									
                                    <input type="file" id="preview" name="preview" class="form-control"placeholder="@lang('certificates::lang.certificates.editor.preview.placeholder') " />													
						   		</div>
								<div id="previewHelp" class="form-text">@lang('certificates::lang.certificates.editor.preview.description')</div>   
							</div>				
						</div>
					</div>
				</div>
				<div class="col-md-6">				
					<div class="md-3">
						<div class="">								
							<div class="mb-1">
								<label for="short" class="form-label">@lang('certificates::lang.certificates.editor.short') </label>
								    <textarea rows="5" class="form-control editor-here" name="short" id="short" placeholder="@lang('certificates::lang.certificates.editor.short.description')">{!! $unit->short ?? false !!}</textarea>
								<div id="shortHelp" class="form-text">@lang('certificates::lang.certificates.editor.short.description')</div>
							</div>								
						</div>		
					</div>
					<div class="">
						<div class="">									
							<div class="mb-1">
								<label for="full" class="form-label">@lang('certificates::lang.certificates.editor.full') </label>
								    <textarea rows="5" class="form-control editor-here" name="full" id="full" placeholder=" @lang('certificates::lang.certificates.editor.full.description') ">{{ $unit->full ?? false }}</textarea>
								<div id="fullHelp" class="form-text">@lang('certificates::lang.certificates.editor.full.description')</div>
							</div>								
						</div>		
					</div>
					<div class="">
						<div class="">	
							<div class="mb-1">
								<label for="fulltitle" class="form-label">@lang('certificates::lang.certificates.editor.fulltitle')</label>
								    <input type="text" class="form-control" name="fulltitle" id="fulltitle" aria-describedby="fulltitleHelp" value="{{ $unit->fulltitle ?? false }}" placeholder="@lang('certificates::lang.certificates.editor.fulltitle.placeholder')" />
								<div id="fulltitleHelp" class="form-text">@lang('certificates::lang.certificates.editor.fulltitle.description')</div>
							</div>
							<div class="mb-1">
								<label for="description" class="form-label">@lang('certificates::lang.certificates.editor.description')</label>
								    <input type="text" class="form-control" name="description" id="description" aria-describedby="descriptionHelp" value="{{ $unit->description ?? false }}" placeholder="@lang('certificates::lang.certificates.editor.description.placeholder')" />
								<div id="descriptionHelp" class="form-text">@lang('certificates::lang.certificates.editor.description.description')</div>
							</div>
							<div class="mb-1">
								<label for="keywords" class="form-label">@lang('certificates::lang.certificates.editor.keywords')</label>
								    <input type="text" class="form-control" name="keywords" id="keywords" aria-describedby="keywordsHelp" value="{{ $unit->keywords ?? false }}" placeholder="@lang('certificates::lang.certificates.editor.keywords.placeholder')" />
								<div id="keywordsHelp" class="form-text">@lang('certificates::lang.certificates.editor.keywords.description')</div>
							</div>
						</div>
					</div>
				</div>
            </div>

            <div class="card-footer d-flex justify-content-between">
                <a class="btn btn-primary" href="{{ route('admin.certificates.index') }}">Отмена</a>
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
