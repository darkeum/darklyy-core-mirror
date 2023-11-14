<div>
    @include('elements.preloader', ['wire' => true])
    @if($selectedForm == null)

    @else
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h4>
                    @if($selectedForm->type == 2)
                        Спецификация №{{ $selectedForm->spec_numb }}
                        от {{$docform['date']}}
                    @else
                        {{ $typesForAdd[$selectedForm->type] }}
                    @endif
                </h4>
            </div>
            <div class="col-12">
                <div class="col-12">
                    <i class="fa-regular fa-file-word fa-xl" style="color: #4287ff;"></i>

                    @if($selectedForm->docFile->name ?? false)
                        {{ $selectedForm->docFile->name }}
                        @if($relations)
                            <i role='button' class="fas fa-redo-alt ml20 text-primary cp"
                                data-bs-toggle="modal"
                                data-bs-target="#reestr_spec_modal">
                            </i>
                        @endif
                        <p onclick="delFile('doc')" role="button" class="text-danger">
                            Удалить</p>
                    @else
                        <input wire:model="newDoc" type='file' class="form-input mb-1"
                                accept=".doc, .docx, .xls, .xlsx">
                    @endif
                </div>
                <div class="col-12">
                    <i class="fa-regular fa-file-pdf fa-xl" style="color: #ea331f;"></i>
                    @if($selectedForm->scanFile->name ?? false)
                        {{ $selectedForm->scanFile->name }}
                        <p onclick="delFile('scan')" role="button" class="text-danger">
                            Удалить</p>
                    @else
                        <input wire:model='newScan' type='file' class="form-input mb-1"
                                accept=".pdf">

                    @endif
                </div>
            </div>

            <div class="col-12">

                <h5>Статус</h5>
                <select wire:model="docform.status" class="form-select" name='status'
                        onchange="statusUpdate()">
                    <option value='0' @selected($selectedForm->status == 0)>Документ
                        добавлен
                    </option>
                    <option value='1' @selected($selectedForm->status == 1)>В архиве
                    </option>
                    <option value='2' @selected($selectedForm->status == 2)>На руках
                    </option>
                </select>

                <div class="mt-1" @if($docform['status'] != 2) style="display: none" @endif>
                    <h5>Забрал</h5>
                    <select class="form-select select2" id="uidForm">
                        @if($selectedForm->uid_took != null)
                            <option value="{{ $selectedForm->uid_took }}">{{ shortUserName($selectedForm->uid_took) }}</option>
                        @endif
                    </select>
                </div>

                <h5 class="mt-1">Дата</h5>
                <input wire:model.debounce.500ms="docform.date" placeholder="Выберите дату"
                        class="form-control flatpickr" type="text">
            </div>

            <div class="col-12 mt-1">
                <h5>Комментарий</h5>
                <textarea wire:model.debounce.500ms="docform.comment" rows="5"
                            class="form-control"></textarea>
            </div>

            <div title="Подпись документа" class="form-check form-switch form-check-success mt-1">
                <div>
                    <input wire:click="signDoc()" type="checkbox" class="form-check-input" id="customSwitch111" @if($selectedForm->sign == 1) checked="" @endif>
                </div>
                <div class="position-absolute top-50 translate-middle-y">
                    Подпись
                </div>
            </div>

        </div>
    @endif
</div>

@push('scripts')
    <script>
         Livewire.on('reloadFieldsModal', event => {
            $('#uidForm').on('select2:select', function (e) {
            @this.
            set('docform.uid_took', $("#uidForm").val());
            });
        });

    </script>
@endpush