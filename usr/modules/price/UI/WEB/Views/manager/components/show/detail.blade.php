<div>
    {{-- preloader --}}
    @include('elements.preloader', ['wire' => true])
    {{-- preloader --}}
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">@lang('price::show.list')</h4>
                <div class="widget-menu pull-right">
                    @if ($catalog_price->active == 0)
                        <div class="btn-group">
                            @php
                                $victoria = new \Victoria();
                                $victoria->link = "/administration/module/price/victoria_add/{$price_id}";
                            @endphp
                            {!! $victoria->button() !!}
                            <button id="refresh" type="button" class="btn btn-xs btn-info"><span class="mr10 icon fa fa-refresh"></span> @lang('price::show.refresh')</button>
                            <button type="button" class="btn btn-xs btn-success" id="saveBtn"><i class="fa fa-floppy-o mr10" aria-hidden="true"></i>@lang('price::show.save')</button>
                        </div>
                    @else
                        <button id="refresh" type="button" class="btn btn-xs btn-info"><span class="mr10 icon fa fa-refresh"></span> @lang('price::show.refresh')</button>
                    @endif
                </div>
            </div>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th class="w40"><span class="pd-l-sm"></span>#</th>
                            <th class="w50">@lang('price::show.table.sku')</th>
                            <th class="w100">@lang('price::show.table.title')</th>
                            <th class="w50">@lang('price::show.table.unit')</th>
                            <th class="w50">@lang('price::show.table.price')</th>
                            <th class="w40">@lang('price::show.table.action')</th>
                        </tr>
                    </thead>
                    <tbody id="tov_list">
                        @foreach ($items as $item)
                            <tr data-id="{{ $item->id }}">
                                <td data-bs-toggle="tooltip" title="id: {{ $item->id }}; cid: {{ $item->cid }}"><span class="pd-l-sm"></span>{{ $loop->iteration }}</td>
                                <td>{{ $item->catalog->sku }}</td>
                                <td>{{ $item->catalog->title }}</td>
                                <td>{{ $item->catalog->catalog_unit->sname }}</td>
                                <td>
                                    <div style="display: inline-flex; align-items: center;">
                                        <span class="cp  @if ($catalog_price->active == 0) sum @endif">{{ $item->sum }}</span>
                                        <span class="ms-1"> {{ $currency }}</span>
                                    </div>
                                </td>
                                <td>
                                    @if ($catalog_price->active == 0)
                                        <div class="btn-group" role="group">
                                            <a data-bs-toggle="tooltip" title="@lang('price::show.table.action.show')" href="/administration/module/catalog_v2/{{ $item->cid }}/edit" target="_blank" class="btn btn-sm btn-outline-secondary">
                                                <i class="fa-light fa-eye"></i>
                                            </a>
                                            <button data-bs-toggle="tooltip" title="@lang('price::show.table.action.delete')" type="button" onclick="delModal(this);" data-id="{{ $item->id }}" class="btn btn-sm btn-outline-danger">
                                                <i class="fa-light fa-trash"></i>
                                            </button>
                                        </div>
                                    @else
                                        <a data-bs-toggle="tooltip" title="@lang('price::show.table.action.show')" href="/administration/module/catalog_v2/{{ $item->cid }}/edit" target="_blank" class="btn btn-sm btn-outline-secondary">
                                            <i class="fa-light fa-eye"></i>
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach

                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div x-data="{
        observe() {
            let observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        @this.call('loadMore')
                    }
                })
            }, {
                root: null
            })
    
            observer.observe(this.$el)
        }
    }" x-init="observe"></div>
</div>
