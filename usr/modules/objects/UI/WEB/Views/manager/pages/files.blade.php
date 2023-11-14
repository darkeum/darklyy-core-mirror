<div class="card-header">
    <h4 class="card-title">Файлы по объекту</h4>
</div>

<div class="card-body">

    <div class="table-responsive">
        <table class="table">
            <thead>
            <tr>
                <th class="col-1 text-center">
                    <i class="feather-hash"></i>
                </th>
                <th>Файл</th>
                <th class="col-1 text-center">Тип</th>
                <th class="col-1 text-center">
                    <i class="feather-settings"></i>
                </th>
            </tr>
            </thead>
            <tbody>

            {{--                @forelse($object->files as $file)--}}
            @forelse($files as $file)
                <tr>
                    {{-- ID --}}
                    <td class="">{{ $file->id }}</td>
                    {{-- ID --}}

                    {{-- Заказ --}}
                    <td>
                        <div>
                            {{--<a href="{{ route('admin.sale', $file->id) }}" --}}
                            <a href="#"
                               target="_blank" class="fw-bolder">
                                {{ $file->numb }}
                            </a>
                            <div class="font-small-3">
                                {{ $file->title ? $file->title : 'Не указан' }}
                            </div>
                        </div>
                    </td>
                    {{-- Заказ --}}

                    {{-- Тип --}}
                    <td class="text-center">
                            <span class="badge rounded-pill bg-secondary">
                                {{ $files_types[$file->sales_type] }}
                            </span>
                    </td>
                    {{-- Тип --}}

                    {{-- Дейсвтия --}}
                    <td class="text-center">
                        <div class="dropdown">
                            <button type="button" class="btn btn-sm dropdown-toggle hide-arrow py-0"
                                    data-bs-toggle="dropdown">
                                <i data-feather="more-vertical"></i>
                            </button>
                            <div class="dropdown-menu dropdown-menu-end">
                                <a class="dropdown-item" href="#">
                                    <i data-feather="edit-2" class="me-50"></i>
                                    <span>Edit</span>
                                </a>
                                <a class="dropdown-item" href="#">
                                    <i data-feather="trash" class="me-50"></i>
                                    <span>Delete</span>
                                </a>
                            </div>
                        </div>
                    </td>
                    {{-- Дейсвтия --}}

                </tr>
            @empty
                <td class="text-center bg-light-secondary" colspan="100%">
                    <div class="fw-bolder">Пусто</div>
                </td>
            @endforelse

            </tbody>
        </table>
    </div>

</div>

{{--@push('styles')--}}
{{--@endpush--}}

{{--@push('scripts')--}}
{{--@endpush--}}