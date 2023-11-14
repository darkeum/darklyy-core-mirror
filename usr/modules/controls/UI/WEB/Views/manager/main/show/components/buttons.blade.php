<div>
    @if($status != 2)
            @include('elements.preloader', ['wire' => true])

                @if($status == 1)
                    <button class="btn btn-success w-100 btn-download-invoice mb-75" onclick="show.showApprove()">
                        @lang('controls::lang.pay.approve')
                    </button>
                    <button class="btn btn-success w-100 btn-download-invoice mb-75" onclick="show.showRevision()">
                        @lang('controls::lang.pay.send_revision')
                    </button>
                @endif

                @if($status == 3)
                    <button class="btn btn-danger w-100 btn-download-invoice mb-75" onclick="show.showCancel()">
                        @lang('controls::lang.pay.cancel_approve')
                    </button>
                @endif

            <script>
                window.addEventListener('swal:modal', event => {
                    Swal.fire({
                        title: event.detail.message,
                        icon: event.detail.type,
                    });
                });
            </script>
    @endif
</div>





