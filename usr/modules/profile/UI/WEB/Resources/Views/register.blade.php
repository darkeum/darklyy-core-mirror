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
                                {{ __('profile::auth.register') }}
                            </div>
                            <h1 class="fxt-main-title">
                                {{ __('profile::auth.lk') }}
                            </h1>
                            <p class="fxt-description">
                                {{ __('profile::auth.about_register') }}
                            </p>
                        </div>
                    </div>
                    <div class="fxt-transformX-R-50 fxt-transition-delay-10">
                        <div class="fxt-switcher-description">
                            {{ __('profile::auth.have_account') }}
                            <a href="{{ route('login') }}" class="fxt-switcher-text">
                                {{ __('profile::auth.sign_in') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="fxt-form-content">
                <div class="fxt-main-form">
                    <div class="fxt-inner-wrap fxt-opacity fxt-transition-delay-13">
                        <h2 class="fxt-page-title">
                            {{ __('profile::auth.register') }}
                        </h2>
                        <p class="fxt-description">
                            {{ __('profile::auth.services_register') }}
                        </p>
                        <form method="POST" action="{{ route('register') }}">
                            @csrf
                            <div class="form-group">
                                <label for="name" class="fxt-label">
                                    {{ __('profile::auth.name.label') }}
                                </label>
                                <input type="text" id="name" class="form-control @error('name') is-invalid @enderror"
                                       name="name" value="{{ old('name') }}"
                                       placeholder="{{ __('profile::auth.name.placeholder') }}" required="required"/>
                                @error('name')
                                <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="email" class="fxt-label">
                                    {{ __('profile::auth.email.label') }}
                                </label>
                                <input type="email" id="email" class="form-control @error('email') is-invalid @enderror"
                                       name="email" value="{{ old('email') }}"
                                       placeholder="{{ __('profile::auth.email.placeholder') }}" required="required"/>
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
                                       placeholder="{{ __('profile::auth.password.placeholder') }}" required="required"
                                       autocomplete="new-password"/>
                                @error('password')
                                <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="password-confirm" class="fxt-label">
                                    {{ __('profile::auth.password-confirm.label') }}
                                </label>
                                <input id="password-confirm" type="password"
                                       class="form-control" name="password_confirmation"
                                       placeholder="{{ __('profile::auth.password-confirm.placeholder') }}"
                                       required="required" autocomplete="new-password"/>
                            </div>
                            <div class="form-group">
                                <div class="fxt-checkbox-box">
                                    <input id="agree" type="checkbox">
                                    <label for="agree" class="ps-4"> {{ __('profile::auth.agree') }}</label>
                                </div>
                            </div>
                            <div class="form-group mb-3">
                                <button type="submit" class="fxt-btn-fill">
                                    {{ __('profile::auth.sign_up') }}
                                </button>
                            </div>
                        </form>
                        <div class="fxt-divider-text text-uppercase">
                            {{ __('profile::auth.or') }}
                        </div>
                        <div id="fxt-login-option" class="fxt-login-option">
                            <ul>
                                <li class="fxt-vk active">
                                    <a href="{{ route('social.redirect','vk') }}">
                                    <span class="fxt-option-icon">
                                        <i class="fab fa-vk"></i>
                                    </span>
                                        <span class="fxt-option-text ml-2">
                                        {{ __('profile::auth.social.vk') }}
                                    </span>
                                    </a>
                                </li>
                                <li class="fxt-yandex">
                                    <a href="{{ route('social.redirect','yandex') }}">
                                    <span class="fxt-option-icon">
                                        <i class="fab fa-yandex"></i>
                                    </span>
                                        <span class="fxt-option-text ml-2">
                                        {{ __('profile::auth.social.yandex') }}
                                    </span>
                                    </a>
                                </li>
                                <li class="fxt-google">
                                    <a href="{{ route('social.redirect','google') }}">
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
@endsection
