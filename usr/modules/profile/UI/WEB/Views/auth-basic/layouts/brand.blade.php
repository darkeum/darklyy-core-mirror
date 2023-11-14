<a href="{{ route('home') }}" class="brand-logo">
    @include('layouts.svg.' . check_company(true))
    <h2 class="brand-text text-primary ms-1">
        @lang('app.name_' . check_company(true))
    </h2>
</a>