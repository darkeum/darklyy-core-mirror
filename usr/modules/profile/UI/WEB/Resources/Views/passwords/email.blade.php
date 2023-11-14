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
                            {{ __('profile::auth.reset_password') }}
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
                        {{ __('profile::auth.reset_password') }}
                    </h2>
                    <p class="fxt-description">
                        {{ __('profile::auth.services_reset') }}
                    </p>
                    @if (session('status'))
                    <div class="alert alert-success" role="alert">
                        {{ session('status') }}
                    </div>
                    @endif
                    <form method="POST" action="{{ route('password.email') }}">
                        @csrf
                        <div class="form-group mb-3">
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
                        <div class="form-group mb-3">
                            <button type="submit" class="fxt-btn-fill">
                                {{ __('profile::auth.reset') }}
                            </button>
                        </div>
                    </form>
                    <div class="fxt-switcher-description">{{ __('profile::auth.return') }}<a href="{{ route('login') }}"
                            class="fxt-switcher-text ms-1">{{ __('profile::auth.sign_in') }}</a></div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection