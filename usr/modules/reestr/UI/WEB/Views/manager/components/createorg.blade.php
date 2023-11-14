<div class="modal fade" id="addNewOrg" tabindex="-1" aria-labelledby="addNewOrgTitle" aria-modal="true" role="dialog">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-transparent">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-3 pt-1">
                <h1 class="text-center mb-1" id="addNewOrgTitle">Создание организации</h1>
                <!-- form -->
                <form id="addNewOrgValidation" class="" onsubmit="return false">
                    
                    <div class="col-12">
                        <div class="mb-1">
                            <label class="form-label" for="inn">ИНН</label>
                            <input wire:model.defer="inn" class="form-control" id="inn" placeholder="Введите ИНН" required>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="mb-1">
                            <label class="form-label" for="bik">БИК</label>
                            <input wire:model.defer="bik" class="form-control" id="bik" placeholder="Введите БИК" required>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="mb-1">
                            <label class="form-label" for="schet">Расчетный счет</label>
                            <input wire:model.defer="schet" class="form-control" id="schet" placeholder="Введите расчетный счет" required>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="mb-1">
                            <label class="form-label" for="phone">Контактный телефон</label>
                            <input wire:model.defer="phone" class="form-control" id="phone" placeholder="Введите контактный телефон" required>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="mb-1">
                            <label class="form-label" for="email">Почта для связи</label>
                            <input type='email' wire:model.defer="email" class="form-control" id="email" placeholder="Введите почту" required>
                        </div>
                    </div>

                    <div class="col-12 d-flex justify-content-between">
                        <button type="reset" class="btn btn-outline-secondary mt-1 waves-effect" data-bs-dismiss="modal" aria-label="Close">
                            Отмена
                        </button>
                        <button type="submit" wire:click="createOrg()" class="btn btn-primary mt-1 waves-effect waves-float waves-light" data-bs-dismiss="modal" aria-label="Close">Создать</button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')

    <script>

   
            
    </script>

@endpush