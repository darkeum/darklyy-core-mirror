<div class="row">
    <div class="col-sm-8 col-lg-12">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title"> @lang('engineerings::general.register')</h4>
                <ul class="nav nav-pills">

                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.verifications') ? 'active' : '' }}" href="{{ route('admin.verifications') }}">@lang('engineerings::general.index')</a>
                    </li>
 
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.verifications.create') ? 'active' : '' }}" href="{{ route('admin.verifications.create') }}">@lang('engineerings::general.register')</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.verifications.create.doc') ? 'active' : '' }}" href="{{ route('admin.verifications.create.doc') }}">@lang('engineerings::general.index')</a>
                    </li>
 
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.verifications.device') ? 'active' : '' }}" href="{{ route('admin.verifications.device') }}">@lang('engineerings::general.register')</a>
                    </li>
                    
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.verifications.search') ? 'active' : '' }}" href="{{ route('admin.verifications.search') }}">@lang('engineerings::general.index')</a>
                    </li>
 
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.verifications.report') ? 'active' : '' }}" href="{{ route('admin.verifications.report') }}">@lang('engineerings::general.register')</a>
                    </li>



                </ul>
            </div>
            
        </div>
    </div>
</div>
