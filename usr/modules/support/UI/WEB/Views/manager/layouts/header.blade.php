{{-- header --}}
<div class="content-header row mb-1">

    <div class="content-header-left col-md-10 col-12 mb-50">
        <div class="row breadcrumbs-top">
            <div class="col-12">

                <h2 class="content-header-title float-start mb-0">
                    @lang('support::lang.module')
                </h2>

                <div class="breadcrumb-wrapper">
{{--                    @if (request()->routeIs('admin.support.show'))--}}
{{--                        <ol class="breadcrumb breadcrumb-dots">--}}
{{--                            <li class="breadcrumb-item active">--}}
{{--                                <a href="javascript:void(0)">--}}
{{--                                    Открыта заявка # {{ $id }}--}}
{{--                                </a>--}}
{{--                            </li>--}}
{{--                        </ol>--}}
{{--                    @endif--}}
                </div>

            </div>
        </div>
    </div>

    <div class="content-header-right text-md-end col-md-2 col-12 d-md-block d-none mb-50">
        <div class="breadcrumb-right">

            <a class="btn btn-icon btn-success waves-effect"
               href="{{ route('admin.support.create') }}"
            >
                @lang('support::lang.create')
            </a>

        </div>
    </div>

    {{-- Меню модуля --}}
    <div class="row">
        <div class="d-inline-block">

            <a class="btn btn-sm waves-effect mb-25 {{ request()->routeIs('admin.support.index') ? 'btn-primary' : 'btn-outline-primary' }}"
               href="{{ route('admin.support.index') }}">
                @lang('support::lang.my')
            </a>

            @can('support manager')
                <a class="btn btn-sm waves-effect mb-25 {{ request()->routeIs('admin.support.all') ? 'btn-primary' : 'btn-outline-primary' }}"
                   href="{{ route('admin.support.all') }}">
                    @lang('support::lang.list')
                </a>
            @endcan

            @if (request()->routeIs('admin.support.show'))
                <a class="btn btn-sm waves-effect mb-25 btn-primary">
                    Открыта заявка # {{ $id }}
                </a>
            @endif

        </div>
    </div>
    {{-- Меню модуля --}}

</div>
{{-- header --}}