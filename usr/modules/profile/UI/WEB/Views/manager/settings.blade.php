@extends('profile::manager.body')

@section('title', trans('profile::lang.module') . ' - ' . trans('profile::lang.settings.title'))

@section('content')
    <section class="app-user-view-security">
        <div class="row">

        {{-- User Sidebar --}}
        @include('profile::manager.layouts.sidebar')
        {{--/ User Sidebar --}}

        <!-- User Content -->
            <div class="col-xl-8 col-lg-7 col-md-7 order-0 order-md-1">

                {{-- User Pills --}}
                @include('profile::manager.layouts.pills')
                {{--/ User Pills --}}

                <div class="card">
                    <div class="card-body">
                        <div class="alert alert-secondary m-0" role="alert">
                            <div class="alert-body"><strong>Функционал находится в разработке!</strong> Мы работаем над ним и скоро он появится!</div>
                        </div>
                    </div>
                </div>

            </div>
            <!--/ User Content -->
        </div>
    </section>

    {{--    @include('content/_partials/_modals/modal-edit-user')--}}
    {{--    @include('content/_partials/_modals/modal-upgrade-plan')--}}
    {{--    @include('content/_partials/_modals/modal-two-factor-auth')--}}
@endsection

@section('page-style')
    {{-- Page Css files --}}
    <link rel="stylesheet" href="{{ asset(mix('css/base/plugins/forms/form-validation.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('css/base/plugins/extensions/ext-component-sweet-alerts.css')) }}">
@endsection

@section('page-script')
    {{-- Page js files --}}
    <script src="{{ asset(mix('js/scripts/pages/modal-two-factor-auth.js')) }}"></script>
    <script src="{{ asset(mix('js/scripts/pages/modal-edit-user.js')) }}"></script>
    <script src="{{ asset(mix('js/scripts/pages/app-user-view-security.js')) }}"></script>
    <script src="{{ asset(mix('js/scripts/pages/app-user-view.js')) }}"></script>
@endsection