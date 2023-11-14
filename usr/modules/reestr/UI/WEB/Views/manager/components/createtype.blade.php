<div class="modal fade" id="addNewType" tabindex="-1" aria-labelledby="addNewTypeTitle" aria-modal="true" role="dialog">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-transparent">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-3 pt-1">
                <h1 class="text-center mb-1" id="addNewTypeTitle">Создание типа договора</h1>
                <!-- form -->
                <form id="addNewTypeValidation" class="" onsubmit="return false">

                    <div class="col-12">
                        <div class="mb-1">
                            <label class="form-label" for="orgModalType">Организация</label>
                            <select wire:model.defer='ModalOrg' class="form-select" id="orgModalType" name='orgModalType'>
                                @foreach($orgs as $org)
                                    <option value="{{$org->id}}">{{$org->organization->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row">

                        <div class="col-6">
                            <div class="mb-1">
                                <label class="form-label" for="ModalType">Тип договора</label>
                                <input wire:model.defer="ModalType" class="form-control" id="ModalType" placeholder="Введите тип договора" required>
                            </div>
                        </div>

                        <div class="col-6">
                            <div class="mb-1">
                                <label class="form-label" for="ModalPrefix">Префик</label>
                                <input wire:model.defer="ModalPrefix" class="form-control" id="ModalPrefix" placeholder="Введите префикc" required>
                            </div>
                        </div>

                    </div>

                    <div class="col-12 d-flex justify-content-between">
                        <button type="reset" class="btn btn-outline-secondary mt-1 waves-effect" data-bs-dismiss="modal" aria-label="Close">
                            Отмена
                        </button>
                        <button type="submit" wire:click="createType()" class="btn btn-primary mt-1 waves-effect waves-float waves-light" data-bs-dismiss="modal" aria-label="Close">Создать</button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')

    <script>

        Livewire.on('createType', event => {

            @if(request()->routeIs('admin.reestr.types'))
                location.reload();
            @endif

            Swal.fire({
                position: 'bottom-end',
                icon: 'success',
                title: 'Тип создан',
                showConfirmButton: false,
                timer: 1500,
                toast: true
            })
        });

    </script>

@endpush