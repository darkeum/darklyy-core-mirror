@extends('layouts.fullLayoutMaster')

@section('title', trans('profile::lang.auth.register.title'))

@section('vendor-style')
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/extensions/nouislider.min.css')) }}">
@endsection

@section('page-style')
    {{-- Page Css files --}}
    <link rel="stylesheet" href="{{ asset(mix('css/base/plugins/forms/form-validation.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('css/base/pages/authentication.css')) }}">

    <link rel="stylesheet" href="{{ asset(mix('css/base/plugins/extensions/ext-component-sliders.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('css/base/core/colors/palette-noui.css')) }}">
@endsection

@section('content')
    <div class="auth-wrapper auth-basic px-2">
        <div class="auth-inner my-2">
            <!-- Register basic -->
            <div class="card mb-0">
                <div class="card-body">

                    @include('profile::auth-basic.layouts.brand')

                    <h4 class="card-title mb-1">@lang('profile::lang.auth.register.header') 🤝</h4>
                    <p class="card-text mb-2">@lang('profile::lang.auth.register.message')</p>

                    <form class="auth-register-form mt-2" action="{{ route('register') }}" method="POST">
                        @csrf

                        {{-- Имя пользователя --}}
                        <div class="mb-1">
                            <label for="name" class="form-label">
                                @lang('profile::lang.sidebar.username')
                            </label>
                            <input
                                    type="text"
                                    class="form-control @error('name'){{ 'is-invalid' }}@enderror"
                                    id="name"
                                    name="name"
                                    placeholder="@lang('profile::lang.sidebar.username')"
                                    data-msg="@lang('profile::lang.auth.required')"
                                    aria-describedby="name"
                                    tabindex="1"
                                    autofocus
                                    required
                            />
                            @error('name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        {{-- Имя пользователя --}}

                        {{-- Электронная почта --}}
                        <div class="mb-1">
                            <label for="email" class="form-label">
                                @lang('profile::lang.sidebar.email')
                            </label>
                            <input
                                    type="text"
                                    class="form-control @error('email'){{ 'is-invalid' }}@enderror"
                                    id="email"
                                    name="email"
                                    placeholder="@lang('profile::lang.sidebar.email')"
                                    data-msg="@lang('profile::lang.auth.required')"
                                    aria-describedby="email"
                                    tabindex="2"
                                    required
                            />
                            @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        {{-- Электронная почта --}}

                        {{-- Пароль --}}
                        <div class="mb-1">
                            <label for="password" class="form-label">
                                @lang('profile::lang.auth.login.password')
                            </label>

                            <div class="input-group input-group-merge form-password-toggle">
                                <input
                                        type="password"
                                        class="form-control form-control-merge @error('password'){{ 'is-invalid' }}@enderror"
                                        id="password"
                                        name="password"
                                        placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                                        data-msg="@lang('profile::lang.auth.required')"
                                        aria-describedby="password"
                                        tabindex="3"
                                        required
                                />
                                <span class="input-group-text cursor-pointer"><i data-feather="eye"></i></span>
                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        {{-- Пароль --}}

                        {{-- Повтор пароля --}}
                        <div class="mb-1">
                            <label for="password_confirmation" class="form-label">
                                @lang('profile::lang.auth.register.password_confirm.title')
                            </label>

                            <div class="input-group input-group-merge form-password-toggle">
                                <input
                                        type="password"
                                        class="form-control form-control-merge"
                                        id="password_confirmation"
                                        name="password_confirmation"
                                        placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                                        data-msg="@lang('profile::lang.auth.required')"
                                        {{--placeholder="@lang('profile::lang.auth.register.password_confirm.placeholder')"--}}
                                        aria-describedby="password_confirmation"
                                        autocomplete="new-password"
                                        tabindex="4"
                                        required
                                />
                                <span class="input-group-text cursor-pointer"><i data-feather="eye"></i></span>
                            </div>
                        </div>
                        {{-- Повтор пароля --}}

                        {{-- СТОП --}}
                        <div class="mt-1 mb-4">
                            <label for="" class="form-label">
                                Укажите число <b class="rand-step">{{ decimalRand(10, 100, 10)  }}</b>
                            </label>

                            <div id="pips-range" class="mt-50 mb-3"></div>

                        </div>
                        {{-- СТОП --}}

                        {{-- Условия использования --}}
                        <div class="mb-1">
                            <div class="form-check">
                                <input class="form-check-input"
                                       id="agree"
                                       name="agree"
                                       type="checkbox"
                                       tabindex="5"
                                />
                                <label class="form-check-label" for="agree">
                                    @lang('profile::lang.auth.register.agree', ['url' => '#'])
                                </label>
                            </div>
                        </div>
                        {{-- Условия использования --}}

                        {{-- Зарегистрироваться --}}
                        <button id="register" class="btn btn-primary w-100" tabindex="5" disabled>
                            @lang('profile::lang.auth.register.sing_up')
                        </button>
                        {{-- Зарегистрироваться --}}

                    </form>

                    <p class="text-center mt-2">
                        <span>@lang('profile::lang.auth.register.already')</span>
                        <a class="w-100" href="{{ route('login') }}">
                            <span>@lang('profile::lang.auth.login.sing_in')</span>
                        </a>
                    </p>

                    {{-- social --}}
                    @include('profile::auth-basic.layouts.social')
                    {{-- social --}}

                </div>
            </div>
            <!--/ Register basic -->
        </div>
    </div>
@endsection

@section('vendor-script')
    <script src="{{asset('vendors/js/forms/validation/jquery.validate.min.js')}}"></script>
    <script src="{{ asset(mix('vendors/js/extensions/wNumb.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/extensions/nouislider.min.js')) }}"></script>
@endsection

@section('page-script')
    <script src="{{asset('js/scripts/pages/auth-register.js')}}"></script>

    <script>
        var pipsRange = document.getElementById('pips-range');
        if (typeof pipsRange !== undefined && pipsRange !== null) {
            // Range
            noUiSlider.create(pipsRange, {
                start: 0,
                step: 10,
                range: {
                    min: 0,
                    max: 100
                },
                format: wNumb({
                    decimals: 0
                }),
                // tooltips: true,
                // direction: direction,
                pips: {
                    mode: 'steps',
                    stepped: true,
                    density: 10
                }
            });
        }

        pipsRange.noUiSlider.on('change', function( values, handle ) {
            let rand = $('.rand-step').html();
            if (rand === values[0]) {
                $('#register').prop('disabled', false);
            } else {
                $('#register').prop('disabled', true);
            }
        });

    </script>
@endsection