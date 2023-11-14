@extends("decrees::manager.body")

@section('title', __('decrees::admin.reestr'))

@section('content')
    
    <div id="content" class="card">
    
        <div class="card-header d-flex justify-content-between">            
                
            <h2> @lang('decrees::admin.show', ['numb' => $unit->numb]) </h2>

            <div class="">
                @if($unit->type !=3)
                    <a target="_blank" title='Скачать' href="{{ route('admin.decrees.doc_create', [$unit->id,'type'=>0]) }}" class="btn btn-info btn-sm"><i class="fa-solid fa-download"></i> Проект приказа</a>
                @else
                    <a target="_blank" title='Скачать' href="{{ route('admin.decrees.doc_create', [$unit->id,'type'=>1]) }}" class="btn btn-info btn-sm"><i class="fa-solid fa-download"></i> Проект приказа</a>
                    <a target="_blank" title='Скачать' href="{{ route('admin.decrees.doc_create', [$unit->id,'type'=>2]) }}" class="btn btn-info btn-sm"><i class="fa-solid fa-download"></i> Служебное задание</a>
                @endif
                @if($unit->scan_id != null) <a target="_blank" title='Скачать' href="{{ route('admin.decrees.getFile', [$unit->id,1]) }}" class="me-1 btn btn-info btn-sm"><i class="fa-solid fa-download"></i> Скан приказа</a> @endif
                <a href="{{ route('admin.decrees.edit', $unit->id) }}" class="btn btn-primary btn-sm">@lang('decrees::admin.edit')</a>
            </div>

        </div>


        <div class="card-body">
            <div class="row invoice-spacing">

                {{-- основная инфа --}}
                <div class="col-xxl-3 col-xl-6 col-md-6 col-lg-6 p-1">
                    <h5 class="mb-2">@lang('decrees::admin.show.main')</h5>
                    <table>
                        <tbody>
                            <tr>
                                <td class="pe-1"><strong>@lang('decrees::admin.numb')</strong></td>
                                <td>
                                    @if($unit->numb !=null)
                                        {{ $unit->numb }}
                                    @else 
                                        <span class="badge rounded-pill bg-danger">Отсутвует</span> 
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="pe-1"> <strong>@lang('decrees::admin.started_at')</strong></td>
                                <td>{{ date('d-m-Y', strtotime($unit->started_at)) }}</td>
                            </tr>
                            <tr>
                                <td class="pe-1"><strong>@lang('decrees::admin.ended_at')</strong></td>
                                <td>
                                    @if($unit->ended_at !=null)
                                        {{ date('d-m-Y', strtotime($unit->ended_at)) }}
                                    @else 
                                        <span class="badge rounded-pill bg-warning">Бессрочно</span> 
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="pe-1"><strong>@lang('decrees::admin.org')</strong></td>
                                <td>{{ getOrgInfo($unit->org, 'object')->organizations->name }}</td>
                            </tr>
                            <tr>
                                <td class="pe-1"><strong>@lang('decrees::admin.status')</strong></td>
                                <td>
                                    @if ($unit->status == 1)
                                        <span class="badge rounded-pill bg-success">@lang('decrees::admin.status.active')</span>
                                    @else
                                        <span class="badge rounded-pill bg-danger">@lang('decrees::admin.status.end')</span>
                                    @endif 
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                {{-- основная инфа --}}

                {{-- инфа по приказу --}}
                <div class="col-xxl-3 col-xl-6 col-md-6 col-lg-6 p-1">
                    <h5 class="mb-2">@lang('decrees::admin.show.sub')</h5>
                    <table>
                        <tbody>
                            <tr>
                                <td class="pe-1"> <strong>@lang('decrees::admin.type')</strong> </td>
                                <td>
                                    {{ $type }} {!! $type_icon !!}
                                </td>
                            </tr>
                            <tr>
                                <td class="pe-1">
                                    <strong>
                                        @if ($unit->type == 3)
                                            @lang('decrees::admin.workers')
                                        @else
                                            @lang('decrees::admin.worker')
                                        @endif
                                    </strong>
                                </td>
                                <td>
                                    @if (!empty($unit->worker))
                                        @foreach ($unit->worker as $w)
                                            {{ fullUserName($w) }}
                                        @endforeach
                                    @else
                                        <span class="badge rounded-pill bg-danger">@lang('decrees::admin.null')</span>
                                    @endif  
                                </td>
                            </tr>
                            @if($unit->comment != null)
                                <tr>
                                    <td class="pe-1">
                                        <strong>
                                        Комментарий к приказу
                                        </strong>
                                    </td>
                                    <td>
                                        {!! $unit->comment !!}
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
                {{-- инфа по приказу --}}

                {{-- командировка --}}
                @if( $unit->type == 3)
                    <div class="col-xxl-3 col-xl-6 col-md-6 col-lg-6 p-1">
                        <h5 class="mb-2">@lang('decrees::admin.show.command')</h5>
                        <table>
                            <tbody>
                                <tr>
                                    <td class="pe-1"> <strong>@lang('decrees::admin.post')</strong> </td>
                                    <td>
                                        @if($unit->data['post'] ?? false)
                                            {{ $unit->data['post'] }} 
                                        @else
                                            <span class="badge rounded-pill bg-danger">@lang('decrees::admin.null')</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td class="pe-1">
                                        <strong>@lang('decrees::admin.target')</strong>
                                    </td>
                                    <td>
                                        @if($unit->data['target'] ?? false)
                                            {{ $unit->data['target'] }} 
                                        @else
                                            <span class="badge rounded-pill bg-danger">@lang('decrees::admin.null')</span>
                                        @endif
                                    </td>
                                </tr>
                                
                                <tr>
                                    <td class="pe-1">
                                        <strong>@lang('decrees::admin.date_transit')</strong>
                                    </td>
                                    <td>
                                        @if($unit->data['date_transit'] ?? false)
                                            {{ $unit->data['date_transit'] }} 
                                        @else
                                            <span class="badge rounded-pill bg-danger">@lang('decrees::admin.null')</span>
                                        @endif
                                    </td>
                                </tr>
                                
                            </tbody>
                        </table>

                        <table >
                            <thead>
                                <tr>
                                    <th colspan="2" class="py-1 pe-1">Результат</th>
                                    <th class="py-1 pe-1">Организация</th>
                                    <th class="py-1 pe-1">Город</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($unit->data['destination'] as $key => $item)
                                    <tr class="border-bottom">
                                        <td colspan="2" class="pe-1">
                                            {{ $item['short_report'] ?? 'Нет информации' }}
                                        </td>
                                        <td class="text-center">
                                            {!! $item['org'] !!}
                                        </td>
                                        <td class="text-center">
                                            {{ $item['city'] }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        
                    </div>
                @endif
                {{-- командировка --}}

                {{-- инфа служебная --}}
                <div class="col-xxl-3 col-xl-6 col-md-6 col-lg-6 p-1">
                    <h5 class="mb-2">@lang('decrees::admin.show.system')</h5>
                    <table>
                        <tbody>
                            <tr>
                                <td class="pe-1"> <strong>@lang('decrees::admin.uid')</strong> </td>
                                <td>
                                    {{ fullUserName($unit->uid) }}
                                </td>
                            </tr>
                            <tr>
                                <td class="pe-1">
                                    <strong>
                                        <strong>@lang('decrees::admin.created_at')</strong>
                                    </strong>
                                </td>
                                <td>
                                    {{ date('d-m-Y', strtotime($unit->created_at)) }}
                                </td>
                            </tr>
                            
                            <tr>
                                <td class="pe-1">
                                    <strong>@lang('decrees::admin.updated_at')</strong>
                                </td>
                                <td>
                                    {{ date('d-m-Y', strtotime($unit->updated_at)) }}
                                </td>
                            </tr>
                            
                        </tbody>
                    </table>
                    
                </div>
                {{-- инфа служебная --}}
                
            </div>
          
        </div>
        
    </div>
@endsection

@push('scripts')
    <script>
        @if (\Session::has('message'))
            {
                Swal.fire({
                    icon: 'success',
                    title: 'Успешно',
                    text: '{!! \Session::get('message') !!}',
                    timer: 1500,
                })
            }
        @endif
    </script>   
@endpush