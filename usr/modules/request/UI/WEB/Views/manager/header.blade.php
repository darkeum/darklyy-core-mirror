<div class="row">
    <div class="col-sm-8 col-lg-12">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title"> @lang('request::general.index')</h4>
                <ul class="nav nav-pills">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.request.index') ? 'active' : '' }}" href="{{ route('admin.request.index') }}">@lang('request::general.index')</a>
                    </li>
                    
                    @if (request()->routeIs('admin.request.view'))
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.request.view') ? 'active' : '' }}"
                            href="{{ route('admin.request.index') }}">
                            @lang('request::general.view',['numb' => $id])
                            </a>
                        </li>
                    @endif

                    <li class="nav-item ms-auto">
                        <a class="nav-link btn btn-success waves-effect waves-float waves-light {{ request()->routeIs('admin.request.add') ? 'active' : '' }}" href="{{ route('admin.request.add') }}">@lang('request::general.add')</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
