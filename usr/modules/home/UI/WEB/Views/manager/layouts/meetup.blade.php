{{-- Поумолчанию card-body --}}
@if ($style == false)
    <div class="meetup-img-wrapper rounded-top text-center">
        <img src="@if (check_company()) {{ asset('images/illustration/ae-email.svg') }}@else{{ asset('images/illustration/pe-email.svg') }} @endif" alt="@lang('home::calendar.calendar')" height="{{ $height ?? '170' }}" />
        {{--    @include('layouts.svg.office') --}}
    </div>
@endif
{{-- Поумолчанию card-body --}}

{{-- Праздничный card-body --}}
@if ($style == 2)
    <div class="meetup-img-wrapper rounded-top text-center card-congratulations">
        <div class="card-body text-center">
            <img src="{{ asset('images/elements/decore-left.png') }}" class="congratulations-img-left" alt="card-img-left">
            <img src="{{ asset('images/elements/decore-right.png') }}" class="congratulations-img-right" alt="card-img-right">
            <div class="avatar avatar-xl bg-primary shadow">
                <div class="avatar-content">
                    <i class="fa-duotone fa-cake-candles"></i>
                </div>
            </div>
            <div class="text-center">
                <h1 class="mb-1 text-white">
                    @lang('home::calendar.congratulations')
                    @foreach ($birthday as $user)
                        {{ $user->name }}
                    @endforeach
                </h1>
                <p class="card-text m-auto w-75">                    
                    @foreach ($birthday as $user)
                        {{ $user->surname }} {{ $user->name }}, 
                    @endforeach 
                    @lang('home::calendar.congratulations.full')
                </p>
            </div>
        </div>
    </div>
@endif
{{-- Праздничный card-body --}}
