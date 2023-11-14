<div class="row">
    <div class="col-sm-8">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title"> @lang('notifications::admin.manager')</h4>
                <ul class="nav nav-pills">

                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.notifications.index') ? 'active' : '' }}"
                           href="{{ route('admin.notifications.index') }}">@lang('notifications::admin.list')</a>
                    </li>

                    {{--<li class="nav-item">--}}
                    {{--    <a class="nav-link {{ request()->routeIs('admin.notifications.archive.index') ? 'active' : '' }}"--}}
                    {{--       href="{{ route('admin.notifications.archive.index') }}">@lang('notifications::admin.archive')</a>--}}
                    {{--</li>--}}

                    <li class="nav-item">
                        <a class="nav-link"
                           href="{{ route('admin.profile.notifications') }}">@lang('notifications::admin.settings')</a>
                    </li>

                </ul>
            </div>
        </div>
    </div>
    <div class="col-sm-4">
        <livewire:notifications::read/>
    </div>
</div>