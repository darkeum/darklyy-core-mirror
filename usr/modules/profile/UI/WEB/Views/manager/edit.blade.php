@extends('profile::manager.body')

@section('title', trans('profile::lang.module') . ' - ' . trans('profile::lang.edit.title'))

@section('content')
    <section class="app-user-view-account">
        <div class="row">
            {{-- User Sidebar --}}
            @include('profile::manager.layouts.sidebar')
            {{--/ User Sidebar --}}

            {{-- User Content --}}
            <div class="col-xl-8 col-lg-7 col-md-7 order-0 order-md-1">

                {{-- User Pills --}}
                @include('profile::manager.layouts.pills')
                {{--/ User Pills --}}

                {{-- Profile Edit --}}
                <div class="card">
                    <h4 class="card-header">Редактировать информацию о пользователе</h4>
                    <div class="card-body">
                        <div class="mb-2">
                            <p>Обновляющие данные пользователя получат аудит конфиденциальности.</p>
                        </div>

                        <form id="editUserForm" class="row gy-1 pt-75" onsubmit="return false">

                            {{-- Фамилия --}}
                            <div class="col-12 col-md-4">
                                <label class="form-label" for="editUserLastName">
                                    @lang('profile::lang.edit.l_name')
                                </label>
                                <input
                                        type="text"
                                        id="editUserLastName"
                                        name="editUserLastName"
                                        class="form-control"
                                        placeholder="@lang('profile::lang.edit.l_name')"
                                        value="{{ $user->surname }}"
                                        data-msg="@lang('profile::lang.edit.place.title', ['place' => trans('profile::lang.edit.place.l_name')])"
                                />
                            </div>
                            {{--/ Фамилия --}}

                            {{-- Имя --}}
                            <div class="col-12 col-md-4">
                                <label class="form-label" for="editUserFirstName">
                                    @lang('profile::lang.edit.f_name')
                                </label>
                                <input
                                        type="text"
                                        id="editUserFirstName"
                                        name="editUserFirstName"
                                        class="form-control"
                                        placeholder="@lang('profile::lang.edit.f_name')"
                                        value="{{ $user->name }}"
                                        data-msg="@lang('profile::lang.edit.place.title', ['place' => trans('profile::lang.edit.place.f_name')])"
                                />
                            </div>
                            {{--/ Имя --}}

                            {{-- Отчество --}}
                            <div class="col-12 col-md-4">
                                <label class="form-label" for="editUserMiddleName">
                                    @lang('profile::lang.edit.m_name')
                                </label>
                                <input
                                        type="text"
                                        id="editUserMiddleName"
                                        name="editUserMiddleName"
                                        class="form-control"
                                        placeholder="@lang('profile::lang.edit.m_name')"
                                        value="{{ $user->ochestvo }}"
                                        data-msg="@lang('profile::lang.edit.place.title', ['place' => trans('profile::lang.edit.place.m_name')])"
                                />
                            </div>
                            {{--/ Отчество --}}

                            {{-- Имя пользователя --}}
                            <div class="col-6 col-md-6">
                                <label class="form-label" for="nick">
                                    @lang('profile::lang.sidebar.username')
                                </label>
                                <input
                                        type="text"
                                        id="nick"
                                        class="form-control"
                                        value="{{ stristr($user->nick, '@', true) }}"
                                        placeholder="@lang('profile::lang.sidebar.username')"
                                        readonly
                                        disabled
                                />
                            </div>
                            {{-- Имя пользователя --}}

                            {{-- Электронная почта --}}
                            <div class="col-6 col-md-6">
                                <label class="form-label" for="email">
                                    @lang('profile::lang.sidebar.email')
                                </label>
                                <input
                                        type="text"
                                        id="email"
                                        class="form-control"
                                        value="{{ $user->email }}"
                                        placeholder="@lang('profile::lang.sidebar.email')"
                                        readonly
                                        disabled
                                />
                            </div>
                            {{--/ Электронная почта --}}

                            {{-- Идентификатор --}}
                            <div class="col-12 col-md-6">
                                <label class="form-label" for="id">
                                    @lang('profile::lang.sidebar.id')
                                </label>
                                <input
                                        type="text"
                                        id="id"
                                        class="form-control"
                                        placeholder="@lang('profile::lang.sidebar.id')"
                                        value="{{ $user->id }}"
                                        disabled
                                />
                            </div>
                            {{--/ Идентификатор --}}

                            {{-- Телефон --}}
                            <div class="col-12 col-md-6">
                                <label class="form-label" for="phone">
                                    @lang('profile::lang.sidebar.tel')
                                </label>
                                <input
                                        type="text"
                                        id="phone"
                                        name="phone"
                                        class="form-control phone-number-mask"
                                        placeholder="{{ isset($user->phone) ? trans('profile::lang.sidebar.tel') : trans('profile::lang.sidebar.unspec') }}"
                                        {{--placeholder="+7 (999) 999-99-99"--}}
                                        value="{{ $user->phone }}"
                                />
                            </div>
                            {{--/ Телефон --}}

                            {{-- Статус - Для админа оставить? --}}
                            @can('support manager')
                                <div class="col-12 col-md-6 manager-zone">
                                    <label class="form-label" for="active">
                                        @lang('profile::lang.sidebar.status.title')
                                    </label>

                                    {{--<div class="col-md-12 btn-group" role="group" aria-label="Basic radio toggle button group">--}}
                                    {{--    <input type="radio" class="btn-check" name="btnradio" id="btnradio1" autocomplete="off" checked />--}}
                                    {{--    <label class="btn btn-outline-success" for="btnradio1">--}}
                                    {{--        @lang('profile::lang.sidebar.status.active')--}}
                                    {{--    </label>--}}

                                    {{--    <input type="radio" class="btn-check" name="btnradio" id="btnradio2" autocomplete="off" />--}}
                                    {{--    <label class="btn btn-outline-danger" for="btnradio2">--}}
                                    {{--        @lang('profile::lang.sidebar.status.not_active')--}}
                                    {{--    </label>--}}
                                    {{--</div>--}}

                                    <select id="active" name="active" class="form-select select"
                                            aria-label="">
                                        {{--{{ trans('profile::lang.sidebar.status.'. (isset($user->active) ? '' : 'not_') .'active') }}--}}
                                        {{--<option selected>@lang('profile::lang.sidebar.status.title')</option>--}}

                                        <option {{ isset($user->active) ? 'selected' : '' }} value="1">
                                            @lang('profile::lang.sidebar.status.active')
                                        </option>
                                        <option value="0">
                                            @lang('profile::lang.sidebar.status.not_active')
                                        </option>
                                    </select>
                                </div>
                            @endcan
                            {{--/ Статус --}}

                            {{-- Языки --}}
                            {{--
                            <div class="col-12 col-md-6">
                                <label class="form-label" for="editUserLanguage">Language</label>
                                <select id="editUserLanguage" name="editUserLanguage" class="select2 form-select" multiple>
                                    <option value="english">English</option>
                                    <option value="spanish">Spanish</option>
                                    <option value="french">French</option>
                                    <option value="german">German</option>
                                    <option value="dutch">Dutch</option>
                                    <option value="hebrew">Hebrew</option>
                                    <option value="sanskrit">Sanskrit</option>
                                    <option value="hindi">Hindi</option>
                                </select>
                            </div>
                            --}}
                            {{--/ Языки --}}

                            {{-- Страна --}}
                            {{--
                            <div class="col-12 col-md-6">
                                <label class="form-label" for="editUserCountry">Country</label>
                                <select id="editUserCountry" name="editUserCountry" class="select2 form-select">
                                    <option value="">Select Value</option>
                                    <option value="Australia">Australia</option>
                                    <option value="Bangladesh">Bangladesh</option>
                                    <option value="Belarus">Belarus</option>
                                    <option value="Brazil">Brazil</option>
                                    <option value="Canada">Canada</option>
                                    <option value="China">China</option>
                                    <option value="France">France</option>
                                    <option value="Germany">Germany</option>
                                    <option value="India">India</option>
                                    <option value="Indonesia">Indonesia</option>
                                    <option value="Israel">Israel</option>
                                    <option value="Italy">Italy</option>
                                    <option value="Japan">Japan</option>
                                    <option value="Korea">Korea, Republic of</option>
                                    <option value="Mexico">Mexico</option>
                                    <option value="Philippines">Philippines</option>
                                    <option value="Russia">Russian Federation</option>
                                    <option value="South Africa">South Africa</option>
                                    <option value="Thailand">Thailand</option>
                                    <option value="Turkey">Turkey</option>
                                    <option value="Ukraine">Ukraine</option>
                                    <option value="United Arab Emirates">United Arab Emirates</option>
                                    <option value="United Kingdom">United Kingdom</option>
                                    <option value="United States">United States</option>
                                </select>
                            </div>
                            --}}
                            {{--/ Страна --}}

                            {{-- Переключатель --}}
                            {{--
                            <div class="col-12">
                                <div class="d-flex align-items-center mt-1">
                                    <div class="form-check form-switch form-check-primary">
                                        <input type="checkbox" class="form-check-input" id="customSwitch10" checked />
                                        <label class="form-check-label" for="customSwitch10">
                                            <span class="switch-icon-left"><i data-feather="check"></i></span>
                                            <span class="switch-icon-right"><i data-feather="x"></i></span>
                                        </label>
                                    </div>
                                    <label class="form-check-label fw-bolder" for="customSwitch10">
                                        @lang('profile::lang.edit.toggle')
                                    </label>
                                </div>
                            </div>
                            --}}
                            {{--/ Переключатель --}}

                            {{-- Кнопки действия --}}
                            <div class="col-12 text-center mt-2 pt-50">
                                <button type="submit" class="btn btn-primary me-1">
                                    @lang('profile::lang.edit.save')
                                </button>
                                <button type="reset" class="btn btn-outline-secondary" data-bs-dismiss="modal"
                                        aria-label="Close">
                                    @lang('profile::lang.edit.discard')
                                </button>
                            </div>
                            {{--/ Кнопки действия --}}
                        </form>

                    </div>
                </div>
                {{--/ Profile Edit --}}

            </div>
            {{--/ User Content --}}
        </div>
    </section>

    @include('content/_partials/_modals/modal-edit-user')
    @include('content/_partials/_modals/modal-upgrade-plan')
@endsection

@section('page-style')
    {{-- Page Css files --}}
    <link rel="stylesheet" href="{{ asset(mix('css/base/plugins/forms/form-validation.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('css/base/plugins/extensions/ext-component-sweet-alerts.css')) }}">
@endsection

@section('page-script')
    {{-- Page js files --}}
    <script src="{{ asset(mix('js/scripts/pages/modal-edit-user.js')) }}"></script>
    <script src="{{ asset(mix('js/scripts/pages/app-user-view-account.js')) }}"></script>
    <script src="{{ asset(mix('js/scripts/pages/app-user-view.js')) }}"></script>
@endsection