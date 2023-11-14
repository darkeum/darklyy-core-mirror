@extends('profile::layout')

@section('content')
<section class="fxt-template-animation fxt-template-layout33">
    <div class="fxt-content-wrap">
        <div class="fxt-heading-content">
            <div class="fxt-inner-wrap fxt-transformX-R-50 fxt-transition-delay-3">
                <div class="fxt-transformX-R-50 fxt-transition-delay-10">
                    <a href="{{ url('/') }}" class="fxt-logo">
                        <img src="/usr/modules/profile/UI/WEB/Resources/Assets/images/logo-33.png" alt="Logo">
                    </a>
                </div> 
                <div class="fxt-transformX-R-50 fxt-transition-delay-10">
                    <div class="fxt-middle-content">
                        <div class="fxt-sub-title">
                            {{ __('profile::auth.login') }}
                        </div>
                        <h1 class="fxt-main-title">
                            {{ __('profile::auth.lk') }}
                        </h1>
                        <p class="fxt-description">
                            {{ __('profile::auth.about') }}
                        </p>
                    </div>
                </div>
                <div class="fxt-transformX-R-50 fxt-transition-delay-10">
                    <div class="fxt-switcher-description">
                        {{ __('profile::auth.dont_have_account') }}
                        <a href="{{ route('register') }}" class="fxt-switcher-text">
                            {{ __('profile::auth.sign_up') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="fxt-form-content">
            <div class="fxt-main-form">
                <div class="fxt-inner-wrap fxt-opacity fxt-transition-delay-13">
                    <h2 class="fxt-page-title">
                        {{ __('profile::auth.login') }}
                    </h2>
                    <p class="fxt-description">
                        {{ __('profile::auth.services') }}
                    </p>
                    <form method="POST" action="{{ route('login') }}">
                        @csrf
                        <div class="form-group">
                            <label for="email" class="fxt-label">
                                {{ __('profile::auth.email.label') }}
                            </label>
                            <input type="email" id="email" class="form-control @error('email') is-invalid @enderror"
                                name="email" value="{{ old('email') }}"
                                placeholder="{{ __('profile::auth.email.placeholder') }}" required="required">
                            @error('email')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="password" class="fxt-label">
                                {{ __('profile::auth.password.label') }}
                            </label>
                            <input id="password" type="password"
                                class="form-control @error('password') is-invalid @enderror" name="password"
                                placeholder="{{ __('profile::auth.password.placeholder') }}" required="required">
                            @error('password')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                        <div class="form-group">
                            <div class="fxt-checkbox-box">
                                <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : ''
                                    }}>
                                <label for="remember" class="ps-4"> {{ __('profile::auth.remember') }}</label>
                            </div>
                        </div>
                        @if (Route::has('password.request'))
                        <div class="form-group">
                            <a href="{{ route('password.request') }}" class="fxt-switcher-text">
                                {{ __('profile::auth.forgot') }}
                            </a>
                        </div>
                        @endif
                        <div class="form-group mb-3">
                            <button type="submit" class="fxt-btn-fill">
                                {{ __('profile::auth.sign_in') }}
                            </button>
                        </div>
                    </form>
                    <div class="fxt-divider-text text-uppercase">
                        {{ __('profile::auth.or') }}
                    </div>
                    <div id="fxt-login-option" class="fxt-login-option">
                        <ul>
                            <li class="fxt-vk active">
                                <a href="#">
                                    <span class="fxt-option-icon">
                                        <i class="fab fa-vk"></i>
                                    </span>
                                    <span class="fxt-option-text ml-2">
                                        {{ __('profile::auth.social.vk') }}
                                    </span>
                                </a>
                            </li>
                            <li class="fxt-yandex">
                                <a href="#">
                                    <span class="fxt-option-icon">
                                        <i class="fab fa-yandex"></i>
                                    </span>
                                    <span class="fxt-option-text ml-2">
                                        {{ __('profile::auth.social.yandex') }}
                                    </span>
                                </a>
                            </li>
                            <li class="fxt-google">
                                <a href="#">
                                    <span class="fxt-option-icon">
                                        <i class="fab fa-google"></i>
                                    </span>
                                    <span class="fxt-option-text ml-2">
                                        {{ __('profile::auth.social.google') }}
                                    </span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Reset Password') }}</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('password.update') }}">
                        @csrf

                        <input type="hidden" name="token" value="{{ $token }}">

                        <div class="row mb-3">
                            <label for="email" class="col-md-4 col-form-label text-md-end">{{ __('Email Address') }}</label>

                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ $email ?? old('email') }}" required autocomplete="email" autofocus>

                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="password" class="col-md-4 col-form-label text-md-end">{{ __('Password') }}</label>

                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">

                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="password-confirm" class="col-md-4 col-form-label text-md-end">{{ __('Confirm Password') }}</label>

                            <div class="col-md-6">
                                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password">
                            </div>
                        </div>

                        <div class="row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Reset Password') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
