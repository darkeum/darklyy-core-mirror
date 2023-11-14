<div class="col-lg-4 col-md-6 col-12">
    <div class="card card-developer-meetup">

        {{-- meetup card --}}
        @include('home::manager.layouts.meetup', ['style' => $holiday_marker])
        {{-- meetup card --}}

        <div class="card-body">

            {{-- Приветствие --}}
            <div class="meetup-header d-flex align-items-center">
                <div class="meetup-day">
                    <h6 class="mb-0">{{ \Boot\Support\Carbon::now()->locale('ru_RU')->minDayName }}</h6>
                    <h3 class="mb-0">{{ \Boot\Support\Carbon::now()->day }}</h3>
                </div>
                <div class="my-auto">
                    <h4 class="card-title mb-25">
                        @if ($hour < 8)
                            @lang('home::calendar.day', ['name' => Auth::user()->name])
                        @elseif($hour < 12 && $hour >= 8)
                            @lang('home::calendar.morning', ['name' => Auth::user()->name])
                        @elseif($hour >= 12 && $hour < 17)
                            @lang('home::calendar.day', ['name' => Auth::user()->name])
                        @else
                            @lang('home::calendar.night', ['name' => Auth::user()->name])
                        @endif
                    </h4>
                    <p class="card-text mb-0">
                        @if ($holiday)
                            {{ $holiday }}
                        @else
                            @lang('home::calendar.holiday.empty')
                        @endif
                    </p>
                </div>
            </div>
            {{-- Приветствие --}}

            {{-- Дата --}}
            <div class="mt-0">
                <div class="avatar float-start bg-light-primary rounded me-1">
                    <div class="avatar-content">
                        <i class="fa-light fa-calendar fa-xl"></i>
                    </div>
                </div>
                <div class="more-info">
                    <h6 class="mb-0">{{ \Boot\Support\Carbon::now()->locale('ru_RU')->isoFormat('dd D MMMM, YYYY') }}</h6>
                    <small>{{ \Boot\Support\Carbon::now()->locale('ru_RU')->isoFormat('HH:mm') }}</small>
                </div>
            </div>
            {{-- Дата --}}

            {{-- Задачи --}}
            <div class="mt-2">

                <a href="#" class="avatar float-end bg-light-success rounded"
                   data-bs-toggle="tooltip" data-bs-placement="left" title="@lang('home::calendar.tasks.add')">
                    <div class="avatar-content">
                        <i class="fa-light fa-circle-plus fa-xl"></i>
                    </div>
                </a>

                <div class="avatar float-start bg-light-primary rounded me-1">
                    <div class="avatar-content">
                        <i class="fa-light fa-circle-check fa-xl"></i>
                    </div>
                </div>
                <div class="more-info">
                    <h6 class="mb-0">@lang('home::calendar.tasks', ['count' => $taskTodayCount])</h6>
                    <small>
                        @if ($taskTodayCount)
                            @lang('home::calendar.tasks.full', ['count' => $taskTodayCount])
                        @else
                            @lang('home::calendar.tasks.empty')
                        @endif
                    </small>
                </div>
            </div>
            {{-- Задачи --}}

            {{-- Ближайшие дни рождения --}}
            <div class="mt-2">
                <p class="card-text mb-0">@lang('home::calendar.birthday')</p>
                <div class="avatar-group mt-1">
                    @foreach ($users as $user)
                        <div data-bs-toggle="tooltip" data-popup="tooltip-custom" data-bs-placement="bottom"
                             title="{{ $user->surname }} {{ $user->name }} ({{ \Boot\Support\Carbon::parse($user->birthday)->format('d.m') }})"
                             class="avatar pull-up">
                            <img src="{{ $user->avatar }}" alt="{{ $user->surname }} {{ $user->name }}" width="33"
                                 height="33"/>
                        </div>
                    @endforeach
                    <h6 class="align-self-center cursor-pointer ms-50 mb-0">+{{ $usersCount - 5 }}</h6>
                </div>
            </div>
            {{-- Ближайшие дни рождения --}}

        </div>
    </div>
</div>
