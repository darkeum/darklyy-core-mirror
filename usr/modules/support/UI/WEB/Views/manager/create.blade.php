@extends('support::manager.body')

@section('title', __('support::lang.create'))

@section('content')

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <section class="vertical-wizard">
        <form action="{{ route('admin.support.store') }}" method="POST" enctype="multipart/form-data">
            <div class="bs-stepper vertical vertical-wizard-support">

                <div class="bs-stepper-header">
                    <div class="step" data-target="#account-details-vertical" role="tab"
                         id="account-details-vertical-trigger">
                        <button type="button" class="step-trigger">
                            <span class="bs-stepper-box">1</span>
                            <span class="bs-stepper-label">
                                <span class="bs-stepper-title">@lang('support::form.type')</span>
                                <span class="bs-stepper-subtitle">@lang('support::form.type.description')</span>
                            </span>
                        </button>
                    </div>
                    <div class="step" data-target="#personal-info-vertical" role="tab"
                         id="personal-info-vertical-trigger">
                        <button type="button" class="step-trigger">
                            <span class="bs-stepper-box">2</span>
                            <span class="bs-stepper-label">
                                <span class="bs-stepper-title">@lang('support::form.info')</span>
                                <span class="bs-stepper-subtitle">@lang('support::form.info.description')</span>
                            </span>
                        </button>
                    </div>
                    <div class="step" data-target="#address-step-vertical" role="tab"
                         id="address-step-vertical-trigger">
                        <button type="button" class="step-trigger">
                            <span class="bs-stepper-box">3</span>
                            <span class="bs-stepper-label">
                                <span class="bs-stepper-title">@lang('support::form.other')</span>
                                <span class="bs-stepper-subtitle">@lang('support::form.other.description')</span>
                            </span>
                        </button>
                    </div>
                    <div class="step" data-target="#social-links-vertical" role="tab"
                         id="social-links-vertical-trigger">
                        <button type="button" class="step-trigger">
                            <span class="bs-stepper-box">4</span>
                            <span class="bs-stepper-label">
                                <span class="bs-stepper-title">@lang('support::form.attaches')</span>
                                <span class="bs-stepper-subtitle">@lang('support::form.attaches.description')</span>
                            </span>
                        </button>
                    </div>
                </div>

                <div class="bs-stepper-content">

                    {{-- Раздел --}}
                    <div id="account-details-vertical" class="content" role="tabpanel"
                         aria-labelledby="account-details-vertical-trigger">
                        <div class="content-header">
                            <h5 class="mb-0">@lang('support::form.type')</h5>
                            <small class="text-muted">@lang('support::form.type.description.full')</small>
                        </div>
                        <div class="row custom-options-checkable g-1">
                            @foreach ($types as $typeKey => $type)
                                <div class="col-md-4">
                                    <input class="custom-option-item-check" type="radio" name="type" id="{{ $typeKey }}"
                                           value="{{ $typeKey }}" @if ($typeKey == 'portal') checked="" @endif />
                                    <label class="custom-option-item text-center p-1" for="{{ $typeKey }}">
                                        {!! $type['icon_big'] !!}
                                        <span class="custom-option-item-title h4 d-block">{{ $type['default'] ?? __('support::lang.empty') }}</span>
                                        <small>{!! $type['description'] ?? __('support::lang.empty') !!}</small>
                                    </label>
                                </div>
                            @endforeach
                        </div>
                        <div class="mt-2 d-flex justify-content-end">
                            {{--<button type="button" class="btn btn-outline-secondary btn-prev" disabled>--}}
                            {{--    <i data-feather="arrow-left" class="align-middle me-sm-25 me-0"></i>--}}
                            {{--    <span class="align-middle d-sm-inline-block d-none">@lang('support::lang.back')</span>--}}
                            {{--</button>--}}
                            <button type="button" class="btn btn-primary btn-next">
                                <span class="align-middle d-sm-inline-block d-none">@lang('support::lang.next')</span>
                                <i data-feather="arrow-right" class="align-middle ms-sm-25 ms-0"></i>
                            </button>
                        </div>

                    </div>
                    {{-- Раздел --}}

                    {{-- Информация --}}
                    <div id="personal-info-vertical" class="content" role="tabpanel"
                         aria-labelledby="personal-info-vertical-trigger">
                        <div class="content-header">
                            <h5 class="mb-0">@lang('support::form.info')</h5>
                            <small class="text-muted">@lang('support::form.info.description.full')</small>
                        </div>
                        <div class="row">
                            <div class="mb-1 col-md-12">
                                <label class="form-label" for="title">@lang('support::form.subject')</label>
                                <input type="text" id="title" class="form-control" name="title"
                                       placeholder="@lang('support::form.subject.placeholder')"/>
                            </div>
                            @can('support manager')
                                <label class="form-label" for="uid">@lang('support::form.user')</label>
                                <div class="mb-1 col-md-12">
                                    <select name="uid" id="uid" class="form-select select2 w-100">
                                        @foreach ($users as $u)
                                            <option value="{{ $u->id }}" @selected($u->id == Auth::id())>
                                                {{ $u->full_user_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            @endcan
                            <div class="mb-1 col-md-6">
                                <label class="form-label" for="place">@lang('support::form.place')</label>
                                <input type="text" id="place" class="form-control" name="place"
                                       placeholder="@lang('support::form.place.placeholder')"/>
                            </div>
                            <div class="mb-1 col-md-6">
                                <label class="form-label" for="url">@lang('support::form.url')</label>
                                <input type="text" id="url" class="form-control" name="url"
                                       placeholder="@lang('support::form.url.placeholder')"/>
                            </div>
                        </div>
                        <div class="row mt-1">
                            <div class="col-12">
                                <div class="form-floating mb-0">
                                    <textarea data-length="20" name="comment" class="form-control char-textarea"
                                              id="textarea-counter" rows="3"
                                              placeholder="@lang('support::form.comment.placeholder')"
                                              style="height: 100px"></textarea>
                                    <label for="textarea-counter">@lang('support::form.comment')</label>
                                </div>
                                <small class="textarea-counter-value float-end"><span class="char-count">0</span> / 1000
                                </small>
                            </div>
                        </div>
                        <div class="mt-2 d-flex justify-content-between">
                            <button type="button" class="btn btn-outline-secondary btn-prev">
                                <i data-feather="arrow-left" class="align-middle me-sm-25 me-0"></i>
                                <span class="align-middle d-sm-inline-block d-none">@lang('support::lang.back')</span>
                            </button>
                            <button type="button" class="btn btn-primary btn-next">
                                <span class="align-middle d-sm-inline-block d-none">@lang('support::lang.next')</span>
                                <i data-feather="arrow-right" class="align-middle ms-sm-25 ms-0"></i>
                            </button>
                        </div>
                    </div>
                    {{-- Информация --}}

                    {{-- Дополнительно --}}
                    <div id="address-step-vertical" class="content" role="tabpanel"
                         aria-labelledby="address-step-vertical-trigger">
                        <div class="content-header">
                            <h5 class="mb-0">@lang('support::form.other')</h5>
                            <small class="text-muted">@lang('support::form.other.description.full')</small>
                        </div>

                        <div class="row">
                            <div class="col-md-3 mb-1">
                                <label class="form-label">@lang('support::form.view_at')</label>
                                <input type="text" class="form-control flatpickr-inline d-none" name="view_at"
                                       placeholder="YYYY-MM-DD"/>
                            </div>
                            <div class="col-md-9 mb-1">
                                <label class="form-label">@lang('support::form.mark')</label>
                                <div class="row custom-options-checkable g-1">
                                    <div class="col-md-12">
                                        <input class="custom-option-item-check" type="radio" name="mark" id="mark1"
                                               value="0" checked=""/>
                                        <label class="custom-option-item p-1" for="mark1">
                                            <span class="d-flex justify-content-between flex-wrap mb-50">
                                                <span class="fw-bolder">В порядке очереди</span>
                                                <span class="fw-bolder">Free</span>
                                            </span>
                                            <small class="d-block">Ваша заявка будет выполнена в порадке очереди</small>
                                        </label>
                                    </div>

                                    <div class="col-md-12">
                                        <input class="custom-option-item-check" type="radio" name="mark" id="mark2"
                                               value="1"/>
                                        <label class="custom-option-item p-1" for="mark2">
                                            <span class="d-flex justify-content-between flex-wrap mb-50">
                                                <span class="fw-bolder">Приоритетная заявка</span>
                                                <span class="fw-bolder">Free</span>
                                            </span>
                                            <small class="d-block">Заявка требует неотлогательного выполнения</small>
                                        </label>
                                    </div>
                                    <div class="col-md-12">
                                        <input class="custom-option-item-check" type="radio" name="mark" id="mark3"
                                               value="2"/>
                                        <label class="custom-option-item p-1" for="mark3">
                                            <span class="d-flex justify-content-between flex-wrap mb-50">
                                                <span class="fw-bolder">Премиальная заявка</span>
                                                <span class="fw-bolder">100₽</span>
                                            </span>
                                            <small class="d-block">Премиум заявка с индивидульным обслуживанием</small>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mt-2 d-flex justify-content-between">
                            <button type="button" class="btn btn-outline-secondary btn-prev">
                                <i data-feather="arrow-left" class="align-middle me-sm-25 me-0"></i>
                                <span class="align-middle d-sm-inline-block d-none">@lang('support::lang.back')</span>
                            </button>
                            <button type="button" class="btn btn-primary btn-next">
                                <span class="align-middle d-sm-inline-block d-none">@lang('support::lang.next')</span>
                                <i data-feather="arrow-right" class="align-middle ms-sm-25 ms-0"></i>
                            </button>
                        </div>
                    </div>
                    {{-- Дополнительно --}}

                    {{-- Вложения --}}
                    <div id="social-links-vertical" class="content" role="tabpanel"
                         aria-labelledby="social-links-vertical-trigger">
                        <div class="content-header">
                            <h5 class="mb-0">@lang('support::form.attaches')</h5>
                            <small class="text-muted">@lang('support::form.attaches.description.full')</small>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <x-media-library-attachment multiple name="attaches"/>
                            </div>
                        </div>

                        <div class="mt-2 d-flex justify-content-between ">
                            <button type="button" class="btn btn-outline-secondary btn-prev">
                                <i data-feather="arrow-left" class="align-middle me-sm-25 me-0"></i>
                                <span class="align-middle d-sm-inline-block d-none">@lang('support::lang.back')</span>
                            </button>
                            <button class="btn btn-success btn-submit">@lang('support::lang.send')</button>
                        </div>

                    </div>
                    {{-- Вложения --}}

                </div>

            </div>
        </form>
    </section>

@endsection

@section('vendor-style')
    <!-- vendor css files -->
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/forms/wizard/bs-stepper.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/forms/select/select2.min.css')) }}">
    <!-- vendor css files -->
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/pickers/pickadate/pickadate.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/pickers/flatpickr/flatpickr.min.css')) }}">
@endsection

@section('page-style')
    <!-- Page css files -->
    <link rel="stylesheet" href="{{ asset(mix('css/base/plugins/forms/form-validation.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('css/base/plugins/forms/form-wizard.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('css/base/plugins/forms/pickers/form-flat-pickr.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('css/base/plugins/forms/pickers/form-pickadate.css')) }}">
@endsection

@section('vendor-script')
    <!-- vendor files -->
    <script src="{{ asset(mix('vendors/js/forms/wizard/bs-stepper.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/forms/select/select2.full.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/forms/validation/jquery.validate.min.js')) }}"></script>
    <!-- vendor files -->
    <script src="{{ asset(mix('vendors/js/pickers/pickadate/picker.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/pickers/pickadate/picker.date.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/pickers/pickadate/picker.time.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/pickers/pickadate/legacy.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/pickers/flatpickr/flatpickr.min.js')) }}"></script>
    <script src="{{ asset('vendors/js/pickers/flatpickr/dist/l10n/ru.js') }}"></script>
@endsection
@section('page-script')

    <!-- Page js files -->
    <script>

        $(document).ready(function () {
            $('.select2').select2({width: '100%'});

            {{--flatpickr.localize(flatpickr.l10ns.{!! app()->getLocale() !!});--}}
            $('.flatpickr-inline').flatpickr({
                inline: true,
                locale: "{!! app()->getLocale() !!}"
            });
        });

        var bsStepper = document.querySelectorAll('.bs-stepper');
        if (typeof bsStepper !== undefined && bsStepper !== null) {
            for (var el = 0; el < bsStepper.length; ++el) {
                bsStepper[el].addEventListener('show.bs-stepper', function (event) {
                    var index = event.detail.indexStep;
                    var numberOfSteps = $(event.target).find('.step').length - 1;
                    var line = $(event.target).find('.step');
                    for (var i = 0; i < index; i++) {
                        line[i].classList.add('crossed');

                        for (var j = index; j < numberOfSteps; j++) {
                            line[j].classList.remove('crossed');
                        }
                    }
                    if (event.detail.to == 0) {
                        for (var k = index; k < numberOfSteps; k++) {
                            line[k].classList.remove('crossed');
                        }
                        line[0].classList.remove('crossed');
                    }
                });
            }
        }
        var verticalWizard = document.querySelector('.vertical-wizard-support');
        if (typeof verticalWizard !== undefined && verticalWizard !== null) {
            var verticalStepper = new Stepper(verticalWizard, {
                linear: false
            });
            $(verticalWizard)
                .find('.btn-next')
                .on('click', function () {
                    verticalStepper.next();
                });
            $(verticalWizard)
                .find('.btn-prev')
                .on('click', function () {
                    verticalStepper.previous();
                });
        }
    </script>

    <!-- Page js files -->
    {{--<script src="{{ asset(mix('js/scripts/forms/pickers/form-pickers.js')) }}"></script>--}}

@endsection
