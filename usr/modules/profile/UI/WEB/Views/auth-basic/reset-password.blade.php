@extends('layouts.fullLayoutMaster')

@section('title', trans('profile::lang.auth.reset.title'))

@section('page-style')
    {{-- Page Css files --}}
    <link rel="stylesheet" href="{{ asset(mix('css/base/plugins/forms/form-validation.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('css/base/pages/authentication.css')) }}">
@endsection

@section('content')
    <div class="auth-wrapper auth-basic px-2">
        <div class="auth-inner my-2">
            <!-- Reset Password basic -->
            <div class="card mb-0">
                <div class="card-body">

                    {{-- brand --}}
                    @include('profile::auth-basic.layouts.brand')
                    {{-- brand --}}

                    <h4 class="card-title mb-1">@lang('profile::lang.auth.reset.header') 🔒</h4>
                    <p class="card-text mb-2">@lang('profile::lang.auth.reset.message')</p>

                    <form class="auth-reset-password-form mt-2" action="{{ route('password.update') }}" method="POST">
                        @csrf
                        <input type="hidden" name="token" value="{{ $token }}">

                        {{-- Новый пароль --}}
                        <div class="mb-1">
                            <input id="email" type="hidden" class="form-control" name="email" value="{{ $email }}" />
                            <label for="password" class="form-label">
                                @lang('profile::lang.auth.reset.password')
                            </label>

                            <div class="input-group input-group-merge form-password-toggle">
                                <input type="password" class="form-control form-control-merge @error('password'){{ 'is-invalid' }}@enderror" id="password" name="password"
                                    placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" data-msg="@lang('profile::lang.auth.required')" aria-describedby="password" tabindex="1" required autofocus />
                                <span class="input-group-text cursor-pointer"><i data-feather="eye"></i></span>
                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        {{-- Новый пароль --}}

                        {{-- Повтор пароля --}}
                        <div class="mb-1">
                            <label for="password_confirmation" class="form-label">
                                @lang('profile::lang.auth.register.password_confirm.title')
                            </label>

                            <div class="input-group input-group-merge form-password-toggle">
                                <input type="password" class="form-control form-control-merge" id="password_confirmation" name="password_confirmation" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                                    data-msg="@lang('profile::lang.auth.required')" aria-describedby="password_confirmation" autocomplete="new-password" tabindex="2" required />
                                <span class="input-group-text cursor-pointer"><i data-feather="eye"></i></span>
                            </div>
                        </div>
                        {{-- Повтор пароля --}}

                        <button class="btn btn-primary w-100" tabindex="3">
                            @lang('profile::lang.auth.reset.set_new_password')
                        </button>
                    </form>

                    <p class="text-center mt-2">
                        <a href="{{ route('login') }}"> <i data-feather="chevron-left"></i>
                            @lang('profile::lang.auth.forgot.back')
                        </a>
                    </p>

                    {{-- social --}}
                    @include('profile::auth-basic.layouts.social')
                    {{-- social --}}

                </div>
            </div>
            <!-- /Reset Password basic -->
        </div>
    </div>
@endsection

@section('vendor-script')
    <script src="{{ asset(mix('vendors/js/forms/validation/jquery.validate.min.js')) }}"></script>
@endsection

@section('page-script')
    <script src="{{ asset(mix('js/scripts/pages/auth-reset-password.js')) }}"></script>
@endsection
