<form wire:submit.prevent="load" method="post" enctype="multipart/form-data">
    <div class="modal-dialog modal-dialog-centered">
        {{-- preloader --}}
        @include('elements.preloader', ['class' => 'load-preloader-modal d-none'])
        {{-- preloader --}}
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="load-price-label">
                    @lang('price::show.deactivate.title')
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="">@lang('price::show.load.cid')</label>
                    <input type="text" class="form-control" wire:model="cid" placeholder="@lang('price::show.load.cid.placeholder')">
                </div>
                <div class="form-group">
                    <label for="">@lang('price::show.load.price')</label>
                    <input type="text" class="form-control" wire:model="price" placeholder="@lang('price::show.load.price.placeholder')" required>
                </div>
                <div class="form-group">
                    <label for="">@lang('price::show.load.start')</label>
                    <input type="text" class="form-control" wire:model="start" placeholder="@lang('price::show.load.start.placeholder')" required>
                </div>
                <span class="error"></span>
                <div class="form-group">
                    <label for="">@lang('price::show.load.file')</label>
                    <input type="file" class="form-control  @error('file') is-invalid @enderror" wire:model="file" required>
                    @error('file')
                        <div id="validationFile" class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">@lang('price::show.load.upload')</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">@lang('price::show.load.close')</button>
            </div>
        </div>
    </div>
</form>
@push('scripts')
    <script>
        window.livewire.on('modalBlock', () => {
            $('.load-preloader-modal').removeClass('d-none');
        });
        window.livewire.on('modalClose', () => {
            $('.load-preloader-modal').addClass('d-none');
            $('#load-price').modal('hide')
        });
    </script>
@endpush
