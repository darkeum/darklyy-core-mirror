@extends('price::manager.body')

@section('title', __('price::settings.account.title'))

@section('content')

    <section>
        <div class="row" id="basic-table">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">@lang('price::settings.account.list')</h4>
                    </div>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                            <tr>
                                <th style="width: 15px" title="@lang('price::settings.account.lk')">@lang('price::settings.account.lk.short')</th>
                                <th>@lang('price::settings.account.site')</th>
                                <th>@lang('price::settings.account.field')</th>
                                <th>@lang('price::settings.account.field.translate')</th>
                                <th>@lang('price::settings.account.login')</th>
                                <th>@lang('price::settings.account.password')</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($list as $item)
                                <tr>
                                    <td>
                                        @if ($item['pa'])
                                            <i class="fas fa-check-circle text-success"></i>
                                        @endif
                                    </td>
                                    <td>{{ $item['key'] }}</td>
                                    <td>{{ $item['search_field'] }}</td>
                                    <td>{{ $item['search_field_translate'] }}</td>
                                    <td>
                                        <span data-pk="{{ $item['class_name'] }}" data-bs-toggle="dark-editable" data-value="{{ $item['login'] }}" data-url="{{ route('admin.price.settings.account.login') }}"
                                              data-emptytext="@lang('price::settings.account.empty')"></span>
                                    </td>
                                    <td>
                                        <span data-pk="{{ $item['class_name'] }}" data-bs-toggle="dark-editable" data-value="{{ $item['password'] }}" data-url="{{ route('admin.price.settings.account.password') }}"
                                              data-emptytext="@lang('price::settings.account.empty')"></span>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection