<form action="@if ($unit ?? false) {{ route('admin.stocks.update', $unit) }}@else{{ route('admin.stocks.store') }} @endif" method="POST" enctype="multipart/form-data" class="form">
    @csrf

    @if ($unit ?? false)
        @method('PUT')
    @endif

    <div class="row">
        {{-- Заголовок --}}
        <div class="col-6">
            <div class="mb-1">
                <label class="form-label" for="title">@lang('stocks::general.stock.name'):</label>
                <input type="text" name="name" class="form-control" value="@if ($unit->id ?? false) {{ $unit->name }} @endif" placeholder="@lang('stocks::general.stock.edit')" required>
            </div>
        </div>
        {{-- Заголовок --}}

        {{-- Организация --}}
        <div class="col-6">
            <div class="mb-1">
                <label class="form-label" for="object_id">@lang('stocks::general.org'):</label>
                <select class="form-select select2" name="org">
                    @foreach ($orgs as $org)
                        <option value="{{ $org->id }}" @if (isset($unit) && $org->id == $unit->org) selected @endif>
                            {{ $org->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
        {{-- Организация --}}

        {{-- Кнопки --}}
        <div class="card-footer col-12 d-flex justify-content-between">
            @if ($unit ?? false)
                <a href="{{ route('admin.stocks.show', ['id' => $unit->id]) }}" class="btn btn-outline-secondary waves-effect"> @lang('stocks::general.cancel')</a>
            @endif
            <button type="submit" class="btn btn-primary waves-effect waves-float waves-light">
                @if ($unit ?? false)
                    @lang('stocks::general.edit')
                @else
                    @lang('stocks::general.stock.create')
                @endif
            </button>
        </div>
        {{-- Кнопки --}}

    </div>

</form>

{{-- Необходимые стили --}}
@push('styles')
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/forms/select/select2.min.css')) }}">
@endpush
{{-- Необходимые стили --}}

{{-- Необходимые js либы --}}
@push('scripts')
    <!-- BEGIN: Page Vendor JS-->
    <script src="{{ asset(mix('vendors/js/forms/select/select2.full.min.js')) }}"></script>
    <script src="{{ asset('vendors/js/forms/select/i18n/' . app()->getLocale() . '.js') }}"></script>


    <!-- Page js files -->
    <script data-title="Page js files">
        $(".select2").select2({
            placeholder: "@lang('stocks::general.select')",
        });

        @if (\Session::has('success'))
            {
                Swal.fire({
                    icon: 'success',
                    title: '@lang('stocks::general.warning')',
                    text: '{!! \Session::get('success') !!}',
                    timer: 1500,
                })
            }
        @endif
    </script>
@endpush
{{-- Необходимые js либы --}}
