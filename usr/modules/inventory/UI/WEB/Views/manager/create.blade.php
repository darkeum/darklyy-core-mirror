@extends('inventory::manager.body')

@section('title')
    @if ($unit ?? false)
        @lang('inventory::general.edit')
    @else
        @lang('inventory::general.add')
    @endif
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <h4 class="card-title">
                @if ($unit ?? false)
                    @lang('inventory::general.edit')
                @else
                    @lang('inventory::general.add')
                @endif
            </h4>
        </div>
        <form method="POST" action="@if ($unit ?? false) {{ route('admin.inventory.update', $unit->id) }}@else{{ route('admin.inventory.store') }} @endif" role="form" class="form-horizontal parsley-form">
            <div class="card-body">
                <div class="row">
                    @csrf
                    @if ($unit ?? false)
                        @method('PUT')
                    @endif
                    <div class="col-4">
                        <div class="mb-1">
                            <label class="form-label" for="email-id-icon">@lang('inventory::general.serial')</label>
                            <div class="input-group input-group-merge">
                                <input type="text" name="serial_number" class="form-control" value="@if ($unit ?? false) {{ $unit->serial_number }} @endif" placeholder="@lang('inventory::general.serial.placeholder')">
                            </div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="mb-1">
                            <label class="form-label" for="email-id-icon">@lang('inventory::general.category')</label>
                            <div class="input-group input-group-merge">
                                <select name="category" id="category" class="form-control select2" required>
                                    <option disable></option>
                                    @foreach ($cats_arr as $cat)
                                        <option value="{{ $cat->id }}" @selected(isset($unit) && $cat->id == $unit->category)> {{ $cat->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="mb-1">
                            <label class="form-label" for="email-id-icon">@lang('inventory::general.org')</label>
                            <div class="input-group input-group-merge">
                                <select name="org" id="org" class="form-control select2" required>
                                    <option disable></option>
                                    @foreach ($orgs as $org)
                                        <option value="{{ $org->id }}" @selected(isset($unit) && $org->id == $unit->org)>{{ $org->organization->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="mb-1">
                            <label class="form-label" for="email-id-icon">@lang('inventory::general.name')</label>
                            <div class="input-group input-group-merge">
                                <input type="text" id="name" name="name" placeholder="@lang('inventory::general.name.placeholder')" class="form-control" value="@if (isset($unit)) {{ $unit->name }} @endif" required>
                            </div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="mb-1">
                            <label class="form-label" for="email-id-icon">@lang('inventory::general.location')</label>
                            <div class="input-group input-group-merge">
                                <select name="location" id="location" class="form-control select2" required>
                                    <option disable></option>
                                    @foreach ($locations as $location)
                                        <option value="{{ $location->id }}" @selected(isset($unit) && $location->id == $unit->location)>{{ $location->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="mb-1">
                            <label class="form-label" for="email-id-icon">@lang('inventory::general.wid')</label>
                            <div class="input-group input-group-merge">
                                <select name="wid" id="wid" class="select2 form-control" required>
                                    <option disable></option>
                                    @foreach ($users_list as $user_one)
                                        <option value="{{ $user_one->id }}" @selected(isset($unit) && $user_one->id == $unit->wid)>{{ shortUserName($user_one->id) }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="mb-1">
                            <label class="form-label" for="email-id-icon">@lang('inventory::general.spec')</label>
                            <div class="input-group input-group-merge">
                                <textarea id="spec" name="spec" placeholder="@lang('inventory::general.spec.placeholder')" class="form-control" rows="5">
                                    @if (isset($unit))
{{ $unit->spec }}
@endif
                                </textarea>
                            </div>
                        </div>
                    </div>
                    @if ($unit ?? false)
                        <div class="col-12">
                            <div class="form-group">
                                <div class="mb-1">
                                    <label class="form-label">@lang('inventory::general.date.check')</label>
                                    <span class="badge @if ($check == __('inventory::general.empty')) bg-warning @else bg-info @endif">{{ $check }}</span>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
            <div class="card-footer col-12 d-flex justify-content-between">
                <a href="{{ route('admin.inventory.index') }}" class="btn btn-outline-secondary waves-effect">@lang('inventory::general.cancel')</a>
                <button type="submit" class="btn btn-primary waves-effect waves-float waves-light">
                    @if ($unit ?? false)
                        @lang('inventory::general.create')
                    @else
                        @lang('inventory::general.update')
                    @endif
                </button>
            </div>
        </form>
    </div>
@endsection

@section('vendor-style')
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/forms/select/select2.min.css')) }}">
@endsection


@section('vendor-script')
    <script src="{{ asset(mix('vendors/js/forms/select/select2.full.min.js')) }}"></script>>
@endsection

@push('scripts')
    <script>
        $('.select2').select2({
            width: "100%",
            placeholder: " @lang('inventory::general.select')",
        });

        @if (\Session::has('message'))
            {
                Swal.fire({
                    icon: "success",
                    title: "@lang('inventory::general.warning')",
                    text: "{!! \Session::get('message') !!}",
                    timer: 1200,
                })
            }
        @endif
    </script>
@endpush
