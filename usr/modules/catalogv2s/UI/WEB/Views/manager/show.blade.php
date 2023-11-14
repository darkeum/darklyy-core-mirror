@extends('layouts.base')

@section('title', __('support::lang.show', ['numb' => $support->id]))

@section('content-header')
    @include('elements.header', [
            'buttons' => [
                trans('support::lang.my') => 'admin.support.index',
                trans('support::lang.list') => 'admin.support.admin',
            ],
            'show' => ['Открыта заявка #', 'admin.support.show'],
            'add' => [trans('support::lang.create'), 'admin.support.create']
        ])
@endsection

@section('content-sidebar')
    <livewire:support::sidebar :support_id="$support->id"/>
@endsection

@section('content')

    <div class="body-content-overlay"></div>

    <livewire:support::chat :support="$support"/>

    <!-- User Chat profile right area -->
    <div class="user-profile-sidebar">

        <header class="user-profile-header">
            <span class="close-icon">
                <i data-feather="x"></i>
            </span>
            <!-- User Profile image with name -->
            <div class="header-profile-sidebar">
                <div class="avatar box-shadow-1 avatar-border avatar-xl">
                    <img src="{{ $support->user->avatar }}" alt="user_avatar" height="70" width="70"/>
                </div>
                <h4 class="chat-user-name">{{ $support->user->name }} {{ $support->user->surname }}</h4>
                <span class="user-post">{{ $support->user->signature }}</span>
            </div>
            <!--/ User Profile image with name -->
        </header>

        <div class="user-profile-sidebar-area">
            <!-- User's personal information -->
            <div class="personal-info">
                <h6 class="section-label mb-1 mt-3">@lang('support::lang.contact')</h6>
                <ul class="list-unstyled">
                    <li class="mb-1">
                        <i data-feather="mail" class="font-medium-2 me-50"></i>
                        <span class="align-middle">{{ $support->user->email ?? 'Не указан' }}</span>
                    </li>
                    <li class="mb-1">
                        <i data-feather="phone-call" class="font-medium-2 me-50"></i>
                        <span class="align-middle">{{ $support->user->phone ?? 'Не указан' }}</span>
                    </li>
                </ul>
            </div>
            <!--/ User's personal information -->
        </div>

    </div>
    <!--/ User Chat profile right area -->

@endsection

@section('page-style')

    <!-- Page css files -->
    <link rel="stylesheet" href="{{ asset(mix('css/base/pages/app-chat.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('css/base/pages/app-chat-list.css')) }}">
    <style>
        textarea.form-control.message {
            line-height: 1.45 !important;
            padding: 0.571rem 1rem !important;
        }
    </style>
@endsection

@section('page-script')
    <!-- Page js files -->
    <script src="{{ asset(mix('js/scripts/pages/app-chat.js')) }}"></script>
@endsection

@push('scripts')
    <script>

        let userChats = $('.user-chats');
        if (userChats.length > 0) {
            let chatsUser = new PerfectScrollbar(userChats[0], {
                wheelPropagation: true
            });
        }

        {{----}}
        // document.addEventListener("livewire:load", function (event) {
        document.addEventListener("DOMContentLoaded", () => {
            // Livewire.hook('component.initialized', (component) => {})
            // Livewire.hook('element.initialized', (el, component) => {})
            Livewire.hook('element.updating', (fromEl, toEl, component) => {

            })
            Livewire.hook('element.updated', (el, component) => {

            })
            // Livewire.hook('element.removed', (el, component) => {})
        });
        {{----}}
    </script>
@endpush
