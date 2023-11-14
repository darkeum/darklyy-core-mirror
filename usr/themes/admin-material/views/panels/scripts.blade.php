{{-- BEGIN: Vendor JS --}}
<script src="{{ asset(mix('vendors/js/vendors.min.js')) }}"></script>
{{-- BEGIN Vendor JS --}}

{{-- BEGIN: Page Vendor JS --}}
<script src="{{ asset(mix('vendors/js/ui/jquery.sticky.js')) }}"></script>
@yield('vendor-script')
{{-- END: Page Vendor JS --}}

{{-- Наши скрипты --}}
<script src="{{ asset('js/scripts/ui/ui-creator.js') }}"></script>
<script src="{{ asset('js/scripts/custom/store2.min.js') }}"></script>
{{--\ Наши скрипты --}}

{{-- BEGIN: Theme JS--}}
<script src="{{ asset(mix('js/core/app-menu.js')) }}"></script>
<script src="{{ asset(mix('js/core/app.js')) }}"></script>

{{-- custom scripts file for user --}}
<script src="{{ asset(mix('js/core/scripts.js')) }}"></script>


@if($configData['blankPage'] === false)
<script src="{{ asset(mix('js/scripts/customizer.js')) }}"></script>
@endif
{{-- END: Theme JS --}}

<script src="{{ $manager_theme }}/assets/js/jquery-migrate-301.min.js"></script>
<script src="{{ $manager_theme }}/assets/js/jquery-ui.js"></script>
<script src="/usr/plugins/js/js.cookie.js"></script>
<script src="/usr/tpl/admin/assets/js/sweetalert2.all.min.js"></script>
 <script src="{{ asset(mix('vendors/js/dark-editable/dark-editable.iife.js')) }}"></script>

{{-- BEGIN: Page JS --}}
@yield('page-script')
{{-- END: Page JS --}}

{{-- Обновление иконок feather --}}
<script type="text/javascript">
    $(window).on('load', function () {
        if (feather) {
            feather.replace({
                width: 14,
                height: 14
            });
        }
    })
</script>
{{-- Обновление иконок feather --}}
