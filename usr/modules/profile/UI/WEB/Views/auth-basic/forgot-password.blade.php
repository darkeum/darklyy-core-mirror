@extends('layouts.fullLayoutMaster')

@section('title', trans('profile::lang.auth.forgot.title'))

@section('page-style')
    {{-- Page Css files --}}
    <link rel="stylesheet" href="{{ asset(mix('css/base/plugins/forms/form-validation.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('css/base/pages/authentication.css')) }}">
@endsection

@section('content')
    <div class="auth-wrapper auth-basic px-2">
        <div class="auth-inner my-2">
            <!-- Forgot Password basic -->
            <div class="card mb-0">
                <div class="card-body">

                    {{-- brand --}}
                    @include('profile::auth-basic.layouts.brand')
                    {{-- brand --}}

                    <h4 class="card-title mb-1">@lang('profile::lang.auth.forgot.header') 🔒</h4>
                    <p class="card-text mb-2">@lang('profile::lang.auth.forgot.message')</p>

                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            <div class="alert-body">
                                {{ session('status') }}
                            </div>
                        </div>
                    @endif

                    <form class="auth-forgot-password-form mt-2" action="{{ route('password.email') }}" method="POST">
                        @csrf
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
                                    autofocus
                                    required
                            />
                            @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <button class="btn btn-primary w-100" tabindex="2">
                            @lang('profile::lang.auth.forgot.reset')
                        </button>
                    </form>

                    <p class="text-center mt-2">
                        <a href="{{ route('login') }}"> <i data-feather="chevron-left"></i>
                            @lang('profile::lang.auth.forgot.back')
                        </a>
                    </p>
                </div>
            </div>
            <!-- /Forgot Password basic -->
        </div>
    </div>
@endsection

@section('vendor-script')
    <script src="{{ asset(mix('vendors/js/forms/validation/jquery.validate.min.js')) }}"></script>
@endsection

@section('page-script')
    <script src="{{ asset(mix('js/scripts/pages/auth-forgot-password.js')) }}"></script>
@endsection
