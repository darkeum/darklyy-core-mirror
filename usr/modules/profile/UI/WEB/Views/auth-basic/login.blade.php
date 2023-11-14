@extends('layouts.fullLayoutMaster')

@section('title', trans('profile::lang.auth.login.title'))

@section('page-style')
    {{-- Page Css files --}}
    <link rel="stylesheet" href="{{ asset(mix('css/base/plugins/forms/form-validation.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('css/base/pages/authentication.css')) }}">
@endsection

@section('content')
    <div class="auth-wrapper auth-basic px-2">
        <div class="auth-inner my-2">
            <!-- Login basic -->
            <div class="card mb-0">
                <div class="card-body">

                    {{-- brand --}}
                    @include('profile::auth-basic.layouts.brand')
                    {{-- brand --}}

                    <h4 class="card-title mb-1">@lang('profile::lang.auth.login.header')! 👋</h4>
                    <p class="card-text mb-2">@lang('profile::lang.auth.login.message')</p>

                    <form class="auth-login-form mt-2" action="{{ route('login') }}" method="POST">
                        @csrf

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
                                    tabindex="1"
                                    value="{{ old('email') }}"
                                    autofocus
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

                            @if (Route::has('password.request'))
                                <div class="d-flex justify-content-between">
                                    <label class="form-label" for="password">
                                        @lang('profile::lang.auth.login.password')
                                    </label>
                                    <a href="{{ route('password.request') }}">
                                        <small>
                                            @lang('profile::lang.auth.forgot.header')
                                        </small>
                                    </a>
                                </div>
                            @endif

                            <div class="input-group input-group-merge form-password-toggle">
                                <input
                                        type="password"
                                        class="form-control form-control-merge @error('password'){{ 'is-invalid' }}@enderror"
                                        id="password"
                                        name="password"
                                        tabindex="2"
                                        placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                                        data-msg="@lang('profile::lang.auth.required')"
                                        aria-describedby="password"
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

                        {{-- Запомнить пароль --}}
                        <div class="mb-1">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox"
                                       id="remember" name="remember" tabindex="3"
                                        {{ old('remember') ? 'checked' : '' }}
                                />
                                <label class="form-check-label" for="remember">
                                    @lang('profile::lang.auth.login.remember')
                                </label>
                            </div>
                        </div>
                        {{-- Запомнить пароль --}}

                        {{-- Войти --}}
                        <button class="btn btn-primary w-100" tabindex="4">
                            @lang('profile::lang.auth.login.sing_in')
                        </button>
                        {{-- Войти --}}

                    </form>

                    <p class="text-center mt-2">
                        <span>@lang('profile::lang.auth.login.novice')</span>
                        <a href="{{ route('register') }}">
                            <span>
                                @lang('profile::lang.auth.login.create')
                            </span>
                        </a>
                    </p>

                    {{-- social --}}
                    @include('profile::auth-basic.layouts.social')
                    {{-- social --}}

                </div>
            </div>
            <!-- /Login basic -->
        </div>
    </div>
@endsection

@section('vendor-script')
    <script src="{{ asset(mix('vendors/js/forms/validation/jquery.validate.min.js')) }}"></script>
@endsection

@section('page-script')
    <script src="{{ asset(mix('js/scripts/pages/auth-login.js')) }}"></script>
@endsection