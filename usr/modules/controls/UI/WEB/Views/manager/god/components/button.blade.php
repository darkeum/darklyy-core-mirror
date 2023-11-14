<div class="d-flex justify-content-center mb-2">
    <button wire:click="toggleMinus"
            class="btn @if($toogle) btn-danger @else btn-success @endif fs-1 waves-effect waves-float waves-light p-5 w-100"
    >
        @if($toogle) Разрешить  @else Запретить  @endif отгрузку в минус
    </button>
</div>
