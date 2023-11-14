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