<section id="multiple-column-form">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">
                        @if ($item ?? false)
                            @lang('scada::form.main.edit.title', ['numb' => $item->id])
                        @else
                            @lang('scada::form.main.create.title')
                        @endif
                    </h4>
                </div>
                <div class="card-body">
                    <form @if ($item ?? false) action="{{ route('admin.scada.tags.update', $item) }}" @else action="{{ route('admin.scada.tags.store') }}" @endif method="POST" class="form">
                        @csrf
                        @if ($item ?? false)
                            @method('PUT')
                        @endif
                        <div class="row">
                            <div class="col-12">
                                <div class="mb-1">
                                    <label class="form-label" for="scada_id">@lang('scada::form.tags.scada')</label>
                                    <select id="scada_id" name="scada_id" class="form-select @error('scada_id') is-invalid @enderror">
                                        @foreach ($scada as $value)
                                            <option {{ isset($item->scada_id) ? (old('scada_id', $item->scada_id) == $value->id ? 'selected' : '') : (old('scada_id') == $value->id ? 'selected' : '') }} value="{{ $value->id }}">
                                                {{ $value->title }}</option>
                                        @endforeach
                                    </select>
                                    @error('scada_id')
                                        <div class="invalid-feedback">{{ $message }} </div>
                                    @enderror
                                </div>
                                <div class="mb-1">
                                    <label class="form-label" for="title">@lang('scada::form.tags.title')</label>
                                    <input type="text" value="{{ isset($item->title) ? old('title', $item->title) : old('title') }}" id="title" name="title" class="form-control @error('title') is-invalid @enderror"
                                        placeholder="@lang('scada::form.tags.title.placeholder')" required />
                                    @error('title')
                                        <div class="invalid-feedback">{{ $message }} </div>
                                    @enderror
                                </div>

                                <div class="mb-1">
                                    <label class="form-label" for="unit_id">@lang('scada::form.tags.unit_id')</label>
                                    <input type="text" value="{{ isset($item->unit_id) ? old('unit_id', $item->unit_id) : old('unit_id') }}" id="unit_id" name="unit_id" class="form-control @error('unit_id') is-invalid @enderror"
                                        placeholder="@lang('scada::form.tags.unit_id.placeholder')" required />
                                    @error('unit_id')
                                        <div class="invalid-feedback">{{ $message }} </div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3 col-12">
                                <div class="mb-1">
                                    <label class="form-label" for="address">@lang('scada::form.tags.address')</label>
                                    <input type="text" value="{{ isset($item->address) ? old('address', $item->address) : old('address') }}" id="address" name="address" class="form-control @error('address') is-invalid @enderror"
                                        placeholder="@lang('scada::form.tags.address.placeholder')" required />
                                    @error('address')
                                        <div class="invalid-feedback">{{ $message }} </div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3 col-12">
                                <div class="mb-1">
                                    <label class="form-label" for="quantity">@lang('scada::form.tags.quantity')</label>
                                    <input type="text" value="{{ isset($item->quantity) ? old('quantity', $item->quantity) : old('quantity') }}" id="quantity" name="quantity" class="form-control @error('quantity') is-invalid @enderror"
                                        placeholder="@lang('scada::form.tags.quantity.placeholder')" required />
                                    @error('quantity')
                                        <div class="invalid-feedback">{{ $message }} </div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3 col-12">
                                <div class="mb-1">
                                    <label class="form-label" for="registr">@lang('scada::form.tags.registr')</label>
                                    <select id="registr" name="registr" class="form-select @error('registr') is-invalid @enderror">
                                        @foreach ($registr as $value)
                                            <option {{ isset($item->registr) ? (old('registr', $item->registr) == $value ? 'selected' : '') : (old('registr') == $value ? 'selected' : '') }} id="{{ $value }}">
                                                {{ $value }}</option>
                                        @endforeach
                                    </select>
                                    @error('registr')
                                        <div class="invalid-feedback">{{ $message }} </div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3 col-12">
                                <div class="mb-1">
                                    <label class="form-label" for="endian">@lang('scada::form.tags.endian')</label>
                                    <select id="endian" name="endian" class="form-select @error('endian') is-invalid @enderror">
                                        <option value="0">@lang('scada::form.no_use')</option>
                                        @foreach ($endian as $value)
                                            <option {{ isset($item->endian) ? (old('endian', $item->endian) == $value ? 'selected' : '') : (old('endian') == $value ? 'selected' : '') }} id="{{ $value }}">
                                                {{ $value }}</option>
                                        @endforeach
                                    </select>
                                    @error('endian')
                                        <div class="invalid-feedback">{{ $message }} </div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6 col-12">
                                <div class="mb-1">
                                    <label class="form-label" for="actions">@lang('scada::form.tags.actions')</label>
                                    <select id="actions" name="actions" class="form-select @error('actions') is-invalid @enderror">
                                        @foreach ($actions as $value)
                                            <option {{ isset($item->actions) ? (old('actions', $item->actions) == $value ? 'selected' : '') : (old('actions') == $value ? 'selected' : '') }} id="{{ $value }}">
                                                {{ $value }}</option>
                                        @endforeach
                                    </select>
                                    @error('actions')
                                        <div class="invalid-feedback">{{ $message }} </div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6 col-12">
                                <div class="mb-1">
                                    <label class="form-label" for="actions_type">@lang('scada::form.tags.actions_type')</label>
                                    <select id="actions_type" name="actions_type" class="form-select @error('actions_type') is-invalid @enderror">
                                        <option value="0">@lang('scada::form.no_use')</option>
                                        @foreach ($actions_type as $value)
                                            <option {{ isset($item->actions_type) ? (old('actions_type', $item->actions_type) == $value ? 'selected' : '') : (old('actions_type') == $value ? 'selected' : '') }} id="{{ $value }}">
                                                {{ $value }}</option>
                                        @endforeach
                                    </select>
                                    @error('actions_type')
                                        <div class="invalid-feedback">{{ $message }} </div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="mb-1">
                                    <label class="form-label" for="type">@lang('scada::form.tags.type')</label>
                                    <select id="type" name="type" class="form-select @error('type') is-invalid @enderror">
                                        @foreach ($type as $value)
                                            <option {{ isset($item->type) ? (old('type', $item->type) == $value ? 'selected' : '') : (old('type') == $value ? 'selected' : '') }} id="{{ $value }}">
                                                {{ $value }}</option>
                                        @endforeach
                                    </select>
                                    @error('type')
                                        <div class="invalid-feedback">{{ $message }} </div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="mb-1">
                                    <label class="form-label" for="byte">@lang('scada::form.tags.byte')</label>
                                    <input type="text" value="{{ isset($item->byte) ? old('byte', $item->byte) : old('byte') }}" id="byte" name="byte" class="form-control @error('byte') is-invalid @enderror"
                                        placeholder="@lang('scada::form.tags.byte.placeholder')" />
                                    @error('byte')
                                        <div class="invalid-feedback">{{ $message }} </div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4 col-12">
                                <div class="mb-1">
                                    <label class="form-label" for="recalculation">@lang('scada::form.tags.recalculation')</label>
                                    <select id="recalculation" name="recalculation" class="form-select @error('recalculation') is-invalid @enderror">
                                        <option {{ isset($item->recalculation) ? (old('recalculation', $item->recalculation) == '0' ? 'selected' : '') : (old('recalculation') == '0' ? 'selected' : '') }} value="0">@lang('scada::form.no')
                                        </option>
                                        <option {{ isset($item->recalculation) ? (old('recalculation', $item->recalculation) == '1' ? 'selected' : '') : (old('recalculation') == '1' ? 'selected' : '') }} value="1">@lang('scada::form.yes')
                                        </option>
                                    </select>
                                    @error('recalculation')
                                        <div class="invalid-feedback">{{ $message }} </div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4 col-12">
                                <div class="mb-1">
                                    <label class="form-label" for="coefficient_a">@lang('scada::form.tags.coefficient_a')</label>
                                    <input type="text" value="{{ isset($item->coefficient_a) ? old('coefficient_a', $item->coefficient_a) : old('coefficient_a') }}" id="coefficient_a" name="coefficient_a"
                                        class="form-control @error('coefficient_a') is-invalid @enderror" placeholder="@lang('scada::form.tags.coefficient_a.placeholder')" />
                                    @error('coefficient_a')
                                        <div class="invalid-feedback">{{ $message }} </div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4 col-12">
                                <div class="mb-1">
                                    <label class="form-label" for="coefficient_b">@lang('scada::form.tags.coefficient_b')</label>
                                    <input type="text" value="{{ isset($item->coefficient_b) ? old('coefficient_b', $item->coefficient_b) : old('coefficient_b') }}" id="coefficient_b" name="coefficient_b"
                                        class="form-control @error('coefficient_b') is-invalid @enderror" placeholder="@lang('scada::form.tags.coefficient_b.placeholder')" />
                                    @error('coefficient_b')
                                        <div class="invalid-feedback">{{ $message }} </div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="mb-1">
                                    <label class="form-label" for="field_type">@lang('scada::form.tags.field_type')</label>
                                    <select id="field_type" name="field_type" class="form-select @error('field_type') is-invalid @enderror">
                                        <option value="0">@lang('scada::form.no_use')</option>
                                        @foreach ($field_type as $value)
                                            <option {{ isset($item->field_type) ? (old('field_type', $item->field_type) == $value ? 'selected' : '') : (old('field_type') == $value ? 'selected' : '') }} id="{{ $value }}">
                                                {{ $value }}</option>
                                        @endforeach
                                    </select>
                                    @error('field_type')
                                        <div class="invalid-feedback">{{ $message }} </div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="mb-1">
                                    <label class="form-label" for="field_id">@lang('scada::form.tags.field_id')</label>
                                    <input type="text" value="{{ isset($item->field_id) ? old('field_id', $item->field_id) : old('field_id') }}" id="field_id" name="field_id"
                                        class="form-control @error('field_id') is-invalid @enderror" placeholder="@lang('scada::form.tags.field_id.placeholder')" />
                                    @error('field_id')
                                        <div class="invalid-feedback">{{ $message }} </div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-check form-switch mb-1">
                                    <input type="checkbox" {{ isset($item->notifiable) ? (in_array(old('notifiable', $item->notifiable), ['1', 'on']) ? 'checked' : '') : (old('notifiable') == 'on' ? 'checked' : '') }} name="notifiable"
                                        class="form-check-input" id="notifiable">
                                    <label class="form-check-label" for="notifiable">@lang('scada::form.tags.notifiable')</label>
                                </div>
                                <div class="form-check form-switch mb-1">
                                    <input type="checkbox" {{ isset($item->status) ? (in_array(old('status', $item->status), ['1', 'on']) ? 'checked' : '') : (old('status') == 'on' ? 'checked' : '') }} name="status"
                                        class="form-check-input" id="status">
                                    <label class="form-check-label" for="status">@lang('scada::form.tags.status')</label>
                                </div>
                                <button type="submit" class="btn btn-primary me-1 waves-effect waves-float waves-light">
                                    @if ($item ?? false)
                                        @lang('scada::form.update')
                                    @else
                                        @lang('scada::form.create')
                                    @endif
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
