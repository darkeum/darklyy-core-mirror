@extends('inventory::manager.body')

@section('title', __('inventory::general.list'))

@section('content')
    <grid:inventory::inventorygrid />
    <div class="modal fade" id="exampleModalCenter" tabindex="-1" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalCenterTitle">@lang('inventory::general.check.modal')</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <livewire:inventory::check />
            </div>
        </div>
    </div>
@endsection
