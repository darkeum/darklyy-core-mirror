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
                    <form @if ($item ?? false) action="{{ route('admin.scada.update', $item) }}" @else action="{{ route('admin.scada.store') }}" @endif method="POST" class="form">
                        @csrf
                        @if ($item ?? false)
                            @method('PUT')
                        @endif
                        <div class="row">
                            <div class="col-md-6 col-12">
                                <div class="mb-1">
                                    <label class="form-label" for="title">@lang('scada::form.main.title')</label>
                                    <input type="text" value="{{ isset($item->title) ? old('title', $item->title) : old('title') }}" id="title" name="title" class="form-control @error('title') is-invalid @enderror"
                                        placeholder="@lang('scada::form.main.title.placeholder')" required />
                                    @error('title')
                                        <div class="invalid-feedback">{{ $message }} </div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6 col-12">
                                <div class="mb-1">
                                    <label class="form-label" for="address">@lang('scada::form.main.address')</label>
                                    <input type="text" value="{{ isset($item->address) ? old('address', $item->address) : old('address') }}" id="address" name="address" class="form-control @error('address') is-invalid @enderror"
                                        placeholder="@lang('scada::form.main.address.placeholder')" required />
                                    @error('address')
                                        <div class="invalid-feedback">{{ $message }} </div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6 col-12">
                                <div class="mb-1">
                                    <label class="form-label" for="ip">@lang('scada::form.main.ip')</label>
                                    <input type="text" value="{{ isset($item->ip) ? old('ip', $item->ip) : old('ip') }}" id="ip" name="ip" class="form-control  @error('ip') is-invalid @enderror"
                                        placeholder="@lang('scada::form.main.ip.placeholder')" required />
                                    @error('ip')
                                        <div class="invalid-feedback">{{ $message }} </div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6 col-12">
                                <div class="mb-1">
                                    <label class="form-label" for="port">@lang('scada::form.main.port')</label>
                                    <input type="text" value="{{ isset($item->port) ? old('port', $item->port) : old('port') }}" id="port" name="port" class="form-control @error('port') is-invalid @enderror"
                                        placeholder="@lang('scada::form.main.port.placeholder')" required />
                                    @error('port')
                                        <div class="invalid-feedback">{{ $message }} </div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6 col-12">
                                <div class="form-check form-switch mb-1">
                                    <input type="checkbox" {{ isset($item->rtuConverter) ? (in_array(old('rtuConverter', $item->rtuConverter), ['1', 'on']) ? 'checked' : '') : (old('rtuConverter') == 'on' ? 'checked' : '') }}
                                        name="rtuConverter" class="form-check-input" id="rtuConverter">
                                    <label class="form-check-label" for="rtuConverter">@lang('scada::form.main.rtuConverter')</label>
                                </div>
                                <div class="form-check form-switch mb-1">
                                    <input type="checkbox" {{ isset($item->status) ? (in_array(old('status', $item->status), ['1', 'on']) ? 'checked' : '') : (old('status') == 'on' ? 'checked' : '') }} name="status"
                                        class="form-check-input" id="status">
                                    <label class="form-check-label" for="status">@lang('scada::form.main.status')</label>
                                </div>
                            </div>
                            <div class="col-12">
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
