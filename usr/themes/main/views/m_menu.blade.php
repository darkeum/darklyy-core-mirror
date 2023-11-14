{{-- Управление

menu_view - Меню Показано/Скрыто (по умолчанию false)

--}}

<div class="header-bottom noselect">
    <div class="container container-block">
        <div class="row">

            {{-- Меню - Левое --}}
            @include('menu.left')
            {{-- Меню - Левое --}}

            {{-- Меню - Горизонтальное --}}
            @include('menu.center')
            {{-- Меню - Горизонтальное --}}

        </div>
    </div>
</div>

<script type="application/javascript">

    $('.vertical-megamenu').find('ul').each(function () {
        let txt = $("li", this).text();
        if (txt.length <= 0) {
            $(this).hide().parent().addClass('no_after');
        }
    });

    let left_m = $('#left_menu');
    $(left_m).on("click", function () {
        $(this).toggleClass('mega-left-title-yes-home');
    });

</script>