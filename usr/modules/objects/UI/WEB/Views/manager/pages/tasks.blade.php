<div class="card-header">
    <h4 class="card-title">Задачи по объекту</h4>
</div>

<div class="card-body">

    <div class="table-responsive">
        <table class="table">
            <thead>
            <tr>
                <th class="col-1 text-center">
                    <i class="feather-hash"></i>
                </th>
                <th>Задача</th>
                <th class="text-center">
                    Поручение
                </th>
                <th class="col-1 text-center">Статус</th>
                <th class="col-1 text-center">
                    <i class="feather-settings"></i>
                </th>
            </tr>
            </thead>
            <tbody>

            @forelse($object->tasks as $task)
                <tr>
                    {{-- ID --}}
                    <td class="">{{ $task->id }}</td>
                    {{-- ID --}}

                    {{-- Задача --}}
                    <td>
                        <div>
                            {{--<a href="{{ route('admin.task', $task->id) }}" --}}
                            <a href="#"
                               target="_blank" class="fw-bolder">
                                {{ $task->task ? $task->task : 'Не указана' }}
                            </a>
                            <div class="font-small-3">
                                {{ $task->title ? $task->title : 'Не указан' }}
                            </div>
                            <div class="font-small-2 mt-50 d-flex align-items-center">
                                    <span class="badge bg-dark">
                                        {{ shortUserName($task->fromid) }}
                                    </span>

                                <i class="feather-arrow-right mx-50"></i>

                                <span class="badge badge-light-dark">
                                        {{ shortUserName($task->toid) }}
                                    </span>
                            </div>
                        </div>
                    </td>
                    {{-- Задача --}}

                    {{-- Поручение --}}
                    <td class="text-center">
                        <div class="d-flex justify-content-center align-items-center cursor-pointer"
                             data-bs-toggle="tooltip"
                             data-popup="tooltip-custom"
                             data-bs-placement="top"
                             data-bs-custom-class="tooltip-tasker"
                             title="От {{ shortUserName($task->fromid) }} для {{ shortUserName($task->toid) }}"
                        >
                            <div class="avatar-wrapper">
                                <a href="#" class="avatar me-50 br-3"
                                   target="_blank"
                                   data-bs-toggle="tooltip"
                                   data-popup="tooltip-custom"
                                   data-bs-placement="left" title="{{ shortUserName($task->fromid) }}"
                                >
                                    <img class="br-3" src="{{ asset(\Boot\Models\Users::find($task->fromid)->avatar) }}" alt="Avatar"
                                         width="32"
                                         height="32">
                                </a>
                            </div>
                            <div class="d-flex flex-column">
                                <h6 class="d-flex user-name text-truncate mb-0">
                                    <i class="feather-arrow-right"></i>
                                </h6>
                            </div>
                            <div class="avatar-wrapper">
                                <a href="#" class="avatar ms-50 br-3"
                                   target="_blank"
                                   data-bs-toggle="tooltip"
                                   data-popup="tooltip-custom"
                                   data-bs-placement="right" title="{{ shortUserName($task->toid) }}"
                                >
                                    <img class="br-3" src="{{ asset(\Boot\Models\Users::find($task->toid)->avatar) }}" alt="Avatar"
                                         width="32"
                                         height="32">
                                </a>
                            </div>
                        </div>
                    </td>
                    {{-- Поручение --}}

                    {{-- Статус --}}
                    <td class="text-center">
                            <span class="badge rounded-pill badge-light-secondary">
                                {{ $tasks['status'][$task->status] }}
                            </span>
                    </td>
                    {{-- Статус --}}

                    {{-- Действия --}}
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
                    {{-- Действия --}}

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

@push('styles')
    <style>
        .tooltip-tasker {}
        .tooltip-tasker .tooltip-inner {
            max-width: initial;
        }
    </style>
@endpush

{{--@push('scripts')--}}
{{--@endpush--}}