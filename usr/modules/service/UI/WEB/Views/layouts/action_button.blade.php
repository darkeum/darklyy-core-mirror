<div class="text-center mt-xl-5">

    @if($cert_url ?? true or $cert_route ?? false)
        <a href="@if($cert_route ?? false) {{ route($cert_route) }} @else {{ url($cert_url ?? 'certificates/sertifikaty-servisa') }} @endif"
           class="btn btn-primary btn-lg mb-0">
            @lang('service::lang.certs')
        </a>
    @endif

    {{-- Старый дизайн кнопки btn-a-action --}}
    <a href="@if($route ?? false) {{ route($route) }} @else {{ url($url) }} @endif"
       class="btn btn-danger {{--btn-a-action--}} btn-lg mb-0">
        @lang('service::lang.submit_app')
    </a>

</div>