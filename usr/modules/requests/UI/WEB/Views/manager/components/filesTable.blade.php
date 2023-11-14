<div class="">
    {{--<hr class="invoice-spacing mb-0"/>--}}
    <div class="table-responsive" >
        <table class="table table-striped table-sm br-3">
            <thead>
                
            <tr>
                <th class="col-8">Приложенные файлы</th>
                <th class="text-center">Размер</th>
                <th class="text-center">
                    <i class="feather-settings"></i>
                </th>
            </tr>
            </thead>

            <tbody>
            @if($files->isNotEmpty())
                @foreach ($files as $file)

                    <tr>
                        <td class="py-1">
                            <a class="card-text fw-bold"
                            @if($file->size ?? false) href="{{ url('download', $file->id) }}" target="_blank"
                            title="Открыть файл" @endif
                            >
                                {{ $file->name }}
                            </a>
                        </td>

                        <td class="text-center">
                            <span class="fw-bold">
                                @if($file->size ?? false)
                                    {{ $file->size }}
                                @else
                                    <i class="feather-alert-octagon text-danger fs-4" title="Файл недоступен"></i>
                                @endif
                            </span>
                        </td>

                        <td class="text-center">
                            @if($file->size ?? false)
                                <div class="btn-group" role="group" aria-label="Действия с файлом">
                                    {{-- Скачать файл --}}
                                    <a class="btn btn-sm btn-success"
                                    href="{{ url('download', $file->id) }}"
                                    title="Скачать файл"
                                    download
                                    >
                                        <i class="feather-download"></i>
                                    </a>
                                    {{-- Скачать файл --}}

                                
                                    {{-- Удалить файл --}}
                                    @if($unit->status !=3)
                                        <a class="btn btn-sm btn-primary"
                                        wire:click="deleteFile({{ $file->id }})"
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
                        </td>

                    </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="3" class="text-center p-0">
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
                            <button id="btnAddFile" class="btn-light btn w-100 m-0 border-top-0">Добавить файл <i class="fa-xl fa-light fa-cloud-arrow-down"></i></button>
                            <input wire:change="$emit('fileChoosen')" type="file" id="file" style="display: none">
                        </td>    
                    </tr>
                </tfoot>
            @endif
        </table>
    </div>
</div>

@push('scripts')
    <script data-title="Module - Requests">

        $('#btnAddFile').on('click', function() {
            $('#file').trigger('click');
        });

        window.Livewire.on('fileChoosen', postId => {
            let file = $("#file").prop('files')[0];
  
            let reader = new FileReader();
            reader.onloadend = ()=>{
                window.Livewire.emit('fileUpload', reader.result, file['name']);
            }
            reader.readAsDataURL(file);

        });


        window.addEventListener('deleteFile', function (e) {
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
                    Livewire.emit("deleteFile", fid, 1);
                }
            });
        });

    </script>
@endpush