<div class="">
    @include('elements.preloader', ['wire' => true])
    <div class="table-table-responsive">
        <table class="table table-sm">
            <thead>
                <tr>
                    <th class="col-8">Прочие файлы</th>
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
                                @if($file->size ?? false) href="{{ url('download', $file->attach->id) }}" target="_blank"
                                title="Открыть файл" @endif
                                >
                                    {{ $file->attach->name }}
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
                                        href="{{ url('download', $file->attach->id) }}"
                                        title="Скачать файл"
                                        download
                                        >
                                            <i class="feather-download"></i>
                                        </a>
                                        {{-- Скачать файл --}}

                                    
                                        {{-- Удалить файл --}}
                                        <a class="btn btn-sm btn-primary"
                                        wire:click="deleteDraw({{ $file->attach->id }})"
                                        title="Удалить файл"
                                        >
                                            <i class="feather-trash"></i>
                                        </a>
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

            <tfoot>
                <tr>
                    <td colspan="3" class="px-0 py-0 ">
                        <button id="addDraw" class="btn-light btn w-100 m-0 border-top-0">Добавить файл <i class="fa-xl fa-light fa-cloud-arrow-down"></i></button>
                        <input wire:change="$emit('newDraw')" type="file" id="fileDraw" style="display: none">
                    </td>    
                </tr>
            </tfoot>


            </tbody>
        </table>
    </div>
</div>

@push('scripts')
    <script data-title="Module - Requests">

        $('#addDraw').on('click', function() {
            $('#fileDraw').trigger('click');
        });

        window.Livewire.on('newDraw', postId => {
            let file = $("#fileDraw").prop('files')[0];
  
            let reader = new FileReader();
            reader.onloadend = ()=>{
                window.Livewire.emit('uploadDraw', reader.result, file['name']);
            }
            reader.readAsDataURL(file);

        });


        window.addEventListener('deleteDraw', function (e) {
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
                    Livewire.emit("deleteDraw", fid, 1);
                }
            });
        });

        window.addEventListener('drawSwal', function (e) {
            Swal.fire({
                position: 'bottom-end',
                icon: 'success',
                title: e.detail.message,
                showConfirmButton: false,
                timer: 1000,
                toast: true
            })
        });

    </script>
@endpush