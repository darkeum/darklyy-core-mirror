@extends('support::manager.body')

@section('title', __('support::lang.show', ['numb' => $support->id]))

{{--@section('content-header')--}}
{{--@include('support::manager.header')--}}
{{--@endsection--}}

{{-- Правый Sidebar --}}
@section('content-sidebar')
    <livewire:support::chat_sidebar :support_id="$support->id"/>
@endsection
{{-- Правый Sidebar --}}

@section('content')

    <div class="body-content-overlay"></div>

    {{-- Левая выезная панель --}}
    @include('support::manager.layouts.panel_left')
    {{-- Левая выезная панель --}}

    {{-- Чат --}}
    @include('support::manager.components.chat')
    {{-- Чат --}}

    {{-- Правая выезная панель --}}
    @include('support::manager.layouts.panel_right')
    {{-- Правая выезная панель --}}

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
        // let userChats = $('.user-chats');
        // if (userChats.length > 0) {
        //     let chatsUser = new PerfectScrollbar(userChats[0], {
        //         wheelPropagation: true
        //     });
        // }

        // window.addEventListener('change', event => {
        //     $('#attaches-tip')
        //         //.attr('title', 'NEW_TITLE')
        //         .tooltip('show');
        // })

        document.addEventListener("DOMContentLoaded", () => {

            // Livewire.hook('component.initialized', (component) => {})
            // Livewire.hook('element.initialized', (el, component) => {})
            Livewire.hook('element.updating', (fromEl, toEl, component) => {})
            Livewire.hook('element.updated', (el, component) => {})
            Livewire.hook('element.removed', (el, component) => {})
            Livewire.hook('message.sent', (message, component) => {})
            Livewire.hook('message.failed', (message, component) => {})
            Livewire.hook('message.received', (message, component) => {})
            Livewire.hook('message.processed', (message, component) => {})
        });
    </script>
@endpush
