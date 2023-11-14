@extends('developers::manager.body')

{{--@section('title', __('changelog::lang.edit'))--}}

@section('content')

    <div class="card">
        <div class="card-header">
            <h5 class="card-title">Список роутов приложения</h5>
        </div>
        <div class="card-body">
            <div class="row row-cards row-deck">

                <div class="col-12 mb-3">
                    <div class="row">

                        <div class="col-md-12">
                            <div class="input-group">
                                <input type="text"
                                       id="search_route"
                                       class="form-control form-control-sm"
                                       placeholder="Найти роут"
                                       name="search_route"
                                >
                            </div>
                        </div>


                    </div>
                </div>

                <div class="col-12">
                    <div class="card">
                        <div class="table-responsive">
                            <table class="table table-hover table-outline table-vcenter text-nowrap card-table">

                                <thead>

                                <th class="text-center w-1">
                                    <i class="feather-hash"></i>
                                </th>
                                <th class="text-center">HTTP Method</th>
                                <th class="text-center">Route</th>
                                <th class="text-center">Corresponding Action</th>
                                {{--<th class="text-center">ii</th>--}}

                                </thead>

                                <tbody id="routes">
                                @forelse ($routes  as $key => $route)

                                    <tr data-methods="{{ $route->methods()[0] }}"
                                        data-uri="{{ $route->uri() }}"
                                        data-ActionName="{{ $route->getActionName() }}"
                                    >

                                        <td class="text-center text-muted-dark">
                                            <div class="">
                                                {{ $key + 1 }}
                                            </div>
                                        </td>

                                        <td class="text-center text-muted-dark">
                                            <div class="fs-4">
                                                {{ $route->methods()[0] }}
                                            </div>
                                        </td>

                                        <td class="text-center">
                                            {{ $route->uri() }}
                                            <br>
                                            {{ $route->getName() }}
                                        </td>

                                        <td class="text-center">
                                            {{ $route->getActionName() }}
                                        </td>

                                        {{--<td class="text-center">--}}
                                        {{--    {{ $route->getActionMethod() }}--}}
                                        {{--</td>--}}

                                    </tr>
                                @empty
{{--                                    @include('admin.layouts.table.list.empty')--}}
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection

@push('scripts')

    <script src="{{ asset('js/scripts/custom/simpleSort.js') }}"></script>

    <script>

        let sort = new simpleSort('#routes', 'tr');

        // Фильтры
        $('#search_route').on('propertychange change keyup paste input mouseup',function() {

            sort.filter('data-methods', $(this).val());
            sort.filter('data-uri', $(this).val());
            sort.filter('data-ActionName', $(this).val());

        });

        // $(document).ready(function () {});

    </script>
@endpush