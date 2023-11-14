@extends('profile::manager.body')

@section('title', trans('profile::lang.module') . ' - ' . trans('profile::lang.security.title'))

@section('content')
    <section class="app-user-view-security">
        <div class="row">
            {{-- User Sidebar --}}
            @include('profile::manager.layouts.sidebar')
            {{--/ User Sidebar --}}

            {{-- User Content --}}
            <div class="col-xl-8 col-lg-7 col-md-7 order-0 order-md-1">
                {{-- User Pills --}}
                @include('profile::manager.layouts.pills')
                {{--/ User Pills --}}

                {{-- Change Password --}}
                <div class="card">
                    <h4 class="card-header">Изменить пароль</h4>
                    <div class="card-body">

                        <form id="formChangePassword" method="POST" onsubmit="return false">

                            <div class="alert alert-warning mb-2" role="alert">
                                <h6 class="alert-heading">
                                    Убедитесь, что эти требования соблюдены
                                </h6>
                                <div class="alert-body fw-normal">
                                    Минимум 8 символов длиной, верхний регистр и символ
                                </div>
                            </div>

                            <div class="row">

                                <div class="mb-2 col-md-6 form-password-toggle">
                                    <label class="form-label" for="basic-default-password">Новый пароль</label>
                                    <div class="input-group input-group-merge form-password-toggle">
                                        <input
                                            class="form-control"
                                            type="password"
                                            id="basic-default-password"
                                            name="basic-default-password"
                                            placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                                        />
                                        <span class="input-group-text cursor-pointer">
                                            <i data-feather="eye"></i>
                                        </span>
                                    </div>
                                </div>
                                <div class="mb-2 col-md-6 form-password-toggle">
                                    <label class="form-label" for="confirm-password">Подтвердите новый пароль</label>
                                    <div class="input-group input-group-merge">
                                        <input
                                            class="form-control"
                                            type="password"
                                            name="confirm-password"
                                            id="confirm-password"
                                            placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                                        />
                                        <span class="input-group-text cursor-pointer"><i data-feather="eye"></i></span>
                                    </div>
                                </div>

                                <div>
                                    <button type="submit" class="btn btn-primary me-2">
                                        Изменить пароль
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                {{--/ Change Password --}}

            </div>
            {{--/ User Content --}}
        </div>
    </section>


    {{--@include('content/_partials/_modals/modal-edit-user')--}}
    {{--@include('content/_partials/_modals/modal-upgrade-plan')--}}
    {{--@include('content/_partials/_modals/modal-two-factor-auth')--}}
@endsection

@section('page-style')
    {{-- Page Css files --}}
    <link rel="stylesheet" href="{{ asset(mix('css/base/plugins/forms/form-validation.css')) }}">
    {{--<link rel="stylesheet" href="{{ asset(mix('css/base/plugins/extensions/ext-component-sweet-alerts.css')) }}">--}}
@endsection

@section('vendor-script')
    <!-- vendor files -->
    <script src="{{ asset(mix('vendors/js/forms/validation/jquery.validate.min.js')) }}"></script>
    <script src="{{ asset('vendors/js/forms/validation/localization/messages_ru.min.js') }}"></script>
@endsection

@section('page-script')
    {{-- Page js files --}}
    {{--<script src="{{ asset(mix('js/scripts/pages/modal-two-factor-auth.js')) }}"></script>--}}
    {{--<script src="{{ asset(mix('js/scripts/pages/modal-edit-user.js')) }}"></script>--}}
    {{--<script src="{{ asset(mix('js/scripts/pages/app-user-view-security.js')) }}"></script>--}}
    {{--<script src="{{ asset(mix('js/scripts/pages/app-user-view.js')) }}"></script>--}}

    <script>
        var jqForm = $('#formChangePassword');
        if (jqForm.length) {
            jqForm.validate({
                rules: {
                    'basic-default-password': {
                        required: true
                    },
                    'confirm-password': {
                        required: true,
                        equalTo: '#basic-default-password'
                    },
                    validationCheck: {
                        required: true
                    }
                }
            });
        }
    </script>
@endsection