<div class="ms-1"> 
    @switch($status)
        @case(1)
        <span class='badge badge-light-warning' title='Ожидает исполнителя'>
                Ожидает исполнителя
            </span>
        @break
        @case(2)
        <span class='badge badge-light-info' title='Ожидает выполнения заявки'>
                Ожидает выполнения заявки
            </span>
        @break
        @case(3)
        <span class='badge badge-light-success' title='Заявка выполнена'>
                Заявка выполнена
            </span>
        @break
    @endswitch
</div>