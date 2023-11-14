<div id="content">
    <input type="hidden" value="{{ $price_id }}" id="mainId">
    <input type="hidden" value="{}" id="saveData">
    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title">@lang('price::show.title', ['numb' => $price_id])</h5>
            <div class="row">
                <div class="col-md-3">
                    <div class="holder-container" id="head-buttons">
                        @if ($catalog_price->active == 1)
                            <div class="holder-style holder-danger" data-bs-toggle="modal" data-bs-target="#reactive">
                                <i class="fa-light fa-circle-check holder-icon"></i>@lang('price::show.deactivate')
                            </div>
                        @else
                            <div class="holder-style holder-active" data-bs-toggle="modal" data-bs-target="#reactive">
                                <i class="fa-light fa-circle-check holder-icon"></i>@lang('price::show.activate')
                            </div>
                        @endif
                        <a target="_blank" href="{{ route('admin.price.print', ['id' => $price_id]) }}" class="holder-style holder-active">
                            <i class="fa-light fa-print holder-icon"></i>@lang('price::show.print')
                        </a>
                        <div class="holder-style  @if ($catalog_price->active == 0) holder-active @endif" data-bs-toggle="modal" data-bs-target="#load-price">
                            <i class="fa-light fa-file-excel holder-icon"></i>@lang('price::show.load')
                        </div>
                    </div>
                </div>
                <div class="modal fade   @if ($catalog_price->active == 1) modal-danger @else modal-success @endif text-start" id="reactive" tabindex="-1" aria-labelledby="reacrive-label" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="reacrive-label">
                                    @if ($catalog_price->active == 1)
                                        @lang('price::show.deactivate.title')
                                    @else
                                        @lang('price::show.activate.title')
                                    @endif
                                </h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                @if ($catalog_price->active == 1)
                                    @lang('price::show.deactivate.description')
                                @else
                                    @lang('price::show.activate.description')
                                @endif
                            </div>
                            <div class="modal-footer">
                                <button type="button" wire:click="reactive" class="btn @if ($catalog_price->active == 1) btn-danger @else btn-success @endif" data-bs-dismiss="modal">@lang('price::show.accept')</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal fade modal-primory text-start" id="load-price" tabindex="-1" aria-labelledby="load-price-label" aria-hidden="true">
                    <livewire:price::showload :price_id="$price_id" />
                </div>
                <div class="col-md-9">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="d-flex align-items-start me-2 mb-2">
                                <div class="avatar bg-light-secondary rounded float-start mt-25">
                                    <div class="avatar-content">
                                        <i class="fa-regular fa-user fa-xl holder-icon"></i>
                                    </div>
                                </div>
                                <div class="ms-75">
                                    <small class="mb-0">@lang('price::show.uid')</small>
                                    <h5>{{ shortUserName($catalog_price->uid) }}</h5>
                                </div>
                            </div>
                            <div class="d-flex align-items-start me-2 mb-2">
                                <div class="avatar bg-light-secondary rounded float-start mt-25">
                                    <div class="avatar-content">
                                        <i class="fa-regular fa-rub fa-xl holder-icon"></i>
                                    </div>
                                </div>
                                <div class="ms-75">
                                    <small class="mb-0">@lang('price::show.type')</small>
                                    <h5>{{ $catalog_price->type->title }} ({{ \Boot\Support\Currency::get($catalog_price->type->currency)->alpha3() }})</h5>
                                </div>
                            </div>
                            <div class="d-flex align-items-start me-2 mb-2">
                                <div class="avatar bg-light-secondary rounded float-start mt-25">
                                    <div class="avatar-content">
                                        <i class="fa-regular fa-comment fa-xl holder-icon"></i>
                                    </div>
                                </div>
                                <div class="ms-75">
                                    <small class="mb-0">@lang('price::show.comment')</small>
                                    <h5>
                                        <span data-pk="{{ $catalog_price->id }}" data-bs-toggle="dark-editable" data-value="{{ $catalog_price->comment }}" data-url="{{ route('admin.price.comment') }}"
                                            data-emptytext="@lang('price::show.comment.empty')"></span>
                                    </h5>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="d-flex align-items-start me-2 mb-2">
                                <div class="avatar bg-light-secondary rounded float-start mt-25">
                                    <div class="avatar-content">
                                        <i class="fa-regular fa-clock fa-xl holder-icon"></i>
                                    </div>
                                </div>
                                <div class="ms-75">
                                    <small class="mb-0">@lang('price::show.date')</small>
                                    <h5>
                                        {{ \Boot\Support\Carbon::parse($catalog_price->date)->locale('ru_RU')->isoFormat('dd D MMMM, YYYY') }}
                                    </h5>
                                </div>
                            </div>
                            <div class="d-flex align-items-start me-2 mb-2">
                                <div class="avatar bg-light-secondary rounded float-start mt-25">
                                    <div class="avatar-content">
                                        <i class="fa-regular fa-check fa-xl holder-icon"></i>
                                    </div>
                                </div>
                                <div class="ms-75">
                                    <small class="mb-0">@lang('price::show.active')</small>
                                    <h5>
                                        @if ($catalog_price->active == 1)
                                            <span class="badge badge-light-success">@lang('price::show.active.on')</span>
                                        @else
                                            <span class="badge badge-light-danger">@lang('price::show.active.off')</span>
                                        @endif
                                    </h5>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="d-flex align-items-start me-2 mb-2">
                                <div class="avatar bg-light-secondary rounded float-start mt-25">
                                    <div class="avatar-content">
                                        <i class="fa-regular fa-file fa-xl holder-icon"></i>
                                    </div>
                                </div>
                                <div class="ms-75">
                                    <small class="mb-0">@lang('price::show.attach')</small>
                                    <h5>
                                        @if (empty($catalog_price->aid))
                                            @lang('price::show.attach.empty')
                                        @else
                                            <a target="_blank" href="/download/{{ $catalog_price->aid }}">{{ $catalog_price->attach->name }}</a>
                                        @endif
                                    </h5>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12">
        <livewire:price::showdetail :price_id="$price_id" :currency="$currency" :catalog_price="$catalog_price" />
    </div>
</div>
