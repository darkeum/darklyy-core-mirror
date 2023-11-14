<div class="col-xl-4 col-lg-5 col-md-5 order-1 order-md-0">

    <!-- User Card -->
    <div class="card">
        <div class="card-body">
            <div class="user-avatar-section">
                <div class="d-flex align-items-center flex-column">
                    <img class="img-fluid rounded mt-3 mb-2"
                         src="{{ avatar(false) }}"
                         height="110"
                         width="110"
                         alt="User avatar"
                    />
                    <div class="user-info text-center">
                        <h4 title="{{ $user->full_user_name }}">{{ $user->surname }} {{ $user->name }}</h4>
                        <span class="badge bg-secondary">
                            {{ Auth::user()->roles->first()['lang_' . app()->getLocale()] }}
                        </span>
                    </div>
                </div>
            </div>
            <div class="d-flex justify-content-around my-2 pt-75">
                <div class="d-flex align-items-start me-2">
              <span class="badge bg-light-primary p-75 rounded">
                <i data-feather="check" class="font-medium-2"></i>
              </span>
                    <div class="ms-75">
                        <h4 class="mb-0">~~~</h4>
                        <small>
                            @lang('profile::lang.sidebar.tasks')
                        </small>
                    </div>
                </div>
                <div class="d-flex align-items-start">
              <span class="badge bg-light-primary p-75 rounded">
                <i data-feather="briefcase" class="font-medium-2"></i>
              </span>
                    <div class="ms-75">
                        <h4 class="mb-0">~~~</h4>
                        <small>
                            @lang('profile::lang.sidebar.projects')
                        </small>
                    </div>
                </div>
            </div>
            <h4 class="fw-bolder border-bottom pb-50 mb-1">
                @lang('profile::lang.sidebar.details')
            </h4>
            <div class="info-container">
                <ul class="list-unstyled">
                    <li class="mb-75">
                        <span class="fw-bolder me-25">@lang('profile::lang.sidebar.id'):</span>
                        <span>{{ $user->id }}</span>
                    </li>
                    <li class="mb-75">
                        <span class="fw-bolder me-25">@lang('profile::lang.sidebar.username'):</span>
                        <span>{{ stristr($user->nick, '@', true) }}</span>
                    </li>
                    <li class="mb-75">
                        <span class="fw-bolder me-25">@lang('profile::lang.sidebar.email'):</span>
                        <span>{{ $user->email }}</span>
                    </li>
                    <li class="mb-75">
                        <span class="fw-bolder me-25">@lang('profile::lang.sidebar.status.title'):</span>
                        <span class="badge bg-light-{{ isset($user->active) ? 'success' : 'danger' }}">
                            {{ trans('profile::lang.sidebar.status.'. (isset($user->active) ? '' : 'not_') .'active') }}
                        </span>
                    </li>
                    <li class="mb-75">
                        <span class="fw-bolder me-25">@lang('profile::lang.sidebar.role'):</span>
                        <span>{{ Auth::user()->roles->first()['lang_' . app()->getLocale()] }}</span>
                    </li>
                    <li class="mb-75">
                        <span class="fw-bolder me-25">@lang('profile::lang.sidebar.tel'):</span>
                        <span>{{ $user->phone ?? trans('profile::lang.sidebar.unspec') }}</span>
                    </li>
                    <li class="mb-75">
                        <span class="fw-bolder me-25">@lang('profile::lang.sidebar.date'):</span>
                        <span>{{ \Boot\Support\Carbon::parse($user->birthday)->format('d.m.Y') }}</span>
                    </li>
                    {{--<li class="mb-75">--}}
                    {{--    <span class="fw-bolder me-25">@lang('profile::lang.sidebar.country'):</span>--}}
                    {{--    <span>Wake Island</span>--}}
                    {{--</li>--}}
                </ul>

                @if(current_user($user))

                    <div class="d-flex justify-content-center pt-2">
                        <a href="{{ route('admin.profile.edit') }}" class="btn btn-primary me-1">
                            @lang('profile::lang.sidebar.edit')
                        </a>
                        {{--<a href="javascript:;" class="btn btn-outline-danger suspend-user">--}}
                        {{--    @lang('profile::lang.sidebar.suspended')--}}
                        {{--</a>--}}
                    </div>
                @endif
            </div>
        </div>
    </div>
    <!-- /User Card -->

</div>