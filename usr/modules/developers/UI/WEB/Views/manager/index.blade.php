@extends('developers::manager.body')

{{--@section('title', __('changelog::lang.edit'))--}}

@section('content')
    <div class="card developers">
        <div class="card-body">
            <h2 class="pb-2 border-bottom">@lang('developers::admin.capabilities')</h2>
            <div class="row g-4 py-5 row-cols-1 row-cols-lg-3">
                <div class="feature col">
                    <div class="feature-icon bg-primary bg-gradient">
                        <i class="fa-light fa-book"></i>
                    </div>
                    <h2>@lang('developers::admin.darklyy')</h2>
                    <p>@lang('developers::admin.darklyy.description')</p>
                    <a href="https://darklyy.ru" target="_blank">@lang('developers::admin.go')</a>
                </div>
                <div class="feature col">
                    <div class="feature-icon bg-primary bg-gradient">
                        <i class="fa-brands fa-git-alt"></i>
                    </div>
                    <h2>@lang('developers::admin.git')</h2>
                    <p>@lang('developers::admin.git.description')</p>
                    <a href="{{ route('admin.developers.git') }}">@lang('developers::admin.go')</a>
                </div>
                <div class="feature col">
                    <div class="feature-icon bg-primary bg-gradient">
                        <i class="fa-regular fa-wand-magic-sparkles"></i>
                    </div>
                    <h2>@lang('developers::admin.composer')</h2>
                    <p>@lang('developers::admin.composer.description')</p>
                    <a href="{{ route('admin.developers.composer') }}">@lang('developers::admin.go')</a>
                </div>
                <div class="feature col">
                    <div class="feature-icon bg-primary bg-gradient">
                        <i class="fa-regular fa-microchip"></i>
                    </div>
                    <h2>@lang('developers::admin.daemons')</h2>
                    <p>@lang('developers::admin.daemon.description')</p>
                    <a href="{{ route('admin.developers.daemon.index') }}">@lang('developers::admin.go')</a>
                </div>
            </div>
        </div>
    </div>
@endsection
