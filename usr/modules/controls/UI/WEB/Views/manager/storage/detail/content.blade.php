<div class="content-wrapper  p-0">
    <div class="content-header row">
    </div>
    <div class="content-body">
        <section class="invoice-preview-wrapper">
            <div class="row invoice-preview">
                <!-- Invoice -->
                <div class="col-xl-9 col-md-8 col-12">
                    <div class="card invoice-preview-card">
                        {{--Header starts--}}
                        @include('controls::manager.storage.detail.head')
                        {{--Header ends--}}
                        <hr class="invoice-spacing" />

                        <div class="card-body">
                            <div id="control-body" class="heading-element">
                                <div class="d-flex justify-content-between">
                                    <div class="pb-1 fs-4">{{ trans('controls::lang.storage.list') }}:</div>
                                    <div class="pb-1">
                                        <a
                                            data-action="reload"
                                            class="btn btn-primary btn-sm px-50"
                                            data-bs-original-title="Обновить"
                                            data-bs-toggle="tooltip"
                                            data-placement="top"
                                        ><i data-feather="rotate-cw"></i></a>
                                    </div>
                                </div>

                                <livewire:controls::tablestorage :id_detail="$topContent->id" :status="$status" :content="$content" />
                            </div>
                        </div>
                        <hr class="invoice-spacing" />
                    </div>
                </div>
                <!-- /Invoice -->

                <!-- Invoice Actions -->
                <div class="col-xxl-3 col-6 invoice-actions mt-md-0 mt-2">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">{{ trans('controls::lang.storage.action') }}</h4>

                            <div class="btn-group w-100 mb-75" role="group" aria-label="">
                                <a class="btn btn-sm btn-info" href="#" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ trans('controls::lang.download') }}">
                                    <i data-feather='download-cloud'></i>
                                </a>
                                <a class="btn btn-sm btn-dark" href="{{ url(ADMIN. '/module/fcontrol/storage_print', $topContent->id) }}" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ trans('controls::lang.print') }}">
                                    <i data-feather='printer'></i>
                                </a>
                                <a class="btn btn-sm btn-warning" href="#" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ trans('controls::lang.edit') }}">
                                    <i data-feather='edit'></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /Invoice Actions -->
            </div>
        </section>
    </div>
</div>
