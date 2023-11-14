<div class="mt-5">
    <div class="table-table-responsive">
        <table class="table table-sm">
            <thead>
                
            <tr>
                <th class="">Файлы заявки</th>
                <th class="text-center">Размер</th>
                <th class="text-center">
                    <i class="feather-settings"></i>
                </th>
            </tr>
            </thead>
            <tbody>
            @if($files->isNotEmpty())

                @foreach ($files as $file)

                    <tr class="" id="file_{{$file->id}}">
                        <td>
                            <a class="card-text fw-bold"                            
                            @if($file->size ?? false) href="{{ url('download', $file->id) }}" target="_blank"
                            title="Открыть файл" @endif
                            >
                                {{ $file->name }}
                            </a>
                        </td>

                   
                        <td >
                            <span class="fw-bold">
                                {{ ($file->size ?? false) ? $file->size : 'none' }}
                            </span>
                        </td>

                        <td class="text-center">
                            @if($file->size ?? false)
                                <div class="btn-group" role="group" aria-label="Действия с файлом">

                                    
                                    {{-- Финальный файл --}}
                                    @if($unit->aid == null)
                                        <a class="btn btn-sm btn-success"
                                        wire:click="finalFileRight({{ $file->id }})"
                                        title="Подтвердить финальный файл"
                                        download
                                        >
                                            <i class="fa-solid fa-check"></i>
                                        </a>
                                    @endif
                                    {{-- Финальный файл --}}

                                
                                    {{-- Удалить файл --}}
                                    @if($unit->status !=3)
                                        <a class="btn btn-sm btn-primary"
                                        wire:click="deleteFileRight({{ $file->id }})"
                                        title="Удалить файл"
                                        >
                                            <i class="feather-trash"></i>
                                        </a>
                                    @endif
                                    {{-- Удалить файл --}}
                                </div>
                            @else
                                <span class="badge bg-danger">
                                    Файл недоступен
                                </span>
                            @endif
                    </tr>
                @endforeach
            @else
            <tr>
                <td  class="text-center p-0" colspan="3">
                    <div class="profile-user-info p-1 w-100 text-center badge rounded-0">
                        <h6 class="mb-0">Отсутвуют</h6>
                    </div>
                </td>   
            </tr>
            @endif

            </tbody>
            @if($unit->status !=3)
                <tfoot>
                    <tr>
                        <td colspan="3" class="px-0 py-0 ">
                            <button id="btnAddRightFile" class="btn-light btn w-100 m-0 border-top-0">Добавить файл <i class="fa-xl fa-light fa-cloud-arrow-down"></i></button>
                            <input wire:change="$emit('fileChoosenRight')" type="file" id="fileRight" style="display: none">
                        </td>    
                    </tr>
                </tfoot>
            @endif
        </table>
    </div>
</div>

@push('scripts')
    <script data-title="Module - Requests">

   
        
        $('#btnAddRightFile').on('click', function() {
            $('#fileRight').trigger('click');
        });

        window.Livewire.on('fileChoosenRight', postId => {
            let file = $("#fileRight").prop('files')[0];
  
            let reader = new FileReader();
            reader.onloadend = ()=>{
                window.Livewire.emit('fileUploadRight', reader.result, file['name']);
            }
            reader.readAsDataURL(file);

        });


        window.addEventListener('deleteFileRight', function (e) {
            let fid = e.detail.fid;
            Swal.fire({
                title: "ВНИМАНИЕ",
                html: "Удалить данный файл?",
                icon: 'warning',
                showCancelButton: true,
                cancelButtonText: 'Отмена',
                confirmButtonText: 'Продолжить',
            }).then((result) => {
                if (result.isConfirmed) {
                    Livewire.emit("deleteFileRight", fid, 1);
                }
            });
        });

        window.addEventListener('finalFileRight', function (e) {
            let fid = e.detail.fid;
            Swal.fire({
                title: "Подтвердить финальный файл?",
                html: "Данное действие завершит заявку",
                icon: 'question',
                showCancelButton: true,
                cancelButtonText: 'Отмена',
                confirmButtonText: 'Продолжить',
            }).then((result) => {
                if (result.isConfirmed) {
                    Livewire.emit("finalFileRight", fid, 1);
                }
            });
        });

    </script>
@endpush