<div class="content-wrapper  p-0">
    <div class="content-header row">
    </div>
    <div class="content-body">
        <section class="invoice-preview-wrapper">
            <div class="row invoice-preview">
                {{-- Invoice --}}
                <div class="col-xxl-9 col-12">
                    <div class="card invoice-preview-card">
                        {{--Header starts--}}
                        @include('controls::manager.main.show.head')
                        {{--Header ends--}}

                        <livewire:controls::table
                                :control_detail="$control->control_detail"
                                :id_show="$control->id"
                                :supply_order_total="$supply_order_total"
                                :sales_total="$sales_total"
                        />
                    </div>
                </div>
                {{-- /Invoice --}}

                {{-- Действия --}}
                <div class="col-xxl-3 col-6 invoice-actions mt-md-0 mt-2">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">@lang('controls::lang.storage.action')</h4>

                            <div class="btn-group w-100 mb-75" role="group" aria-label="">
                                <a class="btn btn-sm btn-info" href="#" data-bs-toggle="tooltip" data-bs-placement="top" title="@lang('controls::lang.download')">
                                    <i data-feather='download-cloud'></i>
                                </a>
                                <a class="btn btn-sm btn-dark" href="#" data-bs-toggle="tooltip" data-bs-placement="top" title="@lang('controls::lang.print')">
                                    <i data-feather='printer'></i>
                                </a>
                                <a class="btn btn-sm btn-warning" href="#" data-bs-toggle="tooltip" data-bs-placement="top" title="@lang('controls::lang.edit')">
                                    <i data-feather='edit'></i>
                                </a>
                            </div>

                            <livewire:controls::buttons :status="$control->status" :detail_id="$control->id"/>

                        </div>
                    </div>
                </div>
                {{-- Действия --}}
            </div>
        </section>
    </div>
</div>