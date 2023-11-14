{{--{{ var_dump($support->users) }}--}}

<section class="chat-app-window">
    <div class="active-chat">

        {{-- Support header --}}
        <div class="chat-navbar">
            <header class="chat-header">
                <div class="d-flex align-items-center w-100">
                    <button class="btn btn-icon btn-outline-secondary waves-effect waves-float waves-light me-50 sidebar-toggle d-block d-lg-none">
                        <i class="feather-menu"></i>
                    </button>
                    <div class="avatar avatar-border sidebar-profile-toggle m-0 me-1"
                         title="{{ $support->user->name }} {{ $support->user->surname }}">
                        <img src="{{ $support->user->avatar }}" alt="avatar" height="36" width="36"/>
                    </div>
                    <h6 class="mb-0">@lang('support::form.subject') {{ $support->title ?? 'Не указано' }}</h6>
                    {{--<div class="btn btn-icon btn-warning waves-effect waves-float waves-light ms-auto sidebar-toggle d-block d-lg-none">--}}
                    {{--<i class="feather-menu"></i>--}}
                    {{--</div>--}}
                </div>
            </header>
        </div>
        {{-- Support header --}}

        {{-- Support messages --}}
        <livewire:support::chat_messages :support="$support"/>
        {{-- Support messages --}}

        {{-- preloader --}}
        {{--@include('elements.preloader', ['wire' => true])--}}
        {{-- preloader --}}

        {{-- Support form --}}
        <livewire:support::chat_form :support="$support"/>
        {{-- Support form --}}

    </div>
</section>

{{--@push('scripts')--}}
{{--@endpush--}}