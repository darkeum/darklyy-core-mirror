<div class="card">
    <div class="row">

        <div class="d-none d-lg-none d-xl-block col">
            <div class="d-flex align-items-end justify-content-center h-100">
                <img
                        src="{{ asset('images/illustration/an-notifications.svg') }}"
                        {{--src="{{ asset('images/illustration/notifications.svg') }}"--}}
                        class="img-fluid" alt="Image"
                        width="75"
                >
            </div>
        </div>

        <div class="col-xl-7">
            <div class="card-body text-sm-end text-center ps-sm-0">
                <button class="btn btn-primary mb-1" wire:click="$emit('triggerReadAll')" @if ($notificationsCount <= 0) disabled @endif>
                    @lang('notifications::admin.read.all.btn')
                </button>
                <p class="mb-0">
                    @if ($notificationsCount > 0)
                        @lang('notifications::admin.read.all.text', ['count' => $notificationsCount])
                    @else
                    @lang('notifications::admin.read.all.empty')
                        
                    @endif
                </p>
            </div>
        </div>

    </div>
</div>

@push('scripts')
    <script type="text/javascript">
        document.addEventListener('DOMContentLoaded', function() {
            @this.on('triggerReadAll', function() {
                Swal.fire({
                    title: '@lang('notifications::admin.read.all.sure')',
                    text: '@lang('notifications::admin.read.all.text', ['count' => $notificationsCount])',
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonText: '@lang('notifications::admin.read.all.read')',
                    cancelButtonText: '@lang('notifications::admin.read.all.cancel')',
                    preConfirm: function(result) {
                        if (result) {
                            return @this.call('read').then(() => {
                                Swal.fire({
                                    title: '@lang('notifications::admin.read.all.info')',
                                    text: '@lang('notifications::admin.read.all.success')',
                                    icon: "success",
                                });
                                $('#notifications_assigned').dataTable().api().ajax.reload();
                            });
                        }
                    }
                });
            });
        })
    </script>
@endpush