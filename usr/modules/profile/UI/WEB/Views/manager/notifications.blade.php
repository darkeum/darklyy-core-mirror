@extends('profile::manager.body')

@section('title', trans('profile::lang.module') . ' - ' . trans('profile::lang.notifications.title'))

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

                <div class="card">
                    <div class="card-body">
                        <div class="alert alert-secondary m-0" role="alert">
                            <div class="alert-body"><strong>Функционал находится в разработке!</strong> Мы работаем над ним и скоро он появится!</div>
                        </div>
                    </div>
                </div>

                {{-- notifications -}}
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title mb-50">
                            @lang('profile::lang.notifications.title')
                        </h4>
                        <p class="mb-0">
                            @lang('profile::lang.notifications.desc')
                        </p>
                    </div>
                    <div class="table-responsive">
                        <table class="table text-nowrap text-center border-bottom">
                            <thead>
                            <tr>
                                <th class="text-start">Тип</th>
                                <th>Email</th>
                                <th>Browser</th>
                                <th>App</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td class="text-start">New for you</td>
                                <td>
                                    <div class="form-check d-flex justify-content-center">
                                        <input class="form-check-input" type="checkbox" id="defaultCheck1" checked/>
                                    </div>
                                </td>
                                <td>
                                    <div class="form-check d-flex justify-content-center">
                                        <input class="form-check-input" type="checkbox" id="defaultCheck2"/>
                                    </div>
                                </td>
                                <td>
                                    <div class="form-check d-flex justify-content-center">
                                        <input class="form-check-input" type="checkbox" id="defaultCheck3"/>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td class="text-start">Account activity</td>
                                <td>
                                    <div class="form-check d-flex justify-content-center">
                                        <input class="form-check-input" type="checkbox" id="defaultCheck4"/>
                                    </div>
                                </td>
                                <td>
                                    <div class="form-check d-flex justify-content-center">
                                        <input class="form-check-input" type="checkbox" id="defaultCheck5" checked/>
                                    </div>
                                </td>
                                <td>
                                    <div class="form-check d-flex justify-content-center">
                                        <input class="form-check-input" type="checkbox" id="defaultCheck6" checked/>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td class="text-start">A new browser used to sign in</td>
                                <td>
                                    <div class="form-check d-flex justify-content-center">
                                        <input class="form-check-input" type="checkbox" id="defaultCheck7" checked/>
                                    </div>
                                </td>
                                <td>
                                    <div class="form-check d-flex justify-content-center">
                                        <input class="form-check-input" type="checkbox" id="defaultCheck8" checked/>
                                    </div>
                                </td>
                                <td>
                                    <div class="form-check d-flex justify-content-center">
                                        <input class="form-check-input" type="checkbox" id="defaultCheck9" checked/>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td class="text-start">A new device is linked</td>
                                <td>
                                    <div class="form-check d-flex justify-content-center">
                                        <input class="form-check-input" type="checkbox" id="defaultCheck10"/>
                                    </div>
                                </td>
                                <td>
                                    <div class="form-check d-flex justify-content-center">
                                        <input class="form-check-input" type="checkbox" id="defaultCheck11" checked/>
                                    </div>
                                </td>
                                <td>
                                    <div class="form-check d-flex justify-content-center">
                                        <input class="form-check-input" type="checkbox" id="defaultCheck12"/>
                                    </div>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="card-body">
                        <button type="submit" class="btn btn-primary me-1">Сохранить изменения</button>
                        <button type="reset" class="btn btn-outline-secondary">Отменить</button>
                    </div>
                </div>
                {{--/ notifications --}}

            </div>
            {{--/ User Content --}}
        </div>
    </section>

    {{--    @include('content/_partials/_modals/modal-edit-user')--}}
    {{--    @include('content/_partials/_modals/modal-upgrade-plan')--}}
@endsection

@section('page-style')
    {{-- Page Css files --}}
    {{--    <link rel="stylesheet" href="{{ asset(mix('css/base/plugins/forms/form-validation.css')) }}">--}}
    {{--    <link rel="stylesheet" href="{{ asset(mix('css/base/plugins/extensions/ext-component-sweet-alerts.css')) }}">--}}
@endsection

@section('page-script')
    {{-- Page js files --}}
    {{--    <script src="{{ asset(mix('js/scripts/pages/modal-edit-user.js')) }}"></script>--}}
    {{--    <script src="{{ asset(mix('js/scripts/pages/app-user-view.js')) }}"></script>--}}
@endsection