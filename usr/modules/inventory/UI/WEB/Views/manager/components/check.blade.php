<div>
    @include('elements.preloader', ['wire' => true])
    <div class="modal-body text-center">
        @if ($unitCheck != null)
            <table class="table table-borderless">
                <thead>
                    <tr>
                        <th class="col-2">
                            @lang('inventory::general.key')
                        </th>
                        <th class="col-10">
                            @lang('inventory::general.value')
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            @lang('inventory::general.id')
                        </td>
                        <td>
                            {{ $unitCheck->id }}
                        </td>
                    </tr>
                    <tr>
                        <td>
                            @lang('inventory::general.name')
                        </td>
                        <td>
                            @if ($unitCheck->name == null)
                                <span class="badge bg-warning">@lang('inventory::general.empty')</span>
                            @else
                                {{ $unitCheck->name }}
                            @endif

                        </td>
                    </tr>
                    <tr>
                        <td>
                            @lang('inventory::general.category')
                        </td>
                        <td>
                            @if ($cat == null)
                                <span class="badge bg-warning">@lang('inventory::general.empty')</span>
                            @else
                                {{ $cat->name }}
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td>
                            @lang('inventory::general.serial')
                        </td>
                        <td>
                            @if ($unitCheck->serial_number == null)
                                <span class="badge bg-warning">@lang('inventory::general.empty')</span>
                            @else
                                {{ $unitCheck->serial_number }}
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td>
                            @lang('inventory::general.wid')
                        </td>
                        <td>
                            @if ($unitCheck->wid == -1)
                                <span class="badge bg-warning">@lang('inventory::general.empty')</span>
                            @else
                                {{ shortUserName($uid->id) }}
                            @endif
                        </td>
                    </tr>
                </tbody>
            </table>
        @else
            <h1><span class="badge bg-warning">@lang('inventory::general.info.empty')</span></h1>
        @endif
    </div>
    <div class="text-center p-1">
        @if (isset($check) && !empty($check) && $unitCheck != null)
            <button type="button" class="btn btn-lg btn-success" data-bs-dismiss="modal">@lang('inventory::general.checked')</button>
        @elseif($unitCheck != null)
            <a wire:click="checkObj()" class="btn btn-lg btn-info">@lang('inventory::general.check.do')</a>
        @else
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">@lang('inventory::general.close')</button>
        @endif

    </div>
</div>

@push('scripts')
    <script>
        $('#exampleModalCenter').on('show.bs.modal', function(e) {
            var button = e.relatedTarget
            var unitId = button.getAttribute('data-id')
            @this.set('idu', unitId);
        })
    </script>
@endpush
