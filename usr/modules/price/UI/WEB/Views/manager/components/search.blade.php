<section class="">
    {{-- preloader --}}
    @include('elements.preloader', ['wire' => true])
    {{-- preloader --}}

    <div class="row">

        {{-- Фома поиска --}}
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">
                        @lang('price::search.title')
                    </h4>
                    <div class="heading-elements">
                        {{----}}
                    </div>
                </div>
                <div class="card-body">
                    <form wire:submit.prevent="search">

                        <div class="row">
                            <div class="col-md-6 col-12">
                                <div class="mb-2">
                                    <label for="cid" class="form-label">@lang('price::search.cid')</label>
                                    <input aria-describedby="validationCid" id="cid" wire:model.lazy="cid" type="text"
                                           class=" @error('cid') is-invalid @enderror form-control"
                                           placeholder="@lang('price::search.cid.placeholder')"/>
                                    @error('cid')
                                    <div id="validationCid" class="invalid-feedback">{{ $message }} </div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6 col-12">
                                <div class="mb-2" wire:ignore>
                                    <label for="select2" class="form-label">@lang('price::search.type')</label>
                                    <select id="select2" wire:model.lazy="price_type" class="form-select" multiple>
                                        @foreach ($this->catalog_price_type as $type)
                                            <option value="{{ $type->id }}">{{ $type->title }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 d-flex justify-content-between">
                            {{--<button type="submit" class="btn btn-outline-secondary waves-effect">Отмена</button>--}}
                            <button type="submit"
                                    class="btn btn-success waves-effect w-100">@lang('price::search.search')</button>
                        </div>

                    </form>
                </div>

            </div>
        </div>
        {{-- Фома поиска --}}

        {{-- Результаты поиска --}}
        @if ($search_flag)
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">
                            @lang('price::search.result')
                        </h4>
                    </div>
                    <div class="table-responsive text-nowrap">
                        <table class="table">
                            <thead>
                            <tr>
                                <th>@lang('price::search.table.module')</th>
                                <th>@lang('price::search.table.type')</th>
                                <th>@lang('price::search.table.active')</th>
                                <th>@lang('price::search.table.price')</th>
                                <th>@lang('price::search.table.date')</th>
                                <th>@lang('price::search.table.uid')</th>
                            </tr>
                            </thead>
                            <tbody class="table-border-bottom-0">
                            @forelse ($items as $item)

                                @php
                                    if ($item['active'] == 0) {
                                        $active_text = __('price::search.table.active.off');
                                        $active_color = 'red';
                                    } else {
                                        $active_text = __('price::search.table.active.on');
                                        $active_color = 'green';
                                    }

                                    $link = '';
                                    $link_func = function ($link, $text) {
                                        return "<div><a href=\"{$link}\" target=\"_blank\">{$text}</a></div>";
                                    };
                                    if (!empty($item['module']) && !empty($item['pid'])) {
                                        switch ($item['module']) {
                                            default:
                                                $link = __('price::search.table.module.unknown');
                                                break;
                                            case 'supply_order':
                                                $link = $link_func('/administration/module/supply/order_detail/' . $item['pid'], __('price::search.table.module.supply', ['numb' => $item['pid']]));
                                                break;
                                            case 'request':
                                                $link = $link_func('/administration/module/request/view/' . $item['pid'], __('price::search.table.module.request', ['numb' => $item['pid']]));
                                                break;
                                        }
                                    }
                                    switch ($item['currency']) {
                                        default:
                                            $currency_sign = '¤';
                                            break;
                                        case '643':
                                            $currency_sign = '₽';
                                            break;
                                        case '978':
                                            $currency_sign = '€';
                                            break;
                                        case '840':
                                            $currency_sign = "$";
                                            break;
                                    }
                                    $price = number_format($item['sum'], 2, '.', ' 	') . ' ' . $currency_sign;
                                    $date = date('d.m.Y H:i:s', strtotime($item['date']));
                                    $user = \Boot\Models\Users::find($item['uid']);
                                @endphp

                                <tr>
                                    <td>
                                        <a href="{{ route('admin.price.show', $item['id']) }}" target="_blank">
                                            @lang('price::search.table.order', ['numb' => $item['id']])
                                        </a>
                                        {!! $link !!}
                                    </td>
                                    <td class="font-bold">
                                        {{ $item['title'] }}
                                    </td>
                                    <td style="color:  {{ $active_color }}">
                                        {{ $active_text }}
                                    </td>
                                    <td class="font-bold">
                                        {{ $price }}
                                    </td>
                                    <td>{{ $date }}</td>
                                    <td>
                                        @if (!empty($user))
                                            {{ $user->short_user_name }}
                                        @else
                                            @lang('price::search.table.unknown')
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6">@lang('price::search.table.empty')</td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif
        {{-- Результаты поиска --}}

    </div>

</section>

@push('scripts')
    <script>
        $(document).ready(function () {
            $('#select2').select2({
                placeholder: `@lang('price::search.type.placeholder')`,
                allowClear: true,
                dropdownAutoWidth: true,
                width: '100%',
            }).on('change', function (e) {
                let data = $('#select2').select2("val");
                @this.set('price_type', data);
            });
        });
    </script>
@endpush